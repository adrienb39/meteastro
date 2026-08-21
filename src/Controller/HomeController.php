<?php

namespace App\Controller;

use App\Entity\Astronomie;
use App\Entity\MenuConnect;
use App\Entity\MenuPrincipal;
use App\Entity\Meteorologie;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class HomeController extends AbstractController
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function index(): void
    {
        session_start();
        // --- Authentification ---
        $isConnected = isset($_SESSION['email']) && isset($_SESSION['password']);

        // --- Redirection Newsletter ---
        if ($isConnected) {
            $userId = $_SESSION['user_id'] ?? null;

            /** @var User|null $user */
            $user = $userId ? $this->entityManager->getRepository(User::class)->find($userId) : null;
            $newsletterStatus = $user ? $user->getNewsletter() : null;

            // Si la valeur est strictement différente de 0 et de 1 (ex: null), on redirige
            if ($newsletterStatus !== 0 && $newsletterStatus !== 1) {
                $this->redirect('/newsletter/welcome');
                exit();
            }

            $_SESSION['newsletter'] = $newsletterStatus;
        }

        // --- Logique de mise à jour du profil ---
        if ($isConnected && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_update'])) {
            $newName = $_POST['name'] ?? '';
            $newEmail = $_POST['email'] ?? '';
            $newPass = $_POST['password'] ?? '';
            $isNewsletter = isset($_POST['newsletter']) ? (int) $_POST['newsletter'] : null;
            $userId = $_SESSION['user_id'] ?? null;

            if ($userId) {
                /** @var User|null $userToUpdate */
                $userToUpdate = $this->entityManager->getRepository(User::class)->find($userId);

                if ($userToUpdate) {
                    $userToUpdate->setName($newName);
                    $userToUpdate->setEmail($newEmail);
                    $userToUpdate->setNewsletter($isNewsletter);

                    if (!empty($newPass)) {
                        $hashedPass = password_hash($newPass, PASSWORD_BCRYPT);
                        $userToUpdate->setPassword($hashedPass);
                    }

                    $this->entityManager->flush();
                }
            }

            session_destroy();
            $this->redirect('/connexion/login.php');
            exit();
        }

        // Données utilisateur
        $userName = $isConnected ? ($_SESSION['name'] ?? '') : '';
        $userEmail = $isConnected ? ($_SESSION['email'] ?? '') : '';
        $userNewsletter = $isConnected ? ($_SESSION['newsletter'] ?? null) : null;

        // --- Génération du menu ---
        $entityClass = $isConnected ? MenuConnect::class : MenuPrincipal::class;
        $menuItems = $this->entityManager
            ->getRepository($entityClass)
            ->findBy([], ['parent' => 'ASC', 'id' => 'ASC']);

        ob_start();
        self::renderBootstrapMenu($menuItems);
        $menuHtml = ob_get_clean();

        // --- Playlist ---
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

        // --- Statistiques ---
        $this->addUniqueIP();
        $nombreVisite = $this->getUniqueVisitor();
        $this->addUniqueIPMonthly();

        // --- Articles & Contact ---
        $articlesAstronomie = $this->entityManager->getRepository(Astronomie::class)->findBy(['verified' => true]);
        $articlesMeteorologie = $this->entityManager->getRepository(Meteorologie::class)->findBy(['verified' => true]);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_signal'])) {
            $this->contactProcess();
        }

        // --- Rendu final ---
        $this->render('home/index', [
            'nombreVisite' => $nombreVisite,
            'isConnected' => $isConnected,
            'userName' => $userName,
            'userEmail' => $userEmail,
            'userNewsletter' => $userNewsletter,
            'menuHtml' => $menuHtml,
            'jsonPlaylist' => $jsonPlaylist,
            'articlesAstronomie' => $articlesAstronomie,
            'articlesMeteorologie' => $articlesMeteorologie,
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

    public function contactProcess(): void
    {
        // Fixe l'en-tête de réponse en JSON
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendJsonResponse('error', 'Méthode non autorisée.');
        }

        // 1. Récupération et Nettoyage des données
        $pseudo = filter_input(INPUT_POST, 'pseudo', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);

        // 2. Validations
        if (!$pseudo || !$email || !$message) {
            $this->sendJsonResponse('error', 'Données invalides ou email mal formé.');
        }

        // Vérification DNS du domaine de l'email
        $domain = substr(strrchr($email, "@"), 1);
        if (!checkdnsrr($domain, "MX")) {
            $this->sendJsonResponse('error', "La destination @{$domain} est introuvable dans la galaxie (Email inexistant).");
        }

        // 3. Envoi via PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configuration Serveur SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'dvmta39@gmail.com';
            $mail->Password = 'pnnikshkztituxfj'; // Idéalement, à passer via $_ENV['SMTP_PASS']
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';
            $mail->SMTPDebug = 0;

            // Destinataires
            $mail->setFrom('dvmta39@gmail.com', 'Meteastro - Station de Contrôle');
            $mail->addAddress('dvmta39@gmail.com');
            $mail->addReplyTo($email, $pseudo);

            // Intégration du Logo
            $logoPath = __DIR__ . '/../../public/assets/images/logo.png'; // Ajuster le chemin selon votre structure
            if (file_exists($logoPath)) {
                $mail->addEmbeddedImage($logoPath, 'meteastro_logo');
                $logoSrc = 'cid:meteastro_logo';
            } else {
                $logoSrc = 'https://meteastro/assets/images/logo.png';
            }

            // Contenu du mail
            $mail->isHTML(true);
            $mail->Subject = "🪐 [METEASTRO] Transmission de {$pseudo}";

            $mail->Body = "
        <div style='background-color: #020617; padding: 40px 10px; font-family: Arial, sans-serif;'>
            <table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px; background-color: #0f172a; border-radius: 12px; border: 1px solid #1e293b; color: #f1f5f9;'>
                <tr>
                    <td align='center' style='padding: 30px; background: #1e293b; border-radius: 12px 12px 0 0;'>
                        <img src='{$logoSrc}' alt='Meteastro' width='70' style='margin-bottom: 10px;'>
                        <div style='color: #38bdf8; font-size: 10px; text-transform: uppercase; letter-spacing: 3px;'>Station de Communication</div>
                    </td>
                </tr>
                <tr>
                    <td style='padding: 30px;'>
                        <h2 style='font-size: 18px; color: #38bdf8;'>Signal capté de : {$pseudo}</h2>
                        <div style='background: #020617; border: 1px solid #334155; padding: 20px; border-radius: 8px; line-height: 1.6; color: #e2e8f0;'>
                            " . nl2br($message) . "
                        </div>
                        <p style='margin-top: 25px; font-size: 13px; color: #94a3b8;'>
                            <strong>Source :</strong> {$email}
                        </p>
                    </td>
                </tr>
                <tr>
                    <td align='center' style='padding: 20px; font-size: 10px; color: #475569; border-top: 1px solid #1e293b;'>
                        METEASTRO SYSTEM &copy; 2026
                    </td>
                </tr>
            </table>
        </div>";

            $mail->AltBody = "Message de {$pseudo} ({$email}) : \n\n {$message}";

            $mail->send();

            $this->sendJsonResponse('success', 'Signal propulsé avec succès vers Meteastro !');

        } catch (Exception $e) {
            error_log("PHPMailer Error: " . $mail->ErrorInfo);
            $this->sendJsonResponse('error', 'Le signal a été dévié par une anomalie (Erreur d\'envoi).');
        }
    }

    /**
     * Helper privé pour l'envoi de réponses JSON propres
     */
    private function sendJsonResponse(string $status, string $message): void
    {
        // 1. Nettoie tout texte, espace ou HTML généré avant cette ligne
        if (ob_get_length()) {
            ob_clean();
        }

        // 2. Définit l'en-tête HTTP
        header('Content-Type: application/json; charset=utf-8');

        // 3. Envoie le JSON
        echo json_encode([
            'status' => $status,
            'message' => $message,
            'timestamp' => time()
        ], JSON_UNESCAPED_UNICODE);

        // 4. Bloque la suite de l'exécution (évite le rendu d'une vue HTML)
        exit();
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

    /**
     * Compte le nombre d'IPs uniques enregistrées pour le mois et l'année en cours.
     * @return int Le compte des visiteurs uniques du mois.
     */
    private function getUniqueVisitorMonthly(): int
    {
        $filepath = __DIR__ . "/../../iplist_date.txt";

        if (!file_exists($filepath)) {
            return 0;
        }

        $fileContent = file_get_contents($filepath);
        $lines = explode("\n", $fileContent); // Chaque ligne est une entrée (YYYY-MM-IP)

        $targetPrefix = date('Y-m');

        $count = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            // Vérifie si la ligne commence par 'AAAA-MM' du mois en cours
            if (!empty($line) && str_starts_with($line, $targetPrefix)) {
                $count++;
            }
        }

        return $count;
    }
}