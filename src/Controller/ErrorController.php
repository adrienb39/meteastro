<?php

namespace App\Controller;

use App\Entity\MenuConnect;
use App\Entity\MenuPrincipal;
use Doctrine\ORM\EntityManager;

class ErrorController extends AbstractController
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function error404(): void
    {
        $isConnected = isset($_SESSION['email']) && isset($_SESSION['password']);

        // --- Logique de mise à jour du profil ---
        if ($isConnected && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_update'])) {
            $newName = $_POST['name'];
            $newEmail = $_POST['email'];
            $newPass = $_POST['password'];
            $userId = $_SESSION['user_id'];

            if (!empty($newPass)) {
                $hashedPass = password_hash($newPass, PASSWORD_BCRYPT);
                $sql = "UPDATE `users` SET `name` = ?, `email` = ?, `password` = ? WHERE `id_users` = ?";
                $this->db->query2($sql, [$newName, $newEmail, $hashedPass, $userId]);
            } else {
                $sql = "UPDATE `users` SET `name` = ?, `email` = ? WHERE `id_users` = ?";
                $this->db->query2($sql, [$newName, $newEmail, $userId]);
            }

            session_destroy();
            header("Location: /connexion/login");
            exit();
        }

        $userName = $isConnected ? $_SESSION['name'] : '';
        $userEmail = $isConnected ? $_SESSION['email'] : '';

        $entityClass = $isConnected ? MenuConnect::class : MenuPrincipal::class;
        $menuItems = $this->entityManager
            ->getRepository($entityClass)
            ->findBy([], ['parent' => 'ASC', 'id' => 'ASC']);

        // Génération du menu HTML
        ob_start();
        self::renderBootstrapMenu($menuItems);
        $menuHtml = ob_get_clean();

        // On récupère la chaîne des musiques de l'article (ex: "music1.mp3,music2.mp3")
        $musicString = $article['music_file'] ?? '';
        $userPlaylist = [];

        if (!empty($musicString)) {
            $files = explode(',', $musicString);
            foreach ($files as $file) {
                $file = trim($file);
                if (!empty($file)) {
                    $userPlaylist[] = "../../uploads/" . $file;
                }
            }
        }
        $jsonPlaylist = json_encode($userPlaylist);

        // Statistiques et réglages
        $this->addUniqueIP();
        $nombreVisite = $this->getUniqueVisitor();
        $this->addUniqueIPMonthly();

        // Transmission des données à la méthode de rendu d'erreur
        $this->renderError(404, null, [
            'nombreVisite' => $nombreVisite,
            'isConnected' => $isConnected,
            'userName' => $userName,
            'userEmail' => $userEmail,
            'menuHtml' => $menuHtml,
            'jsonPlaylist' => $jsonPlaylist,
        ]);
    }

    public function renderBootstrapMenu(array $items, int $parentId = 0): void
    {
        foreach ($items as $item) {
            if ($item->getParent() == $parentId) {
                $hasChildren = false;
                foreach ($items as $sub) {
                    if ($sub->getParent() == $item->getId()) {
                        $hasChildren = true;
                        break;
                    }
                }
                if ($hasChildren) {
                    echo '<li class="nav-item dropdown">';
                    echo '<a class="nav-link dropdown-toggle ' . ($item->getClass() ?? '') . '" href="' . $item->getUrl() . '" role="button" data-bs-toggle="dropdown" aria-expanded="false">' . ucfirst($item->getMenuName()) . '</a>';
                    echo '<ul class="dropdown-menu shadow border-0 animate slideIn">';
                    self::renderBootstrapMenu($items, $item->getId());
                    echo '</ul></li>';
                } else {
                    $class = ($parentId == 0) ? 'nav-link' : 'dropdown-item';
                    echo '<li><a class="' . $class . ' ' . ($item->getClass() ?? '') . '" href="' . $item->getUrl() . '">' . ucfirst($item->getMenuName()) . '</a></li>';
                }
            }
        }
    }

    /**
     * Adds the unique IP to the iplist.txt file if it doesn't already exist.
     */
    private function addUniqueIP($ip = NULL)
    {
        // Get the IP address, falling back to the current request's IP if $ip is NULL
        $ip = ($ip !== NULL) ? trim($ip) : trim($this->getIP());

        // If we failed to get an IP, stop.
        if (empty($ip)) {
            return;
        }

        $filepath = __DIR__ . '/../../iplist.txt';

        // Check if the file exists and read its contents
        if (file_exists($filepath)) {
            $iplistContent = file_get_contents($filepath);
        } else {
            // Create the file if it doesn't exist
            $iplistContent = '';
        }

        // Split the content into an array of IPs
        $iplist = explode(",", $iplistContent);

        // Clean up the array by trimming whitespace
        $iplist = array_map('trim', $iplist);
        $iplist = array_filter($iplist); // Remove empty values

        // Only add the IP if it's not already in the list
        if (!in_array($ip, $iplist)) {
            // Use FILE_APPEND to safely append data
            // We use a newline character instead of a comma for safer file handling,
            // or ensure the comma is only added if the file isn't empty.
            if (!empty($iplistContent)) {
                // Prepend with a comma if the file wasn't empty
                $contentToWrite = "," . $ip;
            } else {
                // Write just the IP if the file was empty
                $contentToWrite = $ip;
            }

            // Use file_put_contents for a simpler and safer append operation
            file_put_contents($filepath, $contentToWrite, FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * Gets the total count of unique visitors from the iplist.txt file.
     * @return int The count of unique IPs.
     */
    private function getUniqueVisitor(): int
    {
        $filepath = __DIR__ . "/../../iplist.txt";

        // Check if the file exists before attempting to read it
        if (!file_exists($filepath)) {
            return 0;
        }

        $fileContent = file_get_contents($filepath);

        // Split by comma
        $iplist = explode(",", $fileContent);

        // Clean up and count (trim and remove empty elements)
        $iplist = array_map('trim', $iplist);
        $iplist = array_filter($iplist);

        // Return the count of unique IPs
        return count($iplist);
    }

    /**
     * Tries to get the real IP address of the user.
     * @return string|null The IP address or NULL if not found.
     */
    private function getIP(): ?string
    {
        // Use an array and a loop for cleaner IP retrieval logic
        $ip_keys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ip_keys as $key) {
            if (isset($_SERVER[$key])) {
                // HTTP_X_FORWARDED_FOR can contain a list of IPs (proxy chain)
                if ($key === 'HTTP_X_FORWARDED_FOR') {
                    // Take the first IP in the list (most likely the client IP)
                    $ip = trim(explode(',', $_SERVER[$key])[0]);
                } else {
                    $ip = trim($_SERVER[$key]);
                }
                // Perform basic IP validation before returning
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return NULL;
    }

    /**
     * Ajoute l'IP au journal mensuel si elle n'a pas encore été enregistrée ce mois-ci.
     * @param string|null $ip L'adresse IP à vérifier/enregistrer.
     */
    private function addUniqueIPMonthly($ip = NULL)
    {
        $ip = ($ip !== NULL) ? trim($ip) : trim($this->getIP());

        if (empty($ip)) {
            return;
        }

        $filepath = __DIR__ . '/../../iplist_date.txt';

        // Clé de l'enregistrement : [YYYY-MM-IP]
        $month_key = date('Y-m');
        $record = $month_key . "-" . $ip . "\n";

        // Lire le contenu existant
        if (file_exists($filepath)) {
            $monthlyListContent = file_get_contents($filepath);
        } else {
            $monthlyListContent = '';
        }

        // 1. Vérifie si le mois a changé (purge l'ancien fichier si nécessaire, ou pas)
        // Pour l'approche la plus simple (garder un historique), nous lisons simplement.

        // 2. Vérifie si l'IP est déjà enregistrée pour ce mois-ci
        if (strpos($monthlyListContent, $month_key . "-" . $ip) === false) {

            // L'IP n'a pas encore été enregistrée ce mois-ci, on l'ajoute.
            file_put_contents($filepath, $record, FILE_APPEND | LOCK_EX);
        }
    }
}