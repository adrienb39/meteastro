<?php

namespace App\Controller;

use App\Entity\DecompteDepartSortie;
use App\Entity\DefilementTexte;
use App\Entity\MessageApresSortieHebdomadaire;
use App\Entity\MessageSortieHebdomadaireADefinir;
use App\Entity\Page;
use App\Entity\PageAPropos;
use App\Entity\PagePresentation;
use App\Entity\PageStatus;
use App\Entity\PhotoVideo;
use App\Entity\Reglage;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManager;

class HomeController extends AbstractController
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function index(): void
    {
        $index = true;
        $fuseauHoraire = new \DateTimeZone('Europe/Paris');
        $now = new \DateTime('now', $fuseauHoraire);

        // 1. Statistiques et réglages
        $this->addUniqueIP();
        $nombreVisite = $this->getUniqueVisitor();
        $this->addUniqueIPMonthly();

        $settings = $this->entityManager->getRepository(Reglage::class)->find(1);
        $pages = $this->entityManager->getRepository(Page::class)->findBy([], ['ordrePageAccueil' => 'ASC']);
        $pageAPropos = $this->entityManager->getRepository(PageAPropos::class)->findOneBy(['id' => 1]);
        $pageStatus = $this->entityManager->getRepository(PageStatus::class)->findOneBy(['id' => 1]);
        $pagePresentation = $this->entityManager->getRepository(PagePresentation::class)->findOneBy(['id' => 1]);

        $defilementTexte = $this->entityManager->getRepository(DefilementTexte::class)->find(1);

        // -----------------------------------------------------------
        // 2. LOGIQUE MULTI-SORTIES (Prochaines ou Dernières)
        // -----------------------------------------------------------

        // On cherche d'abord s'il y a des sorties aujourd'hui ou dans le futur
        $sortiesAffichees = $this->entityManager->getRepository(Sortie::class)->createQueryBuilder('s')
            ->where('s.date >= :now')
            ->setParameter('now', $now)
            ->orderBy('s.date', 'ASC')
            ->getQuery()
            ->getResult();

        // Si aucune sortie future, on récupère les dernières sorties passées (ex: les sorties d'hier)
        if (empty($sortiesAffichees)) {
            // On récupère la date de la toute dernière sortie enregistrée
            $lastDateResult = $this->entityManager->getRepository(Sortie::class)->createQueryBuilder('s')
                ->select('s.date')
                ->where('s.date < :now')
                ->setParameter('now', $now)
                ->orderBy('s.date', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if ($lastDateResult) {
                $lastDate = $lastDateResult['date']->format('Y-m-d');
                // On récupère toutes les sorties qui ont eu lieu ce jour-là
                $sortiesAffichees = $this->entityManager->getRepository(Sortie::class)->createQueryBuilder('s')
                    ->where('s.date LIKE :lastDate')
                    ->setParameter('lastDate', $lastDate . '%')
                    ->orderBy('s.date', 'ASC')
                    ->getQuery()
                    ->getResult();
            }
        }

        $messageApresSortieHebdomadaire = $this->entityManager->getRepository(MessageApresSortieHebdomadaire::class)->find(1);
        $messageSortieHebdomadaireADefinir = $this->entityManager->getRepository(MessageSortieHebdomadaireADefinir::class)->find(1);

        $medias = $this->entityManager->getRepository(PhotoVideo::class)->findAll();

        // 3. Render
        $this->render('home/index', [
            'index' => $index,
            'nombreVisite' => $nombreVisite,
            'settings' => $settings,
            'pages' => $pages,
            'pageAPropos' => $pageAPropos,
            'pageStatus' => $pageStatus,
            'pagePresentation' => $pagePresentation,
            'defilementTexte' => $defilementTexte,
            'sorties' => $sortiesAffichees, // On passe la collection complète
            'messageApresSortieHebdomadaire' => $messageApresSortieHebdomadaire,
            'messageSortieHebdomadaireADefinir' => $messageSortieHebdomadaireADefinir,
            'medias' => $medias
        ]);
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