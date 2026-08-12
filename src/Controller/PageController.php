<?php

namespace App\Controller;

use App\Entity\CategorieEvent;
use App\Entity\CircuitRandonnee;
use App\Entity\ContenuPage;
use App\Entity\ContenuSection;
use App\Entity\DateEvent;
use App\Entity\DecompteDepartSortie;
use App\Entity\DefilementTexte;
use App\Entity\DispositionPageAccueil;
use App\Entity\InscriptionGravelRandonnee;
use App\Entity\InscriptionPedestreRandonnee;
use App\Entity\InscriptionRouteRandonnee;
use App\Entity\InscriptionVTTRandonnee;
use App\Entity\Membre;
use App\Entity\MessageApresSortieHebdomadaire;
use App\Entity\MessageSortieHebdomadaireADefinir;
use App\Entity\Page;
use App\Entity\PageAPropos;
use App\Entity\PageCommentAdhererPdf;
use App\Entity\PagePresentation;
use App\Entity\PageStatus;
use App\Entity\PhotoVideo;
use App\Entity\Randonnee;
use App\Entity\Reglage;
use App\Entity\Section;
use App\Entity\Sortie;
use App\Entity\TypeSortie;
use App\Service\UploaderService;
use App\UserStory\CreerInscriptionGravelRandonnee;
use App\UserStory\CreerInscriptionPedestreRandonnee;
use App\UserStory\CreerInscriptionRouteRandonnee;
use App\UserStory\CreerInscriptionVTTRandonnee;
use App\UserStory\CreerMembre;
use App\UserStory\CreerPage;
use App\UserStory\CreerSection;
use App\UserStory\ModifierDecompteDepartSortie;
use App\UserStory\ModifierMembre;
use App\UserStory\ModifierPage;
use App\UserStory\ModifierPageAPropos;
use App\UserStory\ModifierPagePresentation;
use App\UserStory\ModifierPageStatus;
use App\UserStory\ModifierSection;
use App\UserStory\SupprimerSection;
use DateInterval;
use Doctrine\ORM\EntityManager;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use PHPMailer\PHPMailer\PHPMailer;

class PageController extends AbstractController
{
    private EntityManager $entityManager;

    private const UPLOAD_DIR = '/uploads/';

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Gère l'affichage d'une page publique. 
     * Si $pageUrl est 'randos' et une randonnée est trouvée, elle rend le template de randonnée directement.
     * Sinon, elle rend la page principale /pages/index.
     * * @param string $pageUrl L'URL de la page à charger (ex: 'accueil', 'contact', 'randos').
     * @return void
     * @throws \Exception Si la page principale n'est pas trouvée.
     */
    public function page(string $pageUrl): void
    {
        // --- 0. CONFIGURATION ET TEMPS ACTUEL ---
        $fuseauHoraire = new \DateTimeZone('Europe/Paris');
        $now = new \DateTime('now', $fuseauHoraire);

        // --- 1. STATISTIQUES ET RÉGLAGES ---
        $this->addUniqueIP();
        $nombreVisite = $this->getUniqueVisitor();
        $this->addUniqueIPMonthly();
        $settings = $this->entityManager->getRepository(Reglage::class)->find(1);

        // --- 2. RÉCUPÉRATION DES ENTITÉS DE PAGE ET DE CONTENU STATIQUE ---
        $pageEntity = $this->entityManager->getRepository(Page::class)->findOneBy(['url' => $pageUrl]);
        $contenusAssocies = $this->entityManager->getRepository(ContenuPage::class)->findBy(['page' => $pageEntity], ['ordre' => 'ASC']);
        if (!$pageEntity) {
            throw new \Exception("Erreur 404: La page demandée ($pageUrl) est introuvable !");
        }

        $pageAPropos = $this->entityManager->getRepository(PageAPropos::class)->find(1);
        $pageStatus = $this->entityManager->getRepository(PageStatus::class)->find(1);
        $pagePresentation = $this->entityManager->getRepository(PagePresentation::class)->find(1);

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

        // --- 4. GESTION DES ÉVÉNEMENTS (DateEvent) ---
        $eventsData = $this->getEventsData($pageEntity, $now);
        $eventsFiltres = $eventsData['eventsFiltres'];
        $eventsPage = $eventsData['eventsPage'];
        $eventsForPage = $eventsData['eventsForPage'];
        $eventsCalendar = $eventsData['eventsCalendar'];


        // --- 5. GESTION SPÉCIFIQUE DE LA RANDONNÉE ('randos') ---
        $randonneeToDisplay = null;
        $circuitsData = [];
        $templateRandonnee = 'tpl_defaut';

        if ($pageUrl === 'randos') {

            // 5.1. Recherche de la PROCHAINE randonnée active et publique
            // 5.1. Tente de trouver la PROCHAINE randonnée active et publiée
            $randonnee = $this->entityManager->getRepository(Randonnee::class)->createQueryBuilder('r')
                ->where('r.dateRandonnee >= :now')
                ->andWhere("r.statutPublication = 'Publié'") // ✅ Correction : Guilemets simples autour de la chaîne
                ->setParameter('now', $now)
                ->orderBy('r.dateRandonnee', 'ASC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            // 5.2. Si aucune randonnée à venir, chercher la DERNIÈRE passée (pour archivage)
            if (!$randonnee) {
                $randonnee = $this->entityManager->getRepository(Randonnee::class)->createQueryBuilder('r')
                    ->where('r.dateRandonnee < :now')
                    ->andWhere("r.statutPublication = 'Publié'") // ✅ Correction : Guilemets simples autour de la chaîne
                    ->setParameter('now', $now)
                    ->orderBy('r.dateRandonnee', 'DESC')
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult();
            }

            // 5.3. Préparation des données si une randonnée est trouvée
            if ($randonnee) {
                $randonneeToDisplay = $randonnee;
                $templateRandonnee = $randonnee->getModelePage() ?? 'tpl_defaut';

                // Préparation des circuits pour la vue
                foreach ($randonnee->getCircuits() as $circuit) {
                    $circuitsData[] = [
                        'id' => $circuit->getId(),
                        'nom' => $circuit->getNom(),
                        'distance_km' => $circuit->getDistanceKm(),
                        'denivele_positif' => $circuit->getDenivelePositif(),
                        'difficulte' => $circuit->getDifficulte(),
                        'type' => $circuit->getType(),
                        'fichier_gpx' => $circuit->getFichierGpx(),
                        'est_principal' => $circuit->isEstPrincipal(),
                        $prixInscriptionMoins18AnsLicencieCentimes = (float) $circuit->getPrixInscriptionMoins18AnsLicencieCentimes(),
                        'prix_inscription_moins_18_ans_licencie' => (int) round($prixInscriptionMoins18AnsLicencieCentimes / 100),
                        $prixInscriptionMoins18AnsNonLicencieCentimes = (float) $circuit->getPrixInscriptionMoins18AnsNonLicencieCentimes(),
                        'prix_inscription_moins_18_ans_non_licencie' => (int) round($prixInscriptionMoins18AnsNonLicencieCentimes / 100),
                        $prixInscriptionAdulteLicencieCentimes = (float) $circuit->getPrixInscriptionAdulteLicencieCentimes(),
                        'prix_inscription_adulte_licencie' => (int) round($prixInscriptionAdulteLicencieCentimes / 100),
                        $prixInscriptionAdulteNonLicencieCentimes = (float) $circuit->getPrixInscriptionAdulteNonLicencieCentimes(),
                        'prix_inscription_adulte_non_licencie' => (int) round($prixInscriptionAdulteNonLicencieCentimes / 100)
                    ];
                }
            }

            // 5.4. **RENDU DIRECT ET ARRÊT SI UNE RANDONNÉE EST TROUVÉE**
            // Cela remplace le rendu de '/pages/index' par le rendu du template spécifique.
            if ($randonneeToDisplay) {
                // Pour que la vue ait accès aux variables:
                $dataForTemplate = [
                    'randonnee' => $randonneeToDisplay,
                    'circuitsData' => $circuitsData,
                    'templateRandonnee' => $templateRandonnee,
                    'settings' => $settings, // Inclure les réglages nécessaires pour le header/footer si vous en avez
                    'index' => true,
                    'indexPage' => true,
                    'nombreVisite' => $nombreVisite,
                    'page' => $pageEntity,
                    'contenus' => $contenusAssocies,
                    'pageAPropos' => $pageAPropos,
                    'pageStatus' => $pageStatus,
                    'pagePresentation' => $pagePresentation,

                    // Données de Sortie
                    'sorties' => $sortiesAffichees, // On passe la collection complète
                    'messageApresSortieHebdomadaire' => $messageApresSortieHebdomadaire,
                    'messageSortieHebdomadaireADefinir' => $messageSortieHebdomadaireADefinir,

                    // 'decompteDepartSortie' => $decompteDepartSortie,
                    // 'dateSortie' => $dateSortie,
                    // 'temps' => $temps,
                    // 'message' => $message,

                    // Données des Événements
                    'eventsPage' => $eventsPage,
                    'eventsAll' => $eventsFiltres,
                    'eventsForPage' => $eventsForPage,
                    'events' => json_encode($eventsCalendar),
                ];

                // Le template de randonnée sera rendu dans un layout global si nécessaire, 
                // mais l'inclusion directe est ce que vous avez demandé:
                $this->render('/pages/randonnees/' . $templateRandonnee, $dataForTemplate);

                return; // Arrête l'exécution pour ne pas rendre /pages/index.php
            }
            // Note: Si $pageUrl === 'randos' mais aucune randonnée n'est trouvée,
            // on passe au rendu par défaut qui affichera la page 'randos' vide.
        }

        $medias = $this->entityManager->getRepository(PhotoVideo::class)->findAll();

        $fichiersPdf = $this->entityManager->getRepository(PageCommentAdhererPdf::class)->findAll();

        // --- 6. RENDU DE LA PAGE NORMALE (/pages/index.php) ---
        // Ceci gère 'accueil', 'contact', ou 'randos' sans randonnée trouvée.
        $this->render('/pages/index', [
            'index' => true,
            'indexPage' => true,
            'nombreVisite' => $nombreVisite,
            'settings' => $settings,
            'page' => $pageEntity,
            'contenus' => $contenusAssocies,
            'pageAPropos' => $pageAPropos,
            'pageStatus' => $pageStatus,
            'pagePresentation' => $pagePresentation,

            // Données de Sortie
            'sorties' => $sortiesAffichees, // On passe la collection complète
            'messageApresSortieHebdomadaire' => $messageApresSortieHebdomadaire,
            'messageSortieHebdomadaireADefinir' => $messageSortieHebdomadaireADefinir,

            // 'decompteDepartSortie' => $decompteDepartSortie,
            // 'dateSortie' => $dateSortie,
            // 'temps' => $temps,
            // 'message' => $message,

            // Données des Événements
            'eventsPage' => $eventsPage,
            'eventsAll' => $eventsFiltres,
            'eventsForPage' => $eventsForPage,
            'events' => json_encode($eventsCalendar),

            // Données de Randonnée (Seront nulles si $pageUrl !== 'randos')
            'randonnee' => $randonneeToDisplay,
            'circuitsData' => $circuitsData,
            'templateRandonnee' => $templateRandonnee,
            'medias' => $medias,
            'fichiersPdf' => $fichiersPdf
        ]);
    }

    /**
     * Récupère la prochaine sortie ou la dernière sortie passée à afficher.
     * * @param \DateTime $now
     * @return Sortie|null
     */
    private function getSortieToDisplay(\DateTime $now): ?Sortie
    {
        // Tente de trouver la PROCHAINE sortie (date > NOW)
        $prochaineSortie = $this->entityManager->getRepository(Sortie::class)->createQueryBuilder('s')
            ->where('s.date >= :now')
            ->setParameter('now', $now)
            ->orderBy('s.date', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($prochaineSortie) {
            return $prochaineSortie;
        }

        // Si aucune prochaine sortie, on cherche la DERNIÈRE sortie passée (date < NOW)
        return $this->entityManager->getRepository(Sortie::class)->createQueryBuilder('s')
            ->where('s.date < :now')
            ->setParameter('now', $now)
            ->orderBy('s.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Récupère et filtre les données des événements.
     * * @param Page $pageEntity Page actuelle pour le filtrage
     * @param \DateTime $now Date actuelle pour le filtrage
     * @return array
     */
    private function getEventsData(Page $pageEntity, \DateTime $now): array
    {
        $allEvents = $this->entityManager->getRepository(DateEvent::class)->findAll();

        // Filtrer les événements (uniquement ceux dont la date de fin est passée)
        $eventsFiltres = array_filter($allEvents, function ($event) use ($now) {
            $start = $event->getDateStart();
            $end = $event->getDateEnd();

            // On exclut si la date de début est future, ou si la date de fin est future
            if (!$start || $start > $now) {
                return false;
            }
            if ($end && $end > $now) {
                return false;
            }
            return true;
        });

        // Trier par date de début décroissante (pour afficher les plus récents en premier)
        usort($eventsFiltres, function ($a, $b) {
            return $b->getDateStart() <=> $a->getDateStart();
        });

        // Préparer les données pour la page et le calendrier
        // MODIFICATION : On prend TOUS les événements filtrés et triés
        $eventsPage = $eventsFiltres;

        $eventsForPage = array_filter($eventsPage, function ($event) use ($pageEntity) {
            // Filtre : événements qui correspondent à l'URL de la page ET qui ont un Compte Rendu
            return $event->getCategorieEvent()->getUrl() === $pageEntity->getUrl() && $event->getCompteRendu() !== null;
        });

        $eventsCalendar = [];
        foreach ($allEvents as $event) {
            if ($event->getDateStart()) {
                $eventData = [
                    'title' => $event->getTitre(),
                    'description' => $event->getDescription(),
                    'start' => $event->getDateStart()->format('Y-m-d\TH:i:s'),
                    'categorieUrl' => $event->getCategorieEvent()->getUrl(),
                    'compteRenduId' => $event->getId(),
                    'compteRendu' => $event->getCompteRendu()
                ];
                if ($event->getDateEnd()) {
                    $eventData['end'] = $event->getDateEnd()->format('Y-m-d\TH:i:s');
                }
                $eventsCalendar[] = $eventData;
            }
        }

        return [
            'eventsFiltres' => $eventsFiltres,
            'eventsPage' => $eventsPage,
            'eventsForPage' => $eventsForPage,
            'eventsCalendar' => $eventsCalendar,
        ];
    }

    public function creerInscriptionVTT(string $slugRandonnee, int $id): void
    {
        // --- 1. INITIALISATION & VÉRIFICATION DU CONTEXTE ---

        // --- 0. CONFIGURATION ET TEMPS ACTUEL ---
        $fuseauHoraire = new \DateTimeZone('Europe/Paris');
        $now = new \DateTime('now', $fuseauHoraire);

        // --- 1. STATISTIQUES ET RÉGLAGES ---
        $this->addUniqueIP();
        $nombreVisite = $this->getUniqueVisitor();
        $this->addUniqueIPMonthly();
        $settings = $this->entityManager->getRepository(Reglage::class)->find(1);

        $pages = $this->entityManager->getRepository(Page::class)->findBy([], ['ordrePageAccueil' => 'ASC']);

        $pageAPropos = $this->entityManager->getRepository(PageAPropos::class)->find(1);
        $pageStatus = $this->entityManager->getRepository(PageStatus::class)->find(1);
        $pagePresentation = $this->entityManager->getRepository(PagePresentation::class)->find(1);

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

        // Récupérer la Randonnée et le Parcours spécifiques
        $randonnee = $this->entityManager->getRepository(Randonnee::class)->findOneBy(['slug' => $slugRandonnee]);
        $circuitRandonnee = $this->entityManager->getRepository(CircuitRandonnee::class)->find($id);

        // Vérification de l'existence des entités (Sécurité)
        if (!$randonnee || !$circuitRandonnee) {
            session_start();
            $_SESSION['error_message'] = 'Randonnée ou circuit introuvable.';
            $this->redirect('/page/randos');
            return;
        }

        // Date limite d'inscription (Utilisation de la date de la randonnée pour le calcul)
        // La date limite est 7 jours avant la randonnée, par exemple
        $dateLimiteInscription = (clone $randonnee->getDateRandonnee())->modify('-7 days');

        // Vérification de la date limite
        if ((new \DateTime()) > $dateLimiteInscription) {
            session_start();
            $_SESSION['error_message'] = 'Les inscriptions en ligne pour cette randonnée sont fermées. Inscription possible sur place le jour J.';
            $this->redirect('/page/randos'); // Redirection vers la page de la randonnée
            return;
        }

        $error = null;
        $index = true;

        // --- 2. TRAITEMENT DU FORMULAIRE (POST) ---

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $nomMembres = $_POST["nom"] ?? [];
            $emailMembres = $_POST["email"] ?? [];
            // Autres tableaux de données...
            // Les champs d'adresse sont uniques pour le premier membre/payeur
            $adresse = $_POST["adresse"] ?? '';
            $codePostal = $_POST["code_postal"] ?? '';
            $ville = $_POST["ville"] ?? '';
            $nomPrenomTelUrgence = $_POST["nom_prenom_tel"] ?? '';

            $membresInscrits = [];
            $montantTotal = 0.0; // Utiliser un float pour le montant

            // Génère un ID de groupe d'inscription unique pour lier tous les participants
            $numeroInscription = uniqid('inscription_');

            // Validation et préparation des données de chaque membre
            foreach ($nomMembres as $i => $nom) {

                // Validation basique des champs obligatoires (ajustez selon votre validation front-end/back-end)
                if (empty($nom) || empty($_POST["prenom"][$i]) || empty($_POST["sexe"][$i]) || empty($_POST["date_naissance"][$i]) || empty($_POST["num_tel"][$i]) || empty($emailMembres[$i])) {
                    // Vérification supplémentaire pour le membre principal
                    if ($i === 0 && (empty($adresse) || empty($codePostal) || empty($ville) || empty($nomPrenomTelUrgence))) {
                        $error = "Veuillez remplir tous les champs obligatoires (y compris l'adresse et l'urgence) pour le participant principal.";
                    } else {
                        $error = "Veuillez remplir tous les champs obligatoires pour le participant n°" . ($i + 1) . ".";
                    }
                    break; // Sortir du foreach si erreur
                }


                // Calcul de l'âge et du montant unitaire
                try {
                    $dateNaissance = \DateTime::createFromFormat('Y-m-d', $_POST["date_naissance"][$i]);
                    if (!$dateNaissance) {
                        throw new \Exception('Date de naissance invalide.');
                    }

                    $diff = $dateNaissance->diff(new \DateTime());
                    $age = $diff->y;

                    $numLicence = $_POST["num_licence"][$i] ?? null;
                    $estLicencie = !empty($numLicence);

                    $prixInscriptionMoins18AnsLicencieCentimes = (float) $circuitRandonnee->getPrixInscriptionMoins18AnsLicencieCentimes();
                    $prixInscriptionMoins18AnsLicencie = (int) round($prixInscriptionMoins18AnsLicencieCentimes / 100);
                    $prixInscriptionMoins18AnsNonLicencieCentimes = (float) $circuitRandonnee->getPrixInscriptionMoins18AnsNonLicencieCentimes();
                    $prixInscriptionMoins18AnsNonLicencie = (int) round($prixInscriptionMoins18AnsNonLicencieCentimes / 100);
                    $prixInscriptionAdulteLicencieCentimes = (float) $circuitRandonnee->getPrixInscriptionAdulteLicencieCentimes();
                    $prixInscriptionAdulteLicencie = (int) round($prixInscriptionAdulteLicencieCentimes / 100);
                    $prixInscriptionAdulteNonLicencieCentimes = (float) $circuitRandonnee->getPrixInscriptionAdulteNonLicencieCentimes();
                    $prixInscriptionAdulteNonLicencie = (int) round($prixInscriptionAdulteNonLicencieCentimes / 100);

                    // Logique de tarif (exemple : licencié vs non-licencié, adulte vs mineur)
                    if ($age < 18) {
                        $montantUnitaire = $estLicencie ? $prixInscriptionMoins18AnsLicencie : $prixInscriptionMoins18AnsNonLicencie; // Moins de 18 ans
                    } else {
                        $montantUnitaire = $estLicencie ? $prixInscriptionAdulteLicencie : $prixInscriptionAdulteNonLicencie; // Adulte
                    }

                    $montantTotal += $montantUnitaire;

                    // Détermination du statut de paiement initial
                    $statutPaiement = ($montantUnitaire > 0) ? 'ATTENTE_PAIEMENT' : 'PAYE';

                    // Préparation des données du membre
                    $membresInscrits[] = [
                        'nom' => $nom,
                        'prenom' => $_POST["prenom"][$i],
                        'sexe' => $_POST["sexe"][$i],
                        'dateNaissance' => $dateNaissance,
                        'numTel' => $_POST["num_tel"][$i],
                        'email' => $emailMembres[$i],
                        'licenceFfveloClub' => $_POST["licence_ffvelo_club"][$i] ?? null,
                        'numLicence' => $numLicence,
                        'autreFederationClub' => $_POST["autre_federation_club"][$i] ?? null,
                        'montant' => $montantUnitaire,
                        'isPrincipal' => ($i === 0),
                        'numeroInscription' => $numeroInscription,
                        'statutPaiement' => $statutPaiement,
                    ];

                } catch (\Exception $e) {
                    $error = "Erreur de validation pour un participant : " . $e->getMessage();
                    break; // Sortir du foreach si erreur
                }
            } // Fin du foreach

            // Si aucune erreur de validation PHP n'est trouvée ($error est null)
            if ($error === null && !empty($membresInscrits)) {

                // 3. ENREGISTREMENT DES INSCRIPTIONS EN BASE DE DONNÉES
                $inscriptionPrincipaleId = null;

                foreach ($membresInscrits as $membre) {
                    try {
                        // Instanciation de l'objet métier de création (hypothétique service)
                        // Remplacement par le nouveau nom d'entité/service
                        $creerInscription = new CreerInscriptionVTTRandonnee($this->entityManager);

                        // Exécution de la création de l'entité
                        // NOTE: Vous devez ajuster la méthode 'execute' de votre service pour accepter 
                        //       le CircuitRandonnee, le montant et le statut de paiement.
                        $inscriptionVTTRandonnee = $creerInscription->execute(
                            $membre['nom'],
                            $membre['prenom'],
                            $membre['sexe'],
                            $membre['dateNaissance'],
                            ($membre['isPrincipal'] ? $adresse : null), // Adresse unique (nullable)
                            ($membre['isPrincipal'] ? $codePostal : null), // CP unique (nullable)
                            ($membre['isPrincipal'] ? $ville : null), // Ville unique (nullable)
                            $membre['numTel'],
                            $membre['email'],
                            $nomPrenomTelUrgence, // Urgence unique
                            $membre['licenceFfveloClub'],
                            $membre['numLicence'],
                            $membre['autreFederationClub'],
                            $membre['numeroInscription'],
                            $circuitRandonnee, // L'entité CircuitRandonnee (le parcours)
                            $membre['montant'], // Le montant unitaire à enregistrer dans montantPaye
                            $membre['statutPaiement'] // Statut initial (PAYE si montant=0, ATTENTE_PAIEMENT sinon)
                        );

                        // On récupère l'ID de la première inscription pour Stripe
                        if ($inscriptionPrincipaleId === null) {
                            $inscriptionPrincipaleId = $inscriptionVTTRandonnee->getId();
                        }

                    } catch (\Exception $e) {
                        // Gestion d'une erreur d'enregistrement
                        $error = "Erreur lors de l'enregistrement en base de données pour " . $membre['nom'] . ": " . $e->getMessage();
                        break;
                    }
                } // Fin de la boucle d'enregistrement

                // 4. PRÉPARATION DU PAIEMENT STRIPE
                if ($error === null && $montantTotal > 0 && $inscriptionPrincipaleId !== null) {
                    try {
                        // Assurez-vous d'avoir bien initialisé la librairie Stripe
                        // \Stripe\Stripe::setApiKey($this->settings->getStripeSecretKey()); 
                        \Stripe\Stripe::setApiKey("");

                        $session = \Stripe\Checkout\Session::create([
                            'payment_method_types' => ['card'],
                            'line_items' => [
                                [
                                    'price_data' => [
                                        'currency' => 'eur',
                                        'product_data' => [
                                            'name' => 'Inscription Randonnée VTT (' . count($membresInscrits) . ' participant(s))',
                                        ],
                                        // Utilisation du montant total calculé, en centimes
                                        'unit_amount' => (int) ($montantTotal * 100),
                                    ],
                                    'quantity' => 1,
                                ],
                            ],
                            'mode' => 'payment',
                            // Attention: Utiliser des URLs dynamiques
                            'success_url' => 'https://avva39.fr/randonnee/' . $slugRandonnee . '/succes-paiement?session_id={CHECKOUT_SESSION_ID}',
                            'cancel_url' => 'https://avva39.fr/randonnee/' . $slugRandonnee,
                            'client_reference_id' => $inscriptionPrincipaleId, // ID de la première inscription (payeur)
                            'metadata' => [
                                'numero_inscription_groupe' => $numeroInscription,
                                'type_circuit' => 'VTT',
                                'circuit_id' => $id
                            ],
                        ]);

                        // Redirection vers l'URL de paiement Stripe
                        $this->redirect($session->url);
                        return;

                    } catch (\Exception $e) {
                        $error = "Erreur lors de la création de la session de paiement Stripe: " . $e->getMessage();
                        // NOTE: En cas d'erreur Stripe, vous devriez idéalement supprimer les inscriptions créées 
                        // qui sont restées au statut 'ATTENTE_PAIEMENT'.
                    }
                } elseif ($montantTotal === 0.0 && $error === null) {
                    // Cas d'une inscription totalement gratuite (Ex: tous mineurs licenciés)
                    session_start();
                    $_SESSION['success_message'] = "Votre inscription gratuite a été enregistrée avec succès sous le numéro " . $numeroInscription . ".";
                    $this->redirect('/page/randos');
                    return;
                }
            } // Fin if ($error === null)
        } // Fin if ($_SERVER["REQUEST_METHOD"] === "POST")

        // --- 5. AFFICHAGE DU TEMPLATE ---

        // Si la requête n'est pas POST, ou si une erreur est survenue, afficher le formulaire
        $this->render('pages/randonnees/inscription-vtt', [
            'index' => $index,
            'indexPage' => true,
            'randonnee' => $randonnee, // Passer la randonnée pour le titre
            'circuitRandonnee' => $circuitRandonnee,
            'error' => $error,
            'nombreVisite' => $nombreVisite,
            'settings' => $settings,
            'pages' => $pages,
            'pageAPropos' => $pageAPropos,
            'pageStatus' => $pageStatus,
            'pagePresentation' => $pagePresentation,

            // Données de Sortie
            'sorties' => $sortiesAffichees, // On passe la collection complète
            'messageApresSortieHebdomadaire' => $messageApresSortieHebdomadaire,
            'messageSortieHebdomadaireADefinir' => $messageSortieHebdomadaireADefinir,
        ]);
    }

    public function creerInscriptionGravel(string $slugRandonnee, int $id): void
    {
        // --- 1. INITIALISATION & VÉRIFICATION DU CONTEXTE ---

        // --- 0. CONFIGURATION ET TEMPS ACTUEL ---
        $fuseauHoraire = new \DateTimeZone('Europe/Paris');
        $now = new \DateTime('now', $fuseauHoraire);

        // --- 1. STATISTIQUES ET RÉGLAGES ---
        $this->addUniqueIP();
        $nombreVisite = $this->getUniqueVisitor();
        $this->addUniqueIPMonthly();
        $settings = $this->entityManager->getRepository(Reglage::class)->find(1);

        $pages = $this->entityManager->getRepository(Page::class)->findBy([], ['ordrePageAccueil' => 'ASC']);

        $pageAPropos = $this->entityManager->getRepository(PageAPropos::class)->find(1);
        $pageStatus = $this->entityManager->getRepository(PageStatus::class)->find(1);
        $pagePresentation = $this->entityManager->getRepository(PagePresentation::class)->find(1);

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

        // Récupérer la Randonnée et le Parcours spécifiques
        $randonnee = $this->entityManager->getRepository(Randonnee::class)->findOneBy(['slug' => $slugRandonnee]);
        $circuitRandonnee = $this->entityManager->getRepository(CircuitRandonnee::class)->find($id);

        // Vérification de l'existence des entités (Sécurité)
        if (!$randonnee || !$circuitRandonnee) {
            session_start();
            $_SESSION['error_message'] = 'Randonnée ou circuit introuvable.';
            $this->redirect('/page/randos');
            return;
        }

        // Date limite d'inscription (Utilisation de la date de la randonnée pour le calcul)
        // La date limite est 7 jours avant la randonnée, par exemple
        $dateLimiteInscription = (clone $randonnee->getDateRandonnee())->modify('-7 days');

        // Vérification de la date limite
        if ((new \DateTime()) > $dateLimiteInscription) {
            session_start();
            $_SESSION['error_message'] = 'Les inscriptions en ligne pour cette randonnée sont fermées. Inscription possible sur place le jour J.';
            $this->redirect('/page/randos'); // Redirection vers la page de la randonnée
            return;
        }

        $error = null;
        $index = true;

        // --- 2. TRAITEMENT DU FORMULAIRE (POST) ---

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $nomMembres = $_POST["nom"] ?? [];
            $emailMembres = $_POST["email"] ?? [];
            // Autres tableaux de données...
            // Les champs d'adresse sont uniques pour le premier membre/payeur
            $adresse = $_POST["adresse"] ?? '';
            $codePostal = $_POST["code_postal"] ?? '';
            $ville = $_POST["ville"] ?? '';
            $nomPrenomTelUrgence = $_POST["nom_prenom_tel"] ?? '';

            $membresInscrits = [];
            $montantTotal = 0.0; // Utiliser un float pour le montant

            // Génère un ID de groupe d'inscription unique pour lier tous les participants
            $numeroInscription = uniqid('inscription_');

            // Validation et préparation des données de chaque membre
            foreach ($nomMembres as $i => $nom) {

                // Validation basique des champs obligatoires (ajustez selon votre validation front-end/back-end)
                if (empty($nom) || empty($_POST["prenom"][$i]) || empty($_POST["sexe"][$i]) || empty($_POST["date_naissance"][$i]) || empty($_POST["num_tel"][$i]) || empty($emailMembres[$i])) {
                    // Vérification supplémentaire pour le membre principal
                    if ($i === 0 && (empty($adresse) || empty($codePostal) || empty($ville) || empty($nomPrenomTelUrgence))) {
                        $error = "Veuillez remplir tous les champs obligatoires (y compris l'adresse et l'urgence) pour le participant principal.";
                    } else {
                        $error = "Veuillez remplir tous les champs obligatoires pour le participant n°" . ($i + 1) . ".";
                    }
                    break; // Sortir du foreach si erreur
                }


                // Calcul de l'âge et du montant unitaire
                try {
                    $dateNaissance = \DateTime::createFromFormat('Y-m-d', $_POST["date_naissance"][$i]);
                    if (!$dateNaissance) {
                        throw new \Exception('Date de naissance invalide.');
                    }

                    $diff = $dateNaissance->diff(new \DateTime());
                    $age = $diff->y;

                    $numLicence = $_POST["num_licence"][$i] ?? null;
                    $estLicencie = !empty($numLicence);

                    $prixInscriptionMoins18AnsLicencieCentimes = (float) $circuitRandonnee->getPrixInscriptionMoins18AnsLicencieCentimes();
                    $prixInscriptionMoins18AnsLicencie = (int) round($prixInscriptionMoins18AnsLicencieCentimes / 100);
                    $prixInscriptionMoins18AnsNonLicencieCentimes = (float) $circuitRandonnee->getPrixInscriptionMoins18AnsNonLicencieCentimes();
                    $prixInscriptionMoins18AnsNonLicencie = (int) round($prixInscriptionMoins18AnsNonLicencieCentimes / 100);
                    $prixInscriptionAdulteLicencieCentimes = (float) $circuitRandonnee->getPrixInscriptionAdulteLicencieCentimes();
                    $prixInscriptionAdulteLicencie = (int) round($prixInscriptionAdulteLicencieCentimes / 100);
                    $prixInscriptionAdulteNonLicencieCentimes = (float) $circuitRandonnee->getPrixInscriptionAdulteNonLicencieCentimes();
                    $prixInscriptionAdulteNonLicencie = (int) round($prixInscriptionAdulteNonLicencieCentimes / 100);

                    // Logique de tarif (exemple : licencié vs non-licencié, adulte vs mineur)
                    if ($age < 18) {
                        $montantUnitaire = $estLicencie ? $prixInscriptionMoins18AnsLicencie : $prixInscriptionMoins18AnsNonLicencie; // Moins de 18 ans
                    } else {
                        $montantUnitaire = $estLicencie ? $prixInscriptionAdulteLicencie : $prixInscriptionAdulteNonLicencie; // Adulte
                    }

                    $montantTotal += $montantUnitaire;

                    // Détermination du statut de paiement initial
                    $statutPaiement = ($montantUnitaire > 0) ? 'ATTENTE_PAIEMENT' : 'PAYE';

                    // Préparation des données du membre
                    $membresInscrits[] = [
                        'nom' => $nom,
                        'prenom' => $_POST["prenom"][$i],
                        'sexe' => $_POST["sexe"][$i],
                        'dateNaissance' => $dateNaissance,
                        'numTel' => $_POST["num_tel"][$i],
                        'email' => $emailMembres[$i],
                        'licenceFfveloClub' => $_POST["licence_ffvelo_club"][$i] ?? null,
                        'numLicence' => $numLicence,
                        'autreFederationClub' => $_POST["autre_federation_club"][$i] ?? null,
                        'montant' => $montantUnitaire,
                        'isPrincipal' => ($i === 0),
                        'numeroInscription' => $numeroInscription,
                        'statutPaiement' => $statutPaiement,
                    ];

                } catch (\Exception $e) {
                    $error = "Erreur de validation pour un participant : " . $e->getMessage();
                    break; // Sortir du foreach si erreur
                }
            } // Fin du foreach

            // Si aucune erreur de validation PHP n'est trouvée ($error est null)
            if ($error === null && !empty($membresInscrits)) {

                // 3. ENREGISTREMENT DES INSCRIPTIONS EN BASE DE DONNÉES
                $inscriptionPrincipaleId = null;

                foreach ($membresInscrits as $membre) {
                    try {
                        // Instanciation de l'objet métier de création (hypothétique service)
                        // Remplacement par le nouveau nom d'entité/service
                        $creerInscription = new CreerInscriptionGravelRandonnee($this->entityManager);

                        // Exécution de la création de l'entité
                        // NOTE: Vous devez ajuster la méthode 'execute' de votre service pour accepter 
                        //       le CircuitRandonnee, le montant et le statut de paiement.
                        $inscriptionGravelRandonnee = $creerInscription->execute(
                            $membre['nom'],
                            $membre['prenom'],
                            $membre['sexe'],
                            $membre['dateNaissance'],
                            ($membre['isPrincipal'] ? $adresse : null), // Adresse unique (nullable)
                            ($membre['isPrincipal'] ? $codePostal : null), // CP unique (nullable)
                            ($membre['isPrincipal'] ? $ville : null), // Ville unique (nullable)
                            $membre['numTel'],
                            $membre['email'],
                            $nomPrenomTelUrgence, // Urgence unique
                            $membre['licenceFfveloClub'],
                            $membre['numLicence'],
                            $membre['autreFederationClub'],
                            $membre['numeroInscription'],
                            $circuitRandonnee, // L'entité CircuitRandonnee (le parcours)
                            $membre['montant'], // Le montant unitaire à enregistrer dans montantPaye
                            $membre['statutPaiement'] // Statut initial (PAYE si montant=0, ATTENTE_PAIEMENT sinon)
                        );

                        // On récupère l'ID de la première inscription pour Stripe
                        if ($inscriptionPrincipaleId === null) {
                            $inscriptionPrincipaleId = $inscriptionGravelRandonnee->getId();
                        }

                    } catch (\Exception $e) {
                        // Gestion d'une erreur d'enregistrement
                        $error = "Erreur lors de l'enregistrement en base de données pour " . $membre['nom'] . ": " . $e->getMessage();
                        break;
                    }
                } // Fin de la boucle d'enregistrement

                // 4. PRÉPARATION DU PAIEMENT STRIPE
                if ($error === null && $montantTotal > 0 && $inscriptionPrincipaleId !== null) {
                    try {
                        // Assurez-vous d'avoir bien initialisé la librairie Stripe
                        // \Stripe\Stripe::setApiKey($this->settings->getStripeSecretKey()); 
                        \Stripe\Stripe::setApiKey("");

                        $session = \Stripe\Checkout\Session::create([
                            'payment_method_types' => ['card'],
                            'line_items' => [
                                [
                                    'price_data' => [
                                        'currency' => 'eur',
                                        'product_data' => [
                                            'name' => 'Inscription Randonnée GRAVEL (' . count($membresInscrits) . ' participant(s))',
                                        ],
                                        // Utilisation du montant total calculé, en centimes
                                        'unit_amount' => (int) ($montantTotal * 100),
                                    ],
                                    'quantity' => 1,
                                ],
                            ],
                            'mode' => 'payment',
                            // Attention: Utiliser des URLs dynamiques
                            'success_url' => 'https://avva39.fr/randonnee/' . $slugRandonnee . '/succes-paiement?session_id={CHECKOUT_SESSION_ID}',
                            'cancel_url' => 'https://avva39.fr/randonnee/' . $slugRandonnee,
                            'client_reference_id' => $inscriptionPrincipaleId, // ID de la première inscription (payeur)
                            'metadata' => [
                                'numero_inscription_groupe' => $numeroInscription,
                                'type_circuit' => 'GRAVEL',
                                'circuit_id' => $id
                            ],
                        ]);

                        // Redirection vers l'URL de paiement Stripe
                        $this->redirect($session->url);
                        return;

                    } catch (\Exception $e) {
                        $error = "Erreur lors de la création de la session de paiement Stripe: " . $e->getMessage();
                        // NOTE: En cas d'erreur Stripe, vous devriez idéalement supprimer les inscriptions créées 
                        // qui sont restées au statut 'ATTENTE_PAIEMENT'.
                    }
                } elseif ($montantTotal === 0.0 && $error === null) {
                    // Cas d'une inscription totalement gratuite (Ex: tous mineurs licenciés)
                    session_start();
                    $_SESSION['success_message'] = "Votre inscription gratuite a été enregistrée avec succès sous le numéro " . $numeroInscription . ".";
                    $this->redirect('/page/randos');
                    return;
                }
            } // Fin if ($error === null)
        } // Fin if ($_SERVER["REQUEST_METHOD"] === "POST")

        // --- 5. AFFICHAGE DU TEMPLATE ---

        // Si la requête n'est pas POST, ou si une erreur est survenue, afficher le formulaire
        $this->render('pages/randonnees/inscription-gravel', [
            'index' => $index,
            'indexPage' => true,
            'randonnee' => $randonnee, // Passer la randonnée pour le titre
            'circuitRandonnee' => $circuitRandonnee,
            'error' => $error,
            'nombreVisite' => $nombreVisite,
            'settings' => $settings,
            'pages' => $pages,
            'pageAPropos' => $pageAPropos,
            'pageStatus' => $pageStatus,
            'pagePresentation' => $pagePresentation,

            // Données de Sortie
            'sorties' => $sortiesAffichees, // On passe la collection complète
            'messageApresSortieHebdomadaire' => $messageApresSortieHebdomadaire,
            'messageSortieHebdomadaireADefinir' => $messageSortieHebdomadaireADefinir,
        ]);
    }

    public function creerInscriptionRoute(string $slugRandonnee, int $id): void
    {
        // --- 1. INITIALISATION & VÉRIFICATION DU CONTEXTE ---

        // --- 0. CONFIGURATION ET TEMPS ACTUEL ---
        $fuseauHoraire = new \DateTimeZone('Europe/Paris');
        $now = new \DateTime('now', $fuseauHoraire);

        // --- 1. STATISTIQUES ET RÉGLAGES ---
        $this->addUniqueIP();
        $nombreVisite = $this->getUniqueVisitor();
        $this->addUniqueIPMonthly();
        $settings = $this->entityManager->getRepository(Reglage::class)->find(1);

        $pages = $this->entityManager->getRepository(Page::class)->findBy([], ['ordrePageAccueil' => 'ASC']);

        $pageAPropos = $this->entityManager->getRepository(PageAPropos::class)->find(1);
        $pageStatus = $this->entityManager->getRepository(PageStatus::class)->find(1);
        $pagePresentation = $this->entityManager->getRepository(PagePresentation::class)->find(1);

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

        // Récupérer la Randonnée et le Parcours spécifiques
        $randonnee = $this->entityManager->getRepository(Randonnee::class)->findOneBy(['slug' => $slugRandonnee]);
        $circuitRandonnee = $this->entityManager->getRepository(CircuitRandonnee::class)->find($id);

        // Vérification de l'existence des entités (Sécurité)
        if (!$randonnee || !$circuitRandonnee) {
            session_start();
            $_SESSION['error_message'] = 'Randonnée ou circuit introuvable.';
            $this->redirect('/page/randos');
            return;
        }

        // Date limite d'inscription (Utilisation de la date de la randonnée pour le calcul)
        // La date limite est 7 jours avant la randonnée, par exemple
        $dateLimiteInscription = (clone $randonnee->getDateRandonnee())->modify('-7 days');

        // Vérification de la date limite
        if ((new \DateTime()) > $dateLimiteInscription) {
            session_start();
            $_SESSION['error_message'] = 'Les inscriptions en ligne pour cette randonnée sont fermées. Inscription possible sur place le jour J.';
            $this->redirect('/page/randos'); // Redirection vers la page de la randonnée
            return;
        }

        $error = null;
        $index = true;

        // --- 2. TRAITEMENT DU FORMULAIRE (POST) ---

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $nomMembres = $_POST["nom"] ?? [];
            $emailMembres = $_POST["email"] ?? [];
            // Autres tableaux de données...
            // Les champs d'adresse sont uniques pour le premier membre/payeur
            $adresse = $_POST["adresse"] ?? '';
            $codePostal = $_POST["code_postal"] ?? '';
            $ville = $_POST["ville"] ?? '';
            $nomPrenomTelUrgence = $_POST["nom_prenom_tel"] ?? '';

            $membresInscrits = [];
            $montantTotal = 0.0; // Utiliser un float pour le montant

            // Génère un ID de groupe d'inscription unique pour lier tous les participants
            $numeroInscription = uniqid('inscription_');

            // Validation et préparation des données de chaque membre
            foreach ($nomMembres as $i => $nom) {

                // Validation basique des champs obligatoires (ajustez selon votre validation front-end/back-end)
                if (empty($nom) || empty($_POST["prenom"][$i]) || empty($_POST["sexe"][$i]) || empty($_POST["date_naissance"][$i]) || empty($_POST["num_tel"][$i]) || empty($emailMembres[$i])) {
                    // Vérification supplémentaire pour le membre principal
                    if ($i === 0 && (empty($adresse) || empty($codePostal) || empty($ville) || empty($nomPrenomTelUrgence))) {
                        $error = "Veuillez remplir tous les champs obligatoires (y compris l'adresse et l'urgence) pour le participant principal.";
                    } else {
                        $error = "Veuillez remplir tous les champs obligatoires pour le participant n°" . ($i + 1) . ".";
                    }
                    break; // Sortir du foreach si erreur
                }


                // Calcul de l'âge et du montant unitaire
                try {
                    $dateNaissance = \DateTime::createFromFormat('Y-m-d', $_POST["date_naissance"][$i]);
                    if (!$dateNaissance) {
                        throw new \Exception('Date de naissance invalide.');
                    }

                    $diff = $dateNaissance->diff(new \DateTime());
                    $age = $diff->y;

                    $numLicence = $_POST["num_licence"][$i] ?? null;
                    $estLicencie = !empty($numLicence);

                    $prixInscriptionMoins18AnsLicencieCentimes = (float) $circuitRandonnee->getPrixInscriptionMoins18AnsLicencieCentimes();
                    $prixInscriptionMoins18AnsLicencie = (int) round($prixInscriptionMoins18AnsLicencieCentimes / 100);
                    $prixInscriptionMoins18AnsNonLicencieCentimes = (float) $circuitRandonnee->getPrixInscriptionMoins18AnsNonLicencieCentimes();
                    $prixInscriptionMoins18AnsNonLicencie = (int) round($prixInscriptionMoins18AnsNonLicencieCentimes / 100);
                    $prixInscriptionAdulteLicencieCentimes = (float) $circuitRandonnee->getPrixInscriptionAdulteLicencieCentimes();
                    $prixInscriptionAdulteLicencie = (int) round($prixInscriptionAdulteLicencieCentimes / 100);
                    $prixInscriptionAdulteNonLicencieCentimes = (float) $circuitRandonnee->getPrixInscriptionAdulteNonLicencieCentimes();
                    $prixInscriptionAdulteNonLicencie = (int) round($prixInscriptionAdulteNonLicencieCentimes / 100);

                    // Logique de tarif (exemple : licencié vs non-licencié, adulte vs mineur)
                    if ($age < 18) {
                        $montantUnitaire = $estLicencie ? $prixInscriptionMoins18AnsLicencie : $prixInscriptionMoins18AnsNonLicencie; // Moins de 18 ans
                    } else {
                        $montantUnitaire = $estLicencie ? $prixInscriptionAdulteLicencie : $prixInscriptionAdulteNonLicencie; // Adulte
                    }

                    $montantTotal += $montantUnitaire;

                    // Détermination du statut de paiement initial
                    $statutPaiement = ($montantUnitaire > 0) ? 'ATTENTE_PAIEMENT' : 'PAYE';

                    // Préparation des données du membre
                    $membresInscrits[] = [
                        'nom' => $nom,
                        'prenom' => $_POST["prenom"][$i],
                        'sexe' => $_POST["sexe"][$i],
                        'dateNaissance' => $dateNaissance,
                        'numTel' => $_POST["num_tel"][$i],
                        'email' => $emailMembres[$i],
                        'licenceFfveloClub' => $_POST["licence_ffvelo_club"][$i] ?? null,
                        'numLicence' => $numLicence,
                        'autreFederationClub' => $_POST["autre_federation_club"][$i] ?? null,
                        'montant' => $montantUnitaire,
                        'isPrincipal' => ($i === 0),
                        'numeroInscription' => $numeroInscription,
                        'statutPaiement' => $statutPaiement,
                    ];

                } catch (\Exception $e) {
                    $error = "Erreur de validation pour un participant : " . $e->getMessage();
                    break; // Sortir du foreach si erreur
                }
            } // Fin du foreach

            // Si aucune erreur de validation PHP n'est trouvée ($error est null)
            if ($error === null && !empty($membresInscrits)) {

                // 3. ENREGISTREMENT DES INSCRIPTIONS EN BASE DE DONNÉES
                $inscriptionPrincipaleId = null;

                foreach ($membresInscrits as $membre) {
                    try {
                        // Instanciation de l'objet métier de création (hypothétique service)
                        // Remplacement par le nouveau nom d'entité/service
                        $creerInscription = new CreerInscriptionRouteRandonnee($this->entityManager);

                        // Exécution de la création de l'entité
                        // NOTE: Vous devez ajuster la méthode 'execute' de votre service pour accepter 
                        //       le CircuitRandonnee, le montant et le statut de paiement.
                        $inscriptionRouteRandonnee = $creerInscription->execute(
                            $membre['nom'],
                            $membre['prenom'],
                            $membre['sexe'],
                            $membre['dateNaissance'],
                            ($membre['isPrincipal'] ? $adresse : null), // Adresse unique (nullable)
                            ($membre['isPrincipal'] ? $codePostal : null), // CP unique (nullable)
                            ($membre['isPrincipal'] ? $ville : null), // Ville unique (nullable)
                            $membre['numTel'],
                            $membre['email'],
                            $nomPrenomTelUrgence, // Urgence unique
                            $membre['licenceFfveloClub'],
                            $membre['numLicence'],
                            $membre['autreFederationClub'],
                            $membre['numeroInscription'],
                            $circuitRandonnee, // L'entité CircuitRandonnee (le parcours)
                            $membre['montant'], // Le montant unitaire à enregistrer dans montantPaye
                            $membre['statutPaiement'] // Statut initial (PAYE si montant=0, ATTENTE_PAIEMENT sinon)
                        );

                        // On récupère l'ID de la première inscription pour Stripe
                        if ($inscriptionPrincipaleId === null) {
                            $inscriptionPrincipaleId = $inscriptionRouteRandonnee->getId();
                        }

                    } catch (\Exception $e) {
                        // Gestion d'une erreur d'enregistrement
                        $error = "Erreur lors de l'enregistrement en base de données pour " . $membre['nom'] . ": " . $e->getMessage();
                        break;
                    }
                } // Fin de la boucle d'enregistrement

                // 4. PRÉPARATION DU PAIEMENT STRIPE
                if ($error === null && $montantTotal > 0 && $inscriptionPrincipaleId !== null) {
                    try {
                        // Assurez-vous d'avoir bien initialisé la librairie Stripe
                        // \Stripe\Stripe::setApiKey($this->settings->getStripeSecretKey()); 
                        \Stripe\Stripe::setApiKey("");

                        $session = \Stripe\Checkout\Session::create([
                            'payment_method_types' => ['card'],
                            'line_items' => [
                                [
                                    'price_data' => [
                                        'currency' => 'eur',
                                        'product_data' => [
                                            'name' => 'Inscription Randonnée ROUTE (' . count($membresInscrits) . ' participant(s))',
                                        ],
                                        // Utilisation du montant total calculé, en centimes
                                        'unit_amount' => (int) ($montantTotal * 100),
                                    ],
                                    'quantity' => 1,
                                ],
                            ],
                            'mode' => 'payment',
                            // Attention: Utiliser des URLs dynamiques
                            'success_url' => 'https://avva39.fr/randonnee/' . $slugRandonnee . '/succes-paiement?session_id={CHECKOUT_SESSION_ID}',
                            'cancel_url' => 'https://avva39.fr/randonnee/' . $slugRandonnee,
                            'client_reference_id' => $inscriptionPrincipaleId, // ID de la première inscription (payeur)
                            'metadata' => [
                                'numero_inscription_groupe' => $numeroInscription,
                                'type_circuit' => 'ROUTE',
                                'circuit_id' => $id
                            ],
                        ]);

                        // Redirection vers l'URL de paiement Stripe
                        $this->redirect($session->url);
                        return;

                    } catch (\Exception $e) {
                        $error = "Erreur lors de la création de la session de paiement Stripe: " . $e->getMessage();
                        // NOTE: En cas d'erreur Stripe, vous devriez idéalement supprimer les inscriptions créées 
                        // qui sont restées au statut 'ATTENTE_PAIEMENT'.
                    }
                } elseif ($montantTotal === 0.0 && $error === null) {
                    // Cas d'une inscription totalement gratuite (Ex: tous mineurs licenciés)
                    session_start();
                    $_SESSION['success_message'] = "Votre inscription gratuite a été enregistrée avec succès sous le numéro " . $numeroInscription . ".";
                    $this->redirect('/page/randos');
                    return;
                }
            } // Fin if ($error === null)
        } // Fin if ($_SERVER["REQUEST_METHOD"] === "POST")

        // --- 5. AFFICHAGE DU TEMPLATE ---

        // Si la requête n'est pas POST, ou si une erreur est survenue, afficher le formulaire
        $this->render('pages/randonnees/inscription-route', [
            'index' => $index,
            'indexPage' => true,
            'randonnee' => $randonnee, // Passer la randonnée pour le titre
            'circuitRandonnee' => $circuitRandonnee,
            'error' => $error,
            'nombreVisite' => $nombreVisite,
            'settings' => $settings,
            'pages' => $pages,
            'pageAPropos' => $pageAPropos,
            'pageStatus' => $pageStatus,
            'pagePresentation' => $pagePresentation,

            // Données de Sortie
            'sorties' => $sortiesAffichees, // On passe la collection complète
            'messageApresSortieHebdomadaire' => $messageApresSortieHebdomadaire,
            'messageSortieHebdomadaireADefinir' => $messageSortieHebdomadaireADefinir,
        ]);
    }

    public function creerInscriptionPedestre(string $slugRandonnee, int $id): void
    {
        // --- 1. INITIALISATION & VÉRIFICATION DU CONTEXTE ---

        // --- 0. CONFIGURATION ET TEMPS ACTUEL ---
        $fuseauHoraire = new \DateTimeZone('Europe/Paris');
        $now = new \DateTime('now', $fuseauHoraire);

        // --- 1. STATISTIQUES ET RÉGLAGES ---
        $this->addUniqueIP();
        $nombreVisite = $this->getUniqueVisitor();
        $this->addUniqueIPMonthly();
        $settings = $this->entityManager->getRepository(Reglage::class)->find(1);

        $pages = $this->entityManager->getRepository(Page::class)->findBy([], ['ordrePageAccueil' => 'ASC']);

        $pageAPropos = $this->entityManager->getRepository(PageAPropos::class)->find(1);
        $pageStatus = $this->entityManager->getRepository(PageStatus::class)->find(1);
        $pagePresentation = $this->entityManager->getRepository(PagePresentation::class)->find(1);

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

        // Récupérer la Randonnée et le Parcours spécifiques
        $randonnee = $this->entityManager->getRepository(Randonnee::class)->findOneBy(['slug' => $slugRandonnee]);
        $circuitRandonnee = $this->entityManager->getRepository(CircuitRandonnee::class)->find($id);

        // Vérification de l'existence des entités (Sécurité)
        if (!$randonnee || !$circuitRandonnee) {
            session_start();
            $_SESSION['error_message'] = 'Randonnée ou circuit introuvable.';
            $this->redirect('/page/randos');
            return;
        }

        // Date limite d'inscription (Utilisation de la date de la randonnée pour le calcul)
        // La date limite est 7 jours avant la randonnée, par exemple
        $dateLimiteInscription = (clone $randonnee->getDateRandonnee())->modify('-7 days');

        // Vérification de la date limite
        if ((new \DateTime()) > $dateLimiteInscription) {
            session_start();
            $_SESSION['error_message'] = 'Les inscriptions en ligne pour cette randonnée sont fermées. Inscription possible sur place le jour J.';
            $this->redirect('/page/randos'); // Redirection vers la page de la randonnée
            return;
        }

        $error = null;
        $index = true;

        // --- 2. TRAITEMENT DU FORMULAIRE (POST) ---

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $nomMembres = $_POST["nom"] ?? [];
            $emailMembres = $_POST["email"] ?? [];
            // Autres tableaux de données...
            // Les champs d'adresse sont uniques pour le premier membre/payeur
            $adresse = $_POST["adresse"] ?? '';
            $codePostal = $_POST["code_postal"] ?? '';
            $ville = $_POST["ville"] ?? '';
            $nomPrenomTelUrgence = $_POST["nom_prenom_tel"] ?? '';

            $membresInscrits = [];
            $montantTotal = 0.0; // Utiliser un float pour le montant

            // Génère un ID de groupe d'inscription unique pour lier tous les participants
            $numeroInscription = uniqid('inscription_');

            // Validation et préparation des données de chaque membre
            foreach ($nomMembres as $i => $nom) {

                // Validation basique des champs obligatoires (ajustez selon votre validation front-end/back-end)
                if (empty($nom) || empty($_POST["prenom"][$i]) || empty($_POST["sexe"][$i]) || empty($_POST["date_naissance"][$i]) || empty($_POST["num_tel"][$i]) || empty($emailMembres[$i])) {
                    // Vérification supplémentaire pour le membre principal
                    if ($i === 0 && (empty($adresse) || empty($codePostal) || empty($ville) || empty($nomPrenomTelUrgence))) {
                        $error = "Veuillez remplir tous les champs obligatoires (y compris l'adresse et l'urgence) pour le participant principal.";
                    } else {
                        $error = "Veuillez remplir tous les champs obligatoires pour le participant n°" . ($i + 1) . ".";
                    }
                    break; // Sortir du foreach si erreur
                }


                // Calcul de l'âge et du montant unitaire
                try {
                    $dateNaissance = \DateTime::createFromFormat('Y-m-d', $_POST["date_naissance"][$i]);
                    if (!$dateNaissance) {
                        throw new \Exception('Date de naissance invalide.');
                    }

                    $diff = $dateNaissance->diff(new \DateTime());
                    $age = $diff->y;

                    $numLicence = $_POST["num_licence"][$i] ?? null;
                    $estLicencie = !empty($numLicence);

                    $prixInscriptionMoins18AnsLicencieCentimes = (float) $circuitRandonnee->getPrixInscriptionMoins18AnsLicencieCentimes();
                    $prixInscriptionMoins18AnsLicencie = (int) round($prixInscriptionMoins18AnsLicencieCentimes / 100);
                    $prixInscriptionMoins18AnsNonLicencieCentimes = (float) $circuitRandonnee->getPrixInscriptionMoins18AnsNonLicencieCentimes();
                    $prixInscriptionMoins18AnsNonLicencie = (int) round($prixInscriptionMoins18AnsNonLicencieCentimes / 100);
                    $prixInscriptionAdulteLicencieCentimes = (float) $circuitRandonnee->getPrixInscriptionAdulteLicencieCentimes();
                    $prixInscriptionAdulteLicencie = (int) round($prixInscriptionAdulteLicencieCentimes / 100);
                    $prixInscriptionAdulteNonLicencieCentimes = (float) $circuitRandonnee->getPrixInscriptionAdulteNonLicencieCentimes();
                    $prixInscriptionAdulteNonLicencie = (int) round($prixInscriptionAdulteNonLicencieCentimes / 100);

                    // Logique de tarif (exemple : licencié vs non-licencié, adulte vs mineur)
                    if ($age < 18) {
                        $montantUnitaire = $estLicencie ? $prixInscriptionMoins18AnsLicencie : $prixInscriptionMoins18AnsNonLicencie; // Moins de 18 ans
                    } else {
                        $montantUnitaire = $estLicencie ? $prixInscriptionAdulteLicencie : $prixInscriptionAdulteNonLicencie; // Adulte
                    }

                    $montantTotal += $montantUnitaire;

                    // Détermination du statut de paiement initial
                    $statutPaiement = ($montantUnitaire > 0) ? 'ATTENTE_PAIEMENT' : 'PAYE';

                    // Préparation des données du membre
                    $membresInscrits[] = [
                        'nom' => $nom,
                        'prenom' => $_POST["prenom"][$i],
                        'sexe' => $_POST["sexe"][$i],
                        'dateNaissance' => $dateNaissance,
                        'numTel' => $_POST["num_tel"][$i],
                        'email' => $emailMembres[$i],
                        'licenceFfveloClub' => $_POST["licence_ffvelo_club"][$i] ?? null,
                        'numLicence' => $numLicence,
                        'autreFederationClub' => $_POST["autre_federation_club"][$i] ?? null,
                        'montant' => $montantUnitaire,
                        'isPrincipal' => ($i === 0),
                        'numeroInscription' => $numeroInscription,
                        'statutPaiement' => $statutPaiement,
                    ];

                } catch (\Exception $e) {
                    $error = "Erreur de validation pour un participant : " . $e->getMessage();
                    break; // Sortir du foreach si erreur
                }
            } // Fin du foreach

            // Si aucune erreur de validation PHP n'est trouvée ($error est null)
            if ($error === null && !empty($membresInscrits)) {

                // 3. ENREGISTREMENT DES INSCRIPTIONS EN BASE DE DONNÉES
                $inscriptionPrincipaleId = null;

                foreach ($membresInscrits as $membre) {
                    try {
                        // Instanciation de l'objet métier de création (hypothétique service)
                        // Remplacement par le nouveau nom d'entité/service
                        $creerInscription = new CreerInscriptionPedestreRandonnee($this->entityManager);

                        // Exécution de la création de l'entité
                        // NOTE: Vous devez ajuster la méthode 'execute' de votre service pour accepter 
                        //       le CircuitRandonnee, le montant et le statut de paiement.
                        $inscriptionPedestreRandonnee = $creerInscription->execute(
                            $membre['nom'],
                            $membre['prenom'],
                            $membre['sexe'],
                            $membre['dateNaissance'],
                            ($membre['isPrincipal'] ? $adresse : null), // Adresse unique (nullable)
                            ($membre['isPrincipal'] ? $codePostal : null), // CP unique (nullable)
                            ($membre['isPrincipal'] ? $ville : null), // Ville unique (nullable)
                            $membre['numTel'],
                            $membre['email'],
                            $nomPrenomTelUrgence, // Urgence unique
                            $membre['licenceFfveloClub'],
                            $membre['numLicence'],
                            $membre['autreFederationClub'],
                            $membre['numeroInscription'],
                            $circuitRandonnee, // L'entité CircuitRandonnee (le parcours)
                            $membre['montant'], // Le montant unitaire à enregistrer dans montantPaye
                            $membre['statutPaiement'] // Statut initial (PAYE si montant=0, ATTENTE_PAIEMENT sinon)
                        );

                        // On récupère l'ID de la première inscription pour Stripe
                        if ($inscriptionPrincipaleId === null) {
                            $inscriptionPrincipaleId = $inscriptionPedestreRandonnee->getId();
                        }

                    } catch (\Exception $e) {
                        // Gestion d'une erreur d'enregistrement
                        $error = "Erreur lors de l'enregistrement en base de données pour " . $membre['nom'] . ": " . $e->getMessage();
                        break;
                    }
                } // Fin de la boucle d'enregistrement

                // 4. PRÉPARATION DU PAIEMENT STRIPE
                if ($error === null && $montantTotal > 0 && $inscriptionPrincipaleId !== null) {
                    try {
                        // Assurez-vous d'avoir bien initialisé la librairie Stripe
                        // \Stripe\Stripe::setApiKey($this->settings->getStripeSecretKey()); 
                        \Stripe\Stripe::setApiKey("");

                        $session = \Stripe\Checkout\Session::create([
                            'payment_method_types' => ['card'],
                            'line_items' => [
                                [
                                    'price_data' => [
                                        'currency' => 'eur',
                                        'product_data' => [
                                            'name' => 'Inscription Randonnée PÉDESTRE (' . count($membresInscrits) . ' participant(s))',
                                        ],
                                        // Utilisation du montant total calculé, en centimes
                                        'unit_amount' => (int) ($montantTotal * 100),
                                    ],
                                    'quantity' => 1,
                                ],
                            ],
                            'mode' => 'payment',
                            // Attention: Utiliser des URLs dynamiques
                            'success_url' => 'https://avva39.fr/randonnee/' . $slugRandonnee . '/succes-paiement?session_id={CHECKOUT_SESSION_ID}',
                            'cancel_url' => 'https://avva39.fr/randonnee/' . $slugRandonnee,
                            'client_reference_id' => $inscriptionPrincipaleId, // ID de la première inscription (payeur)
                            'metadata' => [
                                'numero_inscription_groupe' => $numeroInscription,
                                'type_circuit' => 'PÉDESTRE',
                                'session_id' => $id
                            ],
                        ]);

                        // Redirection vers l'URL de paiement Stripe
                        $this->redirect($session->url);
                        return;

                    } catch (\Exception $e) {
                        $error = "Erreur lors de la création de la session de paiement Stripe: " . $e->getMessage();
                        // NOTE: En cas d'erreur Stripe, vous devriez idéalement supprimer les inscriptions créées 
                        // qui sont restées au statut 'ATTENTE_PAIEMENT'.
                    }
                } elseif ($montantTotal === 0.0 && $error === null) {
                    // Cas d'une inscription totalement gratuite (Ex: tous mineurs licenciés)
                    session_start();
                    $_SESSION['success_message'] = "Votre inscription gratuite a été enregistrée avec succès sous le numéro " . $numeroInscription . ".";
                    $this->redirect('/page/randos');
                    return;
                }
            } // Fin if ($error === null)
        } // Fin if ($_SERVER["REQUEST_METHOD"] === "POST")

        // --- 5. AFFICHAGE DU TEMPLATE ---

        // Si la requête n'est pas POST, ou si une erreur est survenue, afficher le formulaire
        $this->render('pages/randonnees/inscription-pedestre', [
            'index' => $index,
            'indexPage' => true,
            'randonnee' => $randonnee, // Passer la randonnée pour le titre
            'circuitRandonnee' => $circuitRandonnee,
            'error' => $error,
            'nombreVisite' => $nombreVisite,
            'settings' => $settings,
            'pages' => $pages,
            'pageAPropos' => $pageAPropos,
            'pageStatus' => $pageStatus,
            'pagePresentation' => $pagePresentation,

            // Données de Sortie
            'sorties' => $sortiesAffichees, // On passe la collection complète
            'messageApresSortieHebdomadaire' => $messageApresSortieHebdomadaire,
            'messageSortieHebdomadaireADefinir' => $messageSortieHebdomadaireADefinir,
        ]);
    }

    // Fichier : src/Controller/PageController.php

    /**
     * Gère la page de succès après un paiement Stripe.
     * Met à jour le statut de paiement des inscriptions concernées.
     */
    public function succesPaiement(): void
    {
        // Initialisation des données de base et des dépendances (conservées)
        $fuseauHoraire = new \DateTimeZone('Europe/Paris');
        $now = new \DateTime('now', $fuseauHoraire);
        $this->addUniqueIP();
        $nombreVisite = $this->getUniqueVisitor();
        $this->addUniqueIPMonthly();
        $settings = $this->entityManager->getRepository(Reglage::class)->find(1);
        $pages = $this->entityManager->getRepository(Page::class)->findBy([], ['ordrePageAccueil' => 'ASC']);
        $pageAPropos = $this->entityManager->getRepository(PageAPropos::class)->find(1);
        $pageStatus = $this->entityManager->getRepository(PageStatus::class)->find(1);
        $pagePresentation = $this->entityManager->getRepository(PagePresentation::class)->find(1);
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

        $sessionId = $_GET['session_id'] ?? null;
        $paymentSuccess = false;
        $error = null;

        if ($sessionId) {
            try {
                // 1. Configuration Stripe (Assurez-vous que la clé est bien définie ici ou globalement)
                // \Stripe\Stripe::setApiKey($settings->getStripeSecretKey());
                \Stripe\Stripe::setApiKey(""); // Clé à remplacer par la variable d'environnement/réglage

                // 2. Récupérer la session Stripe
                $session = \Stripe\Checkout\Session::retrieve($sessionId);

                // 3. Validation de la session
                if ($session->payment_status !== 'paid') {
                    // Si le statut n'est pas 'paid', on lève une exception.
                    // NOTE: Vous pourriez ici ajouter une logique pour les statuts 'unpaid' ou 'requires_payment_method'.
                    throw new \Exception('Le paiement n\'a pas été validé par Stripe. Statut: ' . $session->payment_status);
                }

                // Récupération des métadonnées essentielles
                $clientReferenceId = $session->client_reference_id;
                $numeroInscriptionGroupe = $session->metadata['numero_inscription_groupe'] ?? null;
                $typeCircuit = $session->metadata['type_circuit'] ?? null;

                if (empty($numeroInscriptionGroupe) || empty($typeCircuit)) {
                    throw new \Exception('Métadonnées Stripe manquantes (Numéro de groupe ou Type de circuit). Impossible de mettre à jour les inscriptions.');
                }

                // 4. Déterminer la classe d'entité et la méthode d'envoi d'email
                [$inscriptionEntityClass, $emailSenderMethod] = $this->getCircuitConfig($typeCircuit);

                if (!$inscriptionEntityClass) {
                    throw new \Exception('Type de circuit (' . $typeCircuit . ') non reconnu pour la mise à jour.');
                }

                // 5. Mise à jour des inscriptions (Logique centralisée)

                // Charger les inscriptions liées au NUMÉRO DE GROUPE
                $membres = $this->entityManager->getRepository($inscriptionEntityClass)
                    ->findBy(['numeroInscription' => $numeroInscriptionGroupe]);

                if (empty($membres)) {
                    throw new \Exception('Aucune inscription trouvée pour le numéro de groupe : ' . $numeroInscriptionGroupe);
                }

                $emails = [];

                // Mise à jour du statut pour tous les membres du groupe
                foreach ($membres as $membre) {
                    // Utiliser un statut clair, par exemple 'PAYÉ' ou une constante
                    $membre->setStatutPaiement('PAYÉ');
                    $emails[] = $membre->getEmail();
                }

                $this->entityManager->flush(); // Sauvegarder toutes les modifications

                // 6. Envoi de l'email
                if (method_exists($this, $emailSenderMethod)) {
                    $this->$emailSenderMethod($emails, $membres, $numeroInscriptionGroupe);
                }

                $paymentSuccess = true;

            } catch (\Exception $e) {
                // Gérer toutes les exceptions (Stripe API, BDD, ou logique métier)
                $error = $e->getMessage();
                // Optionnel : Loguer l'erreur détaillée ici
            }
        }

        // 7. Rendu de la vue
        $this->render('pages/randonnees/succes-paiement', [
            'index' => true,
            'indexPage' => true,
            'paymentSuccess' => $paymentSuccess, // Nouveau flag pour l'affichage de la modale
            'error' => $error, // Message d'erreur s'il y a lieu
            'nombreVisite' => $nombreVisite,
            'settings' => $settings,
            'pages' => $pages,
            'pageAPropos' => $pageAPropos,
            'pageStatus' => $pageStatus,
            'pagePresentation' => $pagePresentation,
            'sorties' => $sortiesAffichees, // On passe la collection complète
            'messageApresSortieHebdomadaire' => $messageApresSortieHebdomadaire,
            'messageSortieHebdomadaireADefinir' => $messageSortieHebdomadaireADefinir,
        ]);
    }

    /**
     * Fonction d'aide pour mapper le type de circuit à la classe d'entité et la méthode d'envoi d'email.
     * @param string $typeCircuit Le type récupéré depuis les métadonnées Stripe.
     * @return array{0: ?string, 1: ?string} [Classe Entité, Méthode Email]
     */
    private function getCircuitConfig(string $typeCircuit): array
    {
        // Assurez-vous que les types correspondent à ceux que vous envoyez à Stripe
        return match (strtoupper($typeCircuit)) {
            'VTT' => [InscriptionVTTRandonnee::class, 'envoyerConfirmationInscriptionVTTPaiementParMail'],
            'GRAVEL' => [InscriptionGravelRandonnee::class, 'envoyerConfirmationInscriptionGravelPaiementParMail'],
            'ROUTE' => [InscriptionRouteRandonnee::class, 'envoyerConfirmationInscriptionRoutePaiementParMail'],
            'PÉDESTRE' => [InscriptionPedestreRandonnee::class, 'envoyerConfirmationInscriptionPedestrePaiementParMail'],
            default => [null, null],
        };
    }

    private function envoyerConfirmationInscriptionVTTPaiementParMail(array $emails, array $membres, string $numeroInscription): void
    {
        // Créer une instance de PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configuration du serveur SMTP (par exemple, avec Gmail)
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->CharSet = 'UTF-8';
            $mail->SMTPAuth = true;
            $mail->Username = 'dvmta39@gmail.com'; // Votre adresse email
            $mail->Password = 'pnnikshkztituxfj';    // Votre mot de passe ou mot de passe d'application
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Destinataire et expéditeur
            $mail->setFrom('dvmta39@gmail.com', 'Rando de AVVA39');

            // Ajouter tous les emails des membres
            foreach ($emails as $email) {
                $mail->addAddress($email);  // Ajouter chaque adresse email
            }

            // Sujet et contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = 'Confirmation de l\'inscription à la rando de AVVA39';
            $mail->Body = "Félicitations pour votre inscription à la rando de AVVA39. Voici un récapitulatif de votre inscription.";

            $url = "https://avva39.fr/randonnee/afficher-details-inscription-vtt" . $numeroInscription; // L'URL avec le numéro d'inscription

            // Créer le QR Code pour l'URL de l'inscription
            $builder = new Builder(
                writer: new PngWriter(), // Utiliser le writer pour générer le QR Code (vous pouvez aussi utiliser PngWriter)
                data: $url, // Passer les données du QR Code
                encoding: new Encoding('UTF-8'), // Encodage des données
                errorCorrectionLevel: ErrorCorrectionLevel::High, // Niveau de correction d'erreur
                size: 300, // Taille du QR Code
                margin: 10, // Marge autour du QR Code
                roundBlockSizeMode: RoundBlockSizeMode::Margin, // Arrondir les blocs du QR Code
                logoResizeToWidth: 100, // Redimensionner le logo à 100px de largeur
                logoPunchoutBackground: true, // Utiliser le fond découpé autour du logo
                labelAlignment: LabelAlignment::Center
            ); // Aligner le texte au centre

            // Générer le QR Code avec la configuration du Builder
            $qrCode = $builder->build();

            // Sauvegarder le QR Code dans un fichier temporaire
            $qrCodeFilePath = sys_get_temp_dir() . '/qr_code_' . time() . '.png';
            $qrCode->saveToFile($qrCodeFilePath);

            // Ajouter le fichier le QR Code en pièce jointe
            $mail->addAttachment($qrCodeFilePath, 'QRCode_' . $numeroInscription . '.png'); // Ajouter le QR code

            // Envoyer l'email
            $mail->send();

            // Optionnel : Supprimer les fichiers temporaires après l'envoi
            unlink($qrCodeFilePath);
        } catch (\Exception $e) {
            // Si l'email n'a pas pu être envoyé
            error_log("L'email n'a pas pu être envoyé. Erreur : {$mail->ErrorInfo}");
            throw new \Exception("Erreur lors de l'envoi de l'email.");
        }
    }

    private function envoyerConfirmationInscriptionGravelPaiementParMail(array $emails, array $membres, string $numeroInscription): void
    {
        // Créer une instance de PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configuration du serveur SMTP (par exemple, avec Gmail)
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->CharSet = 'UTF-8';
            $mail->SMTPAuth = true;
            $mail->Username = 'dvmta39@gmail.com'; // Votre adresse email
            $mail->Password = 'pnnikshkztituxfj';    // Votre mot de passe ou mot de passe d'application
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Destinataire et expéditeur
            $mail->setFrom('dvmta39@gmail.com', 'Rando de AVVA39');

            // Ajouter tous les emails des membres
            foreach ($emails as $email) {
                $mail->addAddress($email);  // Ajouter chaque adresse email
            }

            // Sujet et contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = 'Confirmation de l\'inscription à la rando de AVVA39';
            $mail->Body = "Félicitations pour votre inscription à la rando de AVVA39. Voici un récapitulatif de votre inscription.";

            $url = "https://avva39.fr/randonnee/afficher-details-inscription-gravel" . $numeroInscription; // L'URL avec le numéro d'inscription

            // Créer le QR Code pour l'URL de l'inscription
            $builder = new Builder(
                writer: new PngWriter(), // Utiliser le writer pour générer le QR Code (vous pouvez aussi utiliser PngWriter)
                data: $url, // Passer les données du QR Code
                encoding: new Encoding('UTF-8'), // Encodage des données
                errorCorrectionLevel: ErrorCorrectionLevel::High, // Niveau de correction d'erreur
                size: 300, // Taille du QR Code
                margin: 10, // Marge autour du QR Code
                roundBlockSizeMode: RoundBlockSizeMode::Margin, // Arrondir les blocs du QR Code
                logoResizeToWidth: 100, // Redimensionner le logo à 100px de largeur
                logoPunchoutBackground: true, // Utiliser le fond découpé autour du logo
                labelAlignment: LabelAlignment::Center
            ); // Aligner le texte au centre

            // Générer le QR Code avec la configuration du Builder
            $qrCode = $builder->build();

            // Sauvegarder le QR Code dans un fichier temporaire
            $qrCodeFilePath = sys_get_temp_dir() . '/qr_code_' . time() . '.png';
            $qrCode->saveToFile($qrCodeFilePath);

            // Ajouter le fichier le QR Code en pièce jointe
            $mail->addAttachment($qrCodeFilePath, 'QRCode_' . $numeroInscription . '.png'); // Ajouter le QR code

            // Envoyer l'email
            $mail->send();

            // Optionnel : Supprimer les fichiers temporaires après l'envoi
            unlink($qrCodeFilePath);
        } catch (\Exception $e) {
            // Si l'email n'a pas pu être envoyé
            error_log("L'email n'a pas pu être envoyé. Erreur : {$mail->ErrorInfo}");
            throw new \Exception("Erreur lors de l'envoi de l'email.");
        }
    }

    private function envoyerConfirmationInscriptionRoutePaiementParMail(array $emails, array $membres, string $numeroInscription): void
    {
        // Créer une instance de PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configuration du serveur SMTP (par exemple, avec Gmail)
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->CharSet = 'UTF-8';
            $mail->SMTPAuth = true;
            $mail->Username = 'dvmta39@gmail.com'; // Votre adresse email
            $mail->Password = 'pnnikshkztituxfj';    // Votre mot de passe ou mot de passe d'application
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Destinataire et expéditeur
            $mail->setFrom('dvmta39@gmail.com', 'Rando de AVVA39');

            // Ajouter tous les emails des membres
            foreach ($emails as $email) {
                $mail->addAddress($email);  // Ajouter chaque adresse email
            }

            // Sujet et contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = 'Confirmation de l\'inscription à la rando de AVVA39';
            $mail->Body = "Félicitations pour votre inscription à la rando de AVVA39. Voici un récapitulatif de votre inscription.";

            $url = "https://avva39.fr/randonnee/afficher-details-inscription-route" . $numeroInscription; // L'URL avec le numéro d'inscription

            // Créer le QR Code pour l'URL de l'inscription
            $builder = new Builder(
                writer: new PngWriter(), // Utiliser le writer pour générer le QR Code (vous pouvez aussi utiliser PngWriter)
                data: $url, // Passer les données du QR Code
                encoding: new Encoding('UTF-8'), // Encodage des données
                errorCorrectionLevel: ErrorCorrectionLevel::High, // Niveau de correction d'erreur
                size: 300, // Taille du QR Code
                margin: 10, // Marge autour du QR Code
                roundBlockSizeMode: RoundBlockSizeMode::Margin, // Arrondir les blocs du QR Code
                logoResizeToWidth: 100, // Redimensionner le logo à 100px de largeur
                logoPunchoutBackground: true, // Utiliser le fond découpé autour du logo
                labelAlignment: LabelAlignment::Center
            ); // Aligner le texte au centre

            // Générer le QR Code avec la configuration du Builder
            $qrCode = $builder->build();

            // Sauvegarder le QR Code dans un fichier temporaire
            $qrCodeFilePath = sys_get_temp_dir() . '/qr_code_' . time() . '.png';
            $qrCode->saveToFile($qrCodeFilePath);

            // Ajouter le fichier le QR Code en pièce jointe
            $mail->addAttachment($qrCodeFilePath, 'QRCode_' . $numeroInscription . '.png'); // Ajouter le QR code

            // Envoyer l'email
            $mail->send();

            // Optionnel : Supprimer les fichiers temporaires après l'envoi
            unlink($qrCodeFilePath);
        } catch (\Exception $e) {
            // Si l'email n'a pas pu être envoyé
            error_log("L'email n'a pas pu être envoyé. Erreur : {$mail->ErrorInfo}");
            throw new \Exception("Erreur lors de l'envoi de l'email.");
        }
    }

    private function envoyerConfirmationInscriptionPedestrePaiementParMail(array $emails, array $membres, string $numeroInscription): void
    {
        // Créer une instance de PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configuration du serveur SMTP (par exemple, avec Gmail)
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->CharSet = 'UTF-8';
            $mail->SMTPAuth = true;
            $mail->Username = 'dvmta39@gmail.com'; // Votre adresse email
            $mail->Password = 'pnnikshkztituxfj';    // Votre mot de passe ou mot de passe d'application
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Destinataire et expéditeur
            $mail->setFrom('dvmta39@gmail.com', 'Rando de AVVA39');

            // Ajouter tous les emails des membres
            foreach ($emails as $email) {
                $mail->addAddress($email);  // Ajouter chaque adresse email
            }

            // Sujet et contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = 'Confirmation de l\'inscription à la rando de AVVA39';
            $mail->Body = "Félicitations pour votre inscription à la rando de AVVA39. Voici un récapitulatif de votre inscription.";

            $url = "https://avva39.fr/randonnee/afficher-details-inscription-pedestre" . $numeroInscription; // L'URL avec le numéro d'inscription

            // Créer le QR Code pour l'URL de l'inscription
            $builder = new Builder(
                writer: new PngWriter(), // Utiliser le writer pour générer le QR Code (vous pouvez aussi utiliser PngWriter)
                data: $url, // Passer les données du QR Code
                encoding: new Encoding('UTF-8'), // Encodage des données
                errorCorrectionLevel: ErrorCorrectionLevel::High, // Niveau de correction d'erreur
                size: 300, // Taille du QR Code
                margin: 10, // Marge autour du QR Code
                roundBlockSizeMode: RoundBlockSizeMode::Margin, // Arrondir les blocs du QR Code
                logoResizeToWidth: 100, // Redimensionner le logo à 100px de largeur
                logoPunchoutBackground: true, // Utiliser le fond découpé autour du logo
                labelAlignment: LabelAlignment::Center
            ); // Aligner le texte au centre

            // Générer le QR Code avec la configuration du Builder
            $qrCode = $builder->build();

            // Sauvegarder le QR Code dans un fichier temporaire
            $qrCodeFilePath = sys_get_temp_dir() . '/qr_code_' . time() . '.png';
            $qrCode->saveToFile($qrCodeFilePath);

            // Ajouter le fichier le QR Code en pièce jointe
            $mail->addAttachment($qrCodeFilePath, 'QRCode_' . $numeroInscription . '.png'); // Ajouter le QR code

            // Envoyer l'email
            $mail->send();

            // Optionnel : Supprimer les fichiers temporaires après l'envoi
            unlink($qrCodeFilePath);
        } catch (\Exception $e) {
            // Si l'email n'a pas pu être envoyé
            error_log("L'email n'a pas pu être envoyé. Erreur : {$mail->ErrorInfo}");
            throw new \Exception("Erreur lors de l'envoi de l'email.");
        }
    }

    public function afficherDetailsInscritsQrCodeVTT(string $numeroInscription): void
    {
        session_start();

        $active15 = true;

        if (!$this->isUserLoggedIn()) {
            header("Location: /avva-admin/login");
            exit;
        }

        // --- 0. CONFIGURATION ET TEMPS ACTUEL ---
        $fuseauHoraire = new \DateTimeZone('Europe/Paris');
        $now = new \DateTime('now', $fuseauHoraire);

        // --- 1. STATISTIQUES ET RÉGLAGES ---
        $this->addUniqueIP();
        $nombreVisite = $this->getUniqueVisitor();
        $this->addUniqueIPMonthly();
        $settings = $this->entityManager->getRepository(Reglage::class)->find(1);

        $pages = $this->entityManager->getRepository(Page::class)->findBy([], ['ordrePageAccueil' => 'ASC']);

        $pageAPropos = $this->entityManager->getRepository(PageAPropos::class)->find(1);
        $pageStatus = $this->entityManager->getRepository(PageStatus::class)->find(1);
        $pagePresentation = $this->entityManager->getRepository(PagePresentation::class)->find(1);

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

        $afficherDetailsInscrits = $this->entityManager->getRepository(InscriptionVTTRandonnee::class)->findBy(['numeroInscription' => $numeroInscription]);
        foreach ($afficherDetailsInscrits as $afficherDetailsInscritNumero) {
            $afficherDetailsInscritNumero = $afficherDetailsInscritNumero->getNumeroInscription();
        }
        $this->render('pages/randonnees/afficher-details-inscription-vtt', [
            'index' => true,
            'indexPage' => true,
            'active15' => $active15,
            'nombreVisite' => $nombreVisite,
            'settings' => $settings,
            'pages' => $pages,
            'pageAPropos' => $pageAPropos,
            'pageStatus' => $pageStatus,
            'pagePresentation' => $pagePresentation,

            // Données de Sortie
            'sorties' => $sortiesAffichees, // On passe la collection complète
            'messageApresSortieHebdomadaire' => $messageApresSortieHebdomadaire,
            'messageSortieHebdomadaireADefinir' => $messageSortieHebdomadaireADefinir,
            'afficherDetailsInscrits' => $afficherDetailsInscrits,
            'afficherDetailsInscritNumero' => $afficherDetailsInscritNumero
        ]);
    }

    public function afficherDetailsInscritsQrCodeGravel(string $numeroInscription): void
    {
        session_start();

        $active16 = true;

        if (!$this->isUserLoggedIn()) {
            header("Location: /avva-admin/login");
            exit;
        }

        // --- 0. CONFIGURATION ET TEMPS ACTUEL ---
        $fuseauHoraire = new \DateTimeZone('Europe/Paris');
        $now = new \DateTime('now', $fuseauHoraire);

        // --- 1. STATISTIQUES ET RÉGLAGES ---
        $this->addUniqueIP();
        $nombreVisite = $this->getUniqueVisitor();
        $this->addUniqueIPMonthly();
        $settings = $this->entityManager->getRepository(Reglage::class)->find(1);

        $pages = $this->entityManager->getRepository(Page::class)->findBy([], ['ordrePageAccueil' => 'ASC']);

        $pageAPropos = $this->entityManager->getRepository(PageAPropos::class)->find(1);
        $pageStatus = $this->entityManager->getRepository(PageStatus::class)->find(1);
        $pagePresentation = $this->entityManager->getRepository(PagePresentation::class)->find(1);

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

        // // --- 3. GESTION DE LA PROCHAINE SORTIE (Sortie) ---
        // $decompteDepartSortie = $this->getSortieToDisplay($now);
        // $dateSortie = $decompteDepartSortie ? $decompteDepartSortie->getDate()->format('F j, Y H:i:s') : null;

        // if ($decompteDepartSortie) {
        //     $dateSortie = $decompteDepartSortie->getDate()->format('F j, Y H:i:s');
        //     $temps = $decompteDepartSortie->getTemps()->format('H:i');
        //     $message = $decompteDepartSortie->getMessage();
        // }

        $afficherDetailsInscrits = $this->entityManager->getRepository(InscriptionGravelRandonnee::class)->findBy(['numeroInscription' => $numeroInscription]);
        foreach ($afficherDetailsInscrits as $afficherDetailsInscritNumero) {
            $afficherDetailsInscritNumero = $afficherDetailsInscritNumero->getNumeroInscription();
        }
        $this->render('pages/randonnees/afficher-details-inscription-gravel', [
            'index' => true,
            'indexPage' => true,
            'active16' => $active16,
            'nombreVisite' => $nombreVisite,
            'settings' => $settings,
            'pages' => $pages,
            'pageAPropos' => $pageAPropos,
            'pageStatus' => $pageStatus,
            'pagePresentation' => $pagePresentation,

            // Données de Sortie
            'sorties' => $sortiesAffichees, // On passe la collection complète
            'messageApresSortieHebdomadaire' => $messageApresSortieHebdomadaire,
            'messageSortieHebdomadaireADefinir' => $messageSortieHebdomadaireADefinir,
            'afficherDetailsInscrits' => $afficherDetailsInscrits,
            'afficherDetailsInscritNumero' => $afficherDetailsInscritNumero
        ]);
    }

    public function afficherDetailsInscritsQrCodeRoute(string $numeroInscription): void
    {
        session_start();

        $active16 = true;

        if (!$this->isUserLoggedIn()) {
            header("Location: /avva-admin/login");
            exit;
        }

        // --- 0. CONFIGURATION ET TEMPS ACTUEL ---
        $fuseauHoraire = new \DateTimeZone('Europe/Paris');
        $now = new \DateTime('now', $fuseauHoraire);

        // --- 1. STATISTIQUES ET RÉGLAGES ---
        $this->addUniqueIP();
        $nombreVisite = $this->getUniqueVisitor();
        $this->addUniqueIPMonthly();
        $settings = $this->entityManager->getRepository(Reglage::class)->find(1);

        $pages = $this->entityManager->getRepository(Page::class)->findBy([], ['ordrePageAccueil' => 'ASC']);

        $pageAPropos = $this->entityManager->getRepository(PageAPropos::class)->find(1);
        $pageStatus = $this->entityManager->getRepository(PageStatus::class)->find(1);
        $pagePresentation = $this->entityManager->getRepository(PagePresentation::class)->find(1);

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

        $afficherDetailsInscrits = $this->entityManager->getRepository(InscriptionRouteRandonnee::class)->findBy(['numeroInscription' => $numeroInscription]);
        foreach ($afficherDetailsInscrits as $afficherDetailsInscritNumero) {
            $afficherDetailsInscritNumero = $afficherDetailsInscritNumero->getNumeroInscription();
        }
        $this->render('pages/randonnees/afficher-details-inscription-route', [
            'index' => true,
            'indexPage' => true,
            'active16' => $active16,
            'nombreVisite' => $nombreVisite,
            'settings' => $settings,
            'pages' => $pages,
            'pageAPropos' => $pageAPropos,
            'pageStatus' => $pageStatus,
            'pagePresentation' => $pagePresentation,

            // Données de Sortie
            'sorties' => $sortiesAffichees, // On passe la collection complète
            'messageApresSortieHebdomadaire' => $messageApresSortieHebdomadaire,
            'messageSortieHebdomadaireADefinir' => $messageSortieHebdomadaireADefinir,
            'afficherDetailsInscrits' => $afficherDetailsInscrits,
            'afficherDetailsInscritNumero' => $afficherDetailsInscritNumero
        ]);
    }

    public function afficherDetailsInscritsQrCodePedestre(string $numeroInscription): void
    {
        session_start();

        $active16 = true;

        if (!$this->isUserLoggedIn()) {
            header("Location: /avva-admin/login");
            exit;
        }

        // --- 0. CONFIGURATION ET TEMPS ACTUEL ---
        $fuseauHoraire = new \DateTimeZone('Europe/Paris');
        $now = new \DateTime('now', $fuseauHoraire);

        // --- 1. STATISTIQUES ET RÉGLAGES ---
        $this->addUniqueIP();
        $nombreVisite = $this->getUniqueVisitor();
        $this->addUniqueIPMonthly();
        $settings = $this->entityManager->getRepository(Reglage::class)->find(1);

        $pages = $this->entityManager->getRepository(Page::class)->findBy([], ['ordrePageAccueil' => 'ASC']);

        $pageAPropos = $this->entityManager->getRepository(PageAPropos::class)->find(1);
        $pageStatus = $this->entityManager->getRepository(PageStatus::class)->find(1);
        $pagePresentation = $this->entityManager->getRepository(PagePresentation::class)->find(1);

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

        $afficherDetailsInscrits = $this->entityManager->getRepository(InscriptionPedestreRandonnee::class)->findBy(['numeroInscription' => $numeroInscription]);
        foreach ($afficherDetailsInscrits as $afficherDetailsInscritNumero) {
            $afficherDetailsInscritNumero = $afficherDetailsInscritNumero->getNumeroInscription();
        }
        $this->render('pages/randonnees/afficher-details-inscription-pedestre', [
            'index' => true,
            'indexPage' => true,
            'active16' => $active16,
            'nombreVisite' => $nombreVisite,
            'settings' => $settings,
            'pages' => $pages,
            'pageAPropos' => $pageAPropos,
            'pageStatus' => $pageStatus,
            'pagePresentation' => $pagePresentation,

            // Données de Sortie
            'sorties' => $sortiesAffichees, // On passe la collection complète
            'messageApresSortieHebdomadaire' => $messageApresSortieHebdomadaire,
            'messageSortieHebdomadaireADefinir' => $messageSortieHebdomadaireADefinir,
            'afficherDetailsInscrits' => $afficherDetailsInscrits,
            'afficherDetailsInscritNumero' => $afficherDetailsInscritNumero
        ]);
    }

    public function compteRendu(string $pageUrl, int $id): void
    {
        $index = true;
        $indexPage = true;
        $fuseauHoraire = new \DateTimeZone('Europe/Paris');
        $now = new \DateTime('now', $fuseauHoraire);

        // Ajouter l'IP unique
        $this->addUniqueIP();
        $nombreVisite = $this->getUniqueVisitor();
        $this->addUniqueIPMonthly();

        $settings = $this->entityManager->getRepository(Reglage::class)->find(1);

        // Récupérer la page actuelle
        $pageEntity = $this->entityManager->getRepository(Page::class)->findOneBy(['url' => $pageUrl]);
        if (!$pageEntity) {
            throw new \Exception("Page introuvable !");
        }

        // Récupérer les autres pages/entités nécessaires
        $pageAPropos = $this->entityManager->getRepository(PageAPropos::class)->find(1);
        $pageStatus = $this->entityManager->getRepository(PageStatus::class)->find(1);
        $pagePresentation = $this->entityManager->getRepository(PagePresentation::class)->find(1);

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

        // $decompteDepartSortie = null;
        // $dateSortie = null;

        // // Tente de trouver la PROCHAINE sortie (date > NOW)
        // $prochaineSortie = $this->entityManager->getRepository(Sortie::class)->createQueryBuilder('s')
        //     ->where('s.date >= :now')
        //     ->setParameter('now', $now)
        //     ->orderBy('s.date', 'ASC')
        //     ->setMaxResults(1)
        //     ->getQuery()
        //     ->getOneOrNullResult();

        // if ($prochaineSortie) {
        //     // Si une prochaine sortie est trouvée, on l'utilise
        //     $decompteDepartSortie = $prochaineSortie;
        // } else {
        //     // Si aucune prochaine sortie, on cherche la DERNIÈRE sortie passée (date < NOW)
        //     $derniereSortie = $this->entityManager->getRepository(Sortie::class)->createQueryBuilder('s')
        //         ->where('s.date < :now')
        //         ->setParameter('now', $now)
        //         ->orderBy('s.date', 'DESC')
        //         ->setMaxResults(1)
        //         ->getQuery()
        //         ->getOneOrNullResult();

        //     if ($derniereSortie) {
        //         // On affiche la dernière sortie qui a eu lieu
        //         $decompteDepartSortie = $derniereSortie;
        //     }
        // }

        // if ($decompteDepartSortie) {
        //     // Formate la date seulement si une sortie a été trouvée
        //     $dateSortie = $decompteDepartSortie->getDate()->format('F j, Y H:i:s');
        //     $temps = $decompteDepartSortie->getTemps()->format('H:i');
        //     $message = $decompteDepartSortie->getMessage();
        // }

        $event = $this->entityManager->getRepository(DateEvent::class)->find($id);

        if (!$event) {
            throw new \Exception("Compte-rendu introuvable");
        }

        $this->render('/pages/compte-rendu', [
            'index' => $index,
            'indexPage' => $indexPage,
            'nombreVisite' => $nombreVisite,
            'settings' => $settings,
            'page' => $pageEntity,
            'pageAPropos' => $pageAPropos,
            'pageStatus' => $pageStatus,
            'pagePresentation' => $pagePresentation,
            'sorties' => $sortiesAffichees, // On passe la collection complète
            'messageApresSortieHebdomadaire' => $messageApresSortieHebdomadaire,
            'messageSortieHebdomadaireADefinir' => $messageSortieHebdomadaireADefinir,
            'event' => $event
        ]);
    }

    public function pageApropos(string $page): void
    {
        $index = true;
        $indexPage = true;
        $fuseauHoraire = new \DateTimeZone('Europe/Paris');
        $now = new \DateTime('now', $fuseauHoraire);

        $this->addUniqueIP();
        $nombreVisite = $this->getUniqueVisitor();
        $this->addUniqueIPMonthly();

        $settings = $this->entityManager->getRepository(Reglage::class)->find(1);

        $pageAPropos = $this->entityManager->getRepository(PageAPropos::class)->findOneBy(['id' => 1]);
        $pageStatus = $this->entityManager->getRepository(PageStatus::class)->findOneBy(['id' => 1]);
        $pagePresentation = $this->entityManager->getRepository(PagePresentation::class)->findOneBy(['id' => 1]);

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

        $this->render('/pages/a-propos', [
            'index' => $index,
            'indexPage' => $indexPage,
            'nombreVisite' => $nombreVisite,
            'settings' => $settings,
            'pageAPropos' => $this->entityManager->getRepository(PageAPropos::class)->findOneBy(['id' => 1]),
            'pageStatus' => $pageStatus,
            'pagePresentation' => $pagePresentation,
            'sorties' => $sortiesAffichees, // On passe la collection complète
            'messageApresSortieHebdomadaire' => $messageApresSortieHebdomadaire,
            'messageSortieHebdomadaireADefinir' => $messageSortieHebdomadaireADefinir,
        ]);
    }

    public function pageStatus(string $page): void
    {
        $index = true;
        $indexPage = true;
        $fuseauHoraire = new \DateTimeZone('Europe/Paris');
        $now = new \DateTime('now', $fuseauHoraire);

        $this->addUniqueIP();
        $nombreVisite = $this->getUniqueVisitor();
        $this->addUniqueIPMonthly();

        $settings = $this->entityManager->getRepository(Reglage::class)->find(1);

        $pageAPropos = $this->entityManager->getRepository(PageAPropos::class)->findOneBy(['id' => 1]);
        $pageStatus = $this->entityManager->getRepository(PageStatus::class)->findOneBy(['id' => 1]);
        $pagePresentation = $this->entityManager->getRepository(PagePresentation::class)->findOneBy(['id' => 1]);

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

        $this->render('/pages/status', [
            'index' => $index,
            'indexPage' => $indexPage,
            'nombreVisite' => $nombreVisite,
            'settings' => $settings,
            'pageAPropos' => $pageAPropos,
            'pageStatus' => $this->entityManager->getRepository(PageStatus::class)->findOneBy(['id' => 1]),
            'pagePresentation' => $pagePresentation,
            'sorties' => $sortiesAffichees, // On passe la collection complète
            'messageApresSortieHebdomadaire' => $messageApresSortieHebdomadaire,
            'messageSortieHebdomadaireADefinir' => $messageSortieHebdomadaireADefinir,
        ]);
    }

    public function pagePresentation(string $page): void
    {
        $index = true;
        $indexPage = true;
        $fuseauHoraire = new \DateTimeZone('Europe/Paris');
        $now = new \DateTime('now', $fuseauHoraire);

        $this->addUniqueIP();
        $nombreVisite = $this->getUniqueVisitor();
        $this->addUniqueIPMonthly();

        $settings = $this->entityManager->getRepository(Reglage::class)->find(1);

        $pageAPropos = $this->entityManager->getRepository(PageAPropos::class)->findOneBy(['id' => 1]);
        $pageStatus = $this->entityManager->getRepository(PageStatus::class)->findOneBy(['id' => 1]);
        $pagePresentation = $this->entityManager->getRepository(PagePresentation::class)->findOneBy(['id' => 1]);

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

        $this->render('/pages/presentation', [
            'index' => $index,
            'indexPage' => $indexPage,
            'nombreVisite' => $nombreVisite,
            'settings' => $settings,
            'pageAPropos' => $pageAPropos,
            'pageStatus' => $pageStatus,
            'pagePresentation' => $this->entityManager->getRepository(PagePresentation::class)->findOneBy(['id' => 1]),
            'sorties' => $sortiesAffichees, // On passe la collection complète
            'messageApresSortieHebdomadaire' => $messageApresSortieHebdomadaire,
            'messageSortieHebdomadaireADefinir' => $messageSortieHebdomadaireADefinir,
        ]);
    }

    public function index(): void
    {
        session_start();

        $active4 = true;

        // Vérifier si l'utilisateur est connecté
        if (!$this->isUserLoggedIn()) {
            header("Location: /avva-admin/login");
            exit;
        }

        // 1. Récupérer toutes les pages, triées par ordre
        $pages = $this->entityManager->getRepository(Page::class)->findBy([], ['ordrePageAccueil' => 'ASC']);

        // 2. Séparer les pages en deux groupes : Gauche et Droite
        $pages_gauche = [];
        $pages_droite = [];

        // L'ordre maximum est utilisé pour les boutons Monter/Descendre.
        // Nous utilisons le count total car l'ordre est global.
        $max_order = count($pages);

        foreach ($pages as $page) {
            // Obtenir le nom de la disposition, en minuscules pour la comparaison
            $disposition_nom = strtolower($page->getDispositionPageAccueil()->getNom() ?? '');

            // Supposons que '1' ou 'gauche' correspond à la disposition de gauche
            if ($disposition_nom === '1' || $disposition_nom === 'gauche') {
                $pages_gauche[] = $page;
            }
            // Supposons que '2' ou 'droite' correspond à la disposition de droite
            elseif ($disposition_nom === '2' || $disposition_nom === 'droite') {
                $pages_droite[] = $page;
            }
            // Les pages avec d'autres dispositions ou sans sont ignorées ou peuvent être ajoutées à un troisième tableau si nécessaire.
        }

        // 3. Rendre la vue en passant les deux tableaux séparés et l'ordre maximum
        $this->render('/admin/pages/liste-page', [
            'user' => $_SESSION['user'],
            'active4' => $active4,
            'pages' => $pages,
            'pages_gauche' => $pages_gauche, // Nouveau
            'pages_droite' => $pages_droite, // Nouveau
            'max_order' => $max_order        // Nouveau
        ]);
    }

    /**
     * Gère le changement d'ordre d'une page (monter ou descendre).
     * * @param string $action 'monter' ou 'descendre'
     * @param int $id ID de la page à déplacer
     */
    public function changerOrdre(string $action, int $id): void
    {
        session_start();

        if (!$this->isUserLoggedIn()) {
            header("Location: /avva-admin/login");
            exit;
        }

        $pageCourante = $this->entityManager->getRepository(Page::class)->find($id);

        if (!$pageCourante) {
            $_SESSION['error_message'] = "Page non trouvée.";
            header("Location: /avva-admin/page/liste");
            exit;
        }

        $ordreActuel = $pageCourante->getOrdrePageAccueil();
        $nouvelOrdre = $ordreActuel;

        // Déterminer l'ordre cible pour l'échange
        if ($action === 'monter') {
            $nouvelOrdre = $ordreActuel - 1;
        } elseif ($action === 'descendre') {
            $nouvelOrdre = $ordreActuel + 1;
        } else {
            $_SESSION['error_message'] = "Action non valide.";
            header("Location: /avva-admin/page/liste");
            exit;
        }

        // 1. Chercher la page à échanger
        $pageAEchanger = $this->entityManager->getRepository(Page::class)->findOneBy(['ordrePageAccueil' => $nouvelOrdre]);

        if (!$pageAEchanger) {
            $_SESSION['error_message'] = "Impossible de déplacer la page (limite atteinte ou ordre inexistant).";
            header("Location: /avva-admin/page/liste");
            exit;
        }

        try {
            // 2. Échanger les ordres
            $pageCourante->setOrdrePageAccueil($nouvelOrdre);
            $pageAEchanger->setOrdrePageAccueil($ordreActuel);

            // 3. Persister les changements
            $this->entityManager->persist($pageCourante);
            $this->entityManager->persist($pageAEchanger);
            $this->entityManager->flush();

            $_SESSION['success_message'] = "L'ordre de la page a été mis à jour avec succès.";

        } catch (\Exception $e) {
            // Gérer les erreurs de base de données (collision d'ordre, etc.)
            $_SESSION['error_message'] = "Une erreur est survenue lors de la mise à jour de l'ordre : " . $e->getMessage();
        }

        header("Location: /avva-admin/page/liste");
        exit;
    }

    public function creerPage(): void
    {
        session_start();

        $active5 = true;

        // Vérifier si l'utilisateur est connecté
        if (!$this->isUserLoggedIn()) {
            header("Location: /avva-admin/login");
            exit;
        }

        $user = $_SESSION['user']; // Utilisateur stocké dans la session
        $error = ''; // Initialiser la variable d'erreur

        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        // Récupérer toutes les dispositions pour le formulaire
        $dispositionsAccueil = $this->entityManager->getRepository(DispositionPageAccueil::class)->findAll();

        // Initialisation des variables pour le formulaire (inutile si le formulaire est masqué)
        $nom = '';
        $url = '';
        $contenu = '';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nom = $_POST["nom_page"];
            $url = $_POST['url_page'];
            $contenu = $_POST['contenu_page'];

            // Récupérer l'objet DispositionPageAccueil
            $dispositionId = $_POST['disposition_page_accueil'];
            $disposition = $this->entityManager->getRepository(DispositionPageAccueil::class)->find($dispositionId);

            // --- NOUVELLE LOGIQUE DE CALCUL DE L'ORDRE ---

            if (!$disposition) {
                $error = "Disposition de page invalide.";
            } else {
                // 1. Trouver l'ordre maximum actuel pour TOUTES les pages (car l'ordre est global)
                // C'est la méthode la plus sûre pour s'assurer que l'ordre est unique et séquentiel.
                $query = $this->entityManager->createQuery(
                    'SELECT MAX(p.ordrePageAccueil) FROM App\Entity\Page p'
                );
                $maxOrdre = $query->getSingleScalarResult();

                // 2. Définir le nouvel ordre : le maximum actuel + 1
                $ordreAccueil = ($maxOrdre === null) ? 1 : $maxOrdre + 1;

                // NOTE: Si l'ordre était censé être séquentiel PAR DISPOSITION,
                // la requête devrait être : 'SELECT MAX(p.ordrePageAccueil) FROM Page p WHERE p.dispositionPageAccueil = :dispoId'
                // et $ordreAccueil serait calculé uniquement pour ce groupe.
                // Je me base sur votre implémentation de déplacement qui utilise un ordre global.
            }

            // --- FIN LOGIQUE DE CALCUL DE L'ORDRE ---

            function modifierUrl($url)
            {
                $url = str_replace(' ', '-', $url); // Supprimer les espaces et les remplacer par un tiret
                $url = preg_replace('/[^A-Za-z0-9\-]/', '', $url); // Retirer les caractères spéciaux et les accents
                return $url;
            }

            if (empty($error)) {
                try {
                    // $url est modifié dans la fonction, pas utilisé ici, mais on l'appelle au cas où.
                    $urlFinal = modifierUrl($url);

                    // Utiliser la variable $disposition (objet) et $ordreAccueil (calculé)
                    $creerPage = new CreerPage($this->entityManager);
                    $page = $creerPage->execute($nom, $urlFinal, $contenu, $ordreAccueil, $disposition);

                    $_SESSION['success_message'] = "Page créé avec succès !";
                    $this->redirect("/avva-admin/page/liste");
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                }
            }
        }

        // Pages n'est plus utile dans la vue si on ne l'affiche pas
        $this->render('/admin/pages/creer-page', [
            'user' => $_SESSION['user'],
            'active5' => $active5,
            'pages' => $pages,
            'dispositionsAccueil' => $dispositionsAccueil,
            'error' => $error,
            'utilisateur' => $_SESSION['utilisateur'] ?? null,
        ]);
    }

    public function modifierPage(int $id): void
    {
        session_start();

        if ($id == 6) {
            $active10 = true;
        } elseif ($id == 7) {
            $active11 = true;
        } else {
            $active6 = true;
        }

        // Vérifier si l'utilisateur est connecté
        if (!$this->isUserLoggedIn()) {
            header("Location: /admin/login");
            exit;
        }

        $user = $_SESSION['user'];

        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        $page = $this->entityManager->getRepository(Page::class)->find($id);

        $dispositionsPageAccueil = $this->entityManager->getRepository(DispositionPageAccueil::class)->findAll();

        $datesEvent = $this->entityManager->getRepository(DateEvent::class)->findAll();

        $categoriesEvent = $this->entityManager->getRepository(CategorieEvent::class)->findAll();

        $medias = $this->entityManager->getRepository(PhotoVideo::class)->findAll();

        $fichiersPdf = $this->entityManager->getRepository(PageCommentAdhererPdf::class)->findAll();

        $contenusAssocies = $this->entityManager->getRepository(ContenuPage::class)->findBy(['page' => $page], ['ordre' => 'ASC']);

        $ordreMaximum = count($contenusAssocies);

        if (!$page) {
            $_SESSION['error_message'] = "Page introuvable.";
            $this->redirect("/avva-admin/page/liste");
            exit();
        }

        $modifierPage = new ModifierPage($this->entityManager);

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                // 1. Récupération et validation rapide des données textuelles
                $nom = $_POST['nom_page'] ?? null;
                $url = $_POST['url_page'] ?? null;
                $contenu = $_POST['contenu_page'] ?? null;
                $dispositionId = $_POST['disposition_page_accueil'] ?? null;

                if (empty($nom) || empty($url) || empty($contenu) || empty($dispositionId)) {
                    throw new \Exception("Tous les champs doivent être remplis.");
                }

                $dispositionPageAccueil = $this->entityManager->getRepository(DispositionPageAccueil::class)->find($dispositionId);
                if (!$dispositionPageAccueil) {
                    throw new \Exception("La disposition sélectionnée est invalide.");
                }

                // 2. Configuration de l'Uploader
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/medias/';
                if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
                    throw new \Exception("Impossible de créer le répertoire d'upload.");
                }
                $uploader = new UploaderService($uploadDir);

                // 3. Traitement des fichiers (Boucle pour éviter la répétition)
                $images = ['gauche' => null, 'droite' => null];
                $champsFichiers = [
                    'gauche' => 'fichier_media_gauche',
                    'droite' => 'fichier_media_droite'
                ];

                foreach ($champsFichiers as $cle => $nomChamp) {
                    if (!isset($_FILES[$nomChamp]) || $_FILES[$nomChamp]['error'] !== UPLOAD_ERR_OK) {
                        throw new \Exception("Le fichier pour le côté {$cle} est manquant ou corrompu.");
                    }

                    $cheminFichier = $uploader->upload($_FILES[$nomChamp]);

                    if (!$cheminFichier) {
                        throw new \Exception("Erreur lors de l'upload de l'image {$cle}.");
                    }

                    $images[$cle] = $cheminFichier;
                }

                // 4. Exécution de la modification
                $modifierPage->execute(
                    $page->getId(),
                    $nom,
                    $url,
                    $contenu,
                    $dispositionPageAccueil,
                    $images['gauche'],
                    $images['droite']
                );

                $_SESSION['success_message'] = "La page \"{$nom}\" a été modifiée avec succès.";
                $this->redirect("/avva-admin/page/liste");
                exit();

            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        $events = [];

        foreach ($datesEvent as $dateEvent) {
            $events[] = [
                'id' => $dateEvent->getId(),
                'title' => $dateEvent->getTitre(),
                'description' => $dateEvent->getDescription(),
                'start' => $dateEvent->getDateStart()->format('Y-m-d\TH:i:s'),
                'end' => $dateEvent->getDateEnd() ? $dateEvent->getDateEnd()->format('Y-m-d\TH:i:s') : null,
                'categorieId' => $dateEvent->getCategorieEvent()?->getId() ?? null,
                'categorieName' => $dateEvent->getCategorieEvent()?->getNom() ?? '',
                'compteRendu' => $dateEvent->getCompteRendu() ?? '',
                'gpxFilePath' => $dateEvent->getGpxFilePath() ?? ''
            ];
        }

        $this->render('/admin/pages/modifier-page', [
            'user' => $user,
            'active6' => $active6,
            'active10' => $active10,
            'active11' => $active11,
            'pages' => $pages,
            'error' => $error ?? '',
            'utilisateur' => $_SESSION['utilisateur'] ?? null,
            'page' => $page,
            'contenus' => $contenusAssocies,
            'ordreMaximum' => $ordreMaximum,
            'dispositionsPageAccueil' => $dispositionsPageAccueil,
            'dateEvent' => $dateEvent,
            'categoriesEvent' => $categoriesEvent,
            'events' => json_encode($events), // passer au JS du calendrier
            'medias' => $medias,
            'fichiersPdf' => $fichiersPdf
        ]);
    }

    public function changerOrdreContenuPage(string $action, int $id): void
    {
        session_start();

        if (!$this->isUserLoggedIn()) {
            header("Location: /avva-admin/login");
            exit;
        }

        $contenuPageCourante = $this->entityManager->getRepository(ContenuPage::class)->find($id);

        if (!$contenuPageCourante) {
            $_SESSION['error_message'] = "Page non trouvée.";
            header("Location: /avva-admin/page/liste");
            exit;
        }

        $ordreActuel = $contenuPageCourante->getOrdre();
        $nouvelOrdre = $ordreActuel;

        // Déterminer l'ordre cible pour l'échange
        if ($action === 'monter') {
            $nouvelOrdre = $ordreActuel - 1;
        } elseif ($action === 'descendre') {
            $nouvelOrdre = $ordreActuel + 1;
        } else {
            $_SESSION['error_message'] = "Action non valide.";
            header("Location: /avva-admin/page/liste");
            exit;
        }

        // 1. Chercher la page à échanger
        $contenuPageAEchanger = $this->entityManager->getRepository(ContenuPage::class)->findOneBy(['ordre' => $nouvelOrdre]);

        if (!$contenuPageAEchanger) {
            $_SESSION['error_message'] = "Impossible de déplacer la page (limite atteinte ou ordre inexistant).";
            header("Location: /avva-admin/page/liste");
            exit;
        }

        try {
            // 2. Échanger les ordres
            $contenuPageCourante->setOrdre($nouvelOrdre);
            $contenuPageAEchanger->setOrdre($ordreActuel);

            // 3. Persister les changements
            $this->entityManager->persist($contenuPageCourante);
            $this->entityManager->persist($contenuPageAEchanger);
            $this->entityManager->flush();

            $_SESSION['success_message'] = "L'ordre du contenu de la page a été mis à jour avec succès.";

        } catch (\Exception $e) {
            // Gérer les erreurs de base de données (collision d'ordre, etc.)
            $_SESSION['error_message'] = "Une erreur est survenue lors de la mise à jour de l'ordre : " . $e->getMessage();
        }

        header("Location: /avva-admin/page/modifier/" . $contenuPageCourante->getPage()->getId());
        exit;
    }

    public function InsererContenuPageTexte(int $id): void
    {
        session_start();

        if ($id == 6) {
            $active10 = true;
        } elseif ($id == 7) {
            $active11 = true;
        } else {
            $active6 = true;
        }

        // Vérifier si l'utilisateur est connecté
        if (!$this->isUserLoggedIn()) {
            header("Location: /admin/login");
            exit;
        }

        $user = $_SESSION['user'];

        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        $page = $this->entityManager->getRepository(Page::class)->find($id);

        $dispositionsPageAccueil = $this->entityManager->getRepository(DispositionPageAccueil::class)->findAll();

        $datesEvent = $this->entityManager->getRepository(DateEvent::class)->findAll();

        $categoriesEvent = $this->entityManager->getRepository(CategorieEvent::class)->findAll();

        $medias = $this->entityManager->getRepository(PhotoVideo::class)->findAll();

        $fichiersPdf = $this->entityManager->getRepository(PageCommentAdhererPdf::class)->findAll();

        $contenusAssocies = $this->entityManager->getRepository(ContenuPage::class)->findBy(['page' => $page], ['ordre' => 'ASC']);

        if (!$page) {
            $_SESSION['error_message'] = "Page introuvable.";
            $this->redirect("/avva-admin/page/liste");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                $nom = $_POST['nom_contenu_page'];
                $texte = $_POST['texte_contenu_page'];

                if (empty($nom) || empty($texte)) {
                    throw new \Exception("Tous les champs doivent être remplis.");
                }

                $contenuPageTexte = new ContenuPage();
                $contenuPageTexte->setNom($nom);
                $contenuPageTexte->setTexte($texte);
                $contenuPageTexte->setPage($page);
                $maxOrdre = $this->findMaxOrdre();
                $contenuPageTexte->setOrdre($maxOrdre + 1);

                $this->entityManager->persist($contenuPageTexte);
                $this->entityManager->flush();

                $_SESSION['success_message'] = "La page " . $nom . " a été modifiée avec succès";
                $this->redirect("/avva-admin/page/liste");
                exit();
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        $events = [];

        foreach ($datesEvent as $dateEvent) {
            $events[] = [
                'id' => $dateEvent->getId(),
                'title' => $dateEvent->getTitre(),
                'description' => $dateEvent->getDescription(),
                'start' => $dateEvent->getDateStart()->format('Y-m-d\TH:i:s'),
                'end' => $dateEvent->getDateEnd() ? $dateEvent->getDateEnd()->format('Y-m-d\TH:i:s') : null,
                'categorieId' => $dateEvent->getCategorieEvent()?->getId() ?? null,
                'categorieName' => $dateEvent->getCategorieEvent()?->getNom() ?? '',
                'compteRendu' => $dateEvent->getCompteRendu() ?? '',
                'gpxFilePath' => $dateEvent->getGpxFilePath() ?? ''
            ];
        }

        $this->render('/admin/pages/inserer-contenu-page-texte', [
            'user' => $user,
            'active6' => $active6,
            'active10' => $active10,
            'active11' => $active11,
            'pages' => $pages,
            'error' => $error ?? '',
            'utilisateur' => $_SESSION['utilisateur'] ?? null,
            'page' => $page,
            'contenus' => $contenusAssocies,
            'dispositionsPageAccueil' => $dispositionsPageAccueil,
            'dateEvent' => $dateEvent,
            'categoriesEvent' => $categoriesEvent,
            'events' => json_encode($events), // passer au JS du calendrier
            'medias' => $medias,
            'fichiersPdf' => $fichiersPdf
        ]);
    }

    public function InsererContenuPageImage(int $id): void
    {
        session_start();

        if ($id == 6) {
            $active10 = true;
        } elseif ($id == 7) {
            $active11 = true;
        } else {
            $active6 = true;
        }

        // Vérifier si l'utilisateur est connecté
        if (!$this->isUserLoggedIn()) {
            header("Location: /admin/login");
            exit;
        }

        $user = $_SESSION['user'];

        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        $page = $this->entityManager->getRepository(Page::class)->find($id);

        $dispositionsPageAccueil = $this->entityManager->getRepository(DispositionPageAccueil::class)->findAll();

        $datesEvent = $this->entityManager->getRepository(DateEvent::class)->findAll();

        $categoriesEvent = $this->entityManager->getRepository(CategorieEvent::class)->findAll();

        $medias = $this->entityManager->getRepository(PhotoVideo::class)->findAll();

        $fichiersPdf = $this->entityManager->getRepository(PageCommentAdhererPdf::class)->findAll();

        $contenusAssocies = $this->entityManager->getRepository(ContenuPage::class)->findBy(['page' => $page], ['ordre' => 'ASC']);

        if (!$page) {
            $_SESSION['error_message'] = "Page introuvable.";
            $this->redirect("/avva-admin/page/liste");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nom = $_POST['nom_contenu_page'];
            $typeMedia = 'image';

            try {
                // Validation basique
                if (empty($nom)) {
                    throw new \Exception("Le nom du média est obligatoire.");
                }

                $mediaPath = null;
                $mediaTypeToSave = null;

                // --- Logique d'Upload de Fichier (Image OU Vidéo) ---
                if (in_array($typeMedia, ['image'])) {

                    // Vérification du fichier
                    if (!isset($_FILES["fichier_media"]) || $_FILES["fichier_media"]["error"] !== UPLOAD_ERR_OK) {
                        // IMPORTANT : Pour l'upload, le fichier DOIT être là
                        throw new \Exception("Veuillez sélectionner un fichier valide pour l'upload.");
                    }

                    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/medias/';
                    // Assurez-vous que le répertoire d'upload existe
                    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
                        // Utiliser error_get_last() pour plus de détails si mkdir échoue
                        throw new \Exception("Impossible de créer le répertoire d'upload : " . error_get_last()['message'] ?? 'Erreur inconnue.');
                    }

                    // Assurez-vous que UploaderService est bien défini pour gérer le chemin
                    $uploader = new UploaderService($uploadDir);

                    $mediaPath = $uploader->upload($_FILES["fichier_media"]);
                    $mediaTypeToSave = 'image';

                } else {
                    throw new \Exception("Type de média sélectionné ('{$typeMedia}') invalide.");
                }

                // Un media path est obligatoire à ce stade
                if (empty($mediaPath)) {
                    throw new \Exception("Une erreur inattendue est survenue, le chemin du média est vide.");
                }

                $contenuPageImage = new ContenuPage();
                $contenuPageImage->setNom($nom);
                $contenuPageImage->setImage($mediaPath);
                $contenuPageImage->setPage($page);
                $maxOrdre = $this->findMaxOrdre();
                $contenuPageImage->setOrdre($maxOrdre + 1);

                $this->entityManager->persist($contenuPageImage);
                $this->entityManager->flush();

                $_SESSION['success_message'] = "La page " . $nom . " a été modifiée avec succès";
                $this->redirect("/avva-admin/page/liste");
                exit();
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        $events = [];

        foreach ($datesEvent as $dateEvent) {
            $events[] = [
                'id' => $dateEvent->getId(),
                'title' => $dateEvent->getTitre(),
                'description' => $dateEvent->getDescription(),
                'start' => $dateEvent->getDateStart()->format('Y-m-d\TH:i:s'),
                'end' => $dateEvent->getDateEnd() ? $dateEvent->getDateEnd()->format('Y-m-d\TH:i:s') : null,
                'categorieId' => $dateEvent->getCategorieEvent()?->getId() ?? null,
                'categorieName' => $dateEvent->getCategorieEvent()?->getNom() ?? '',
                'compteRendu' => $dateEvent->getCompteRendu() ?? '',
                'gpxFilePath' => $dateEvent->getGpxFilePath() ?? ''
            ];
        }

        $this->render('/admin/pages/inserer-contenu-page-image', [
            'user' => $user,
            'active6' => $active6,
            'active10' => $active10,
            'active11' => $active11,
            'pages' => $pages,
            'error' => $error ?? '',
            'utilisateur' => $_SESSION['utilisateur'] ?? null,
            'page' => $page,
            'contenus' => $contenusAssocies,
            'dispositionsPageAccueil' => $dispositionsPageAccueil,
            'dateEvent' => $dateEvent,
            'categoriesEvent' => $categoriesEvent,
            'events' => json_encode($events), // passer au JS du calendrier
            'medias' => $medias,
            'fichiersPdf' => $fichiersPdf
        ]);
    }

    public function InsererContenuPageVideo(int $id): void
    {
        session_start();

        if ($id == 6) {
            $active10 = true;
        } elseif ($id == 7) {
            $active11 = true;
        } else {
            $active6 = true;
        }

        // Vérifier si l'utilisateur est connecté
        if (!$this->isUserLoggedIn()) {
            header("Location: /admin/login");
            exit;
        }

        $user = $_SESSION['user'];

        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        $page = $this->entityManager->getRepository(Page::class)->find($id);

        $dispositionsPageAccueil = $this->entityManager->getRepository(DispositionPageAccueil::class)->findAll();

        $datesEvent = $this->entityManager->getRepository(DateEvent::class)->findAll();

        $categoriesEvent = $this->entityManager->getRepository(CategorieEvent::class)->findAll();

        $medias = $this->entityManager->getRepository(PhotoVideo::class)->findAll();

        $fichiersPdf = $this->entityManager->getRepository(PageCommentAdhererPdf::class)->findAll();

        $contenusAssocies = $this->entityManager->getRepository(ContenuPage::class)->findBy(['page' => $page], ['ordre' => 'ASC']);

        if (!$page) {
            $_SESSION['error_message'] = "Page introuvable.";
            $this->redirect("/avva-admin/page/liste");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nom = $_POST['nom_contenu_page'];
            $typeMedia = 'video';

            try {
                // Validation basique
                if (empty($nom)) {
                    throw new \Exception("Le nom du média est obligatoire.");
                }

                $mediaPath = null;
                $mediaTypeToSave = null;

                // --- Logique d'Upload de Fichier (Image OU Vidéo) ---
                if (in_array($typeMedia, ['video'])) {

                    // Vérification du fichier
                    if (!isset($_FILES["fichier_media"]) || $_FILES["fichier_media"]["error"] !== UPLOAD_ERR_OK) {
                        // IMPORTANT : Pour l'upload, le fichier DOIT être là
                        throw new \Exception("Veuillez sélectionner un fichier valide pour l'upload.");
                    }

                    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/medias/';
                    // Assurez-vous que le répertoire d'upload existe
                    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
                        // Utiliser error_get_last() pour plus de détails si mkdir échoue
                        throw new \Exception("Impossible de créer le répertoire d'upload : " . error_get_last()['message'] ?? 'Erreur inconnue.');
                    }

                    // Assurez-vous que UploaderService est bien défini pour gérer le chemin
                    $uploader = new UploaderService($uploadDir);

                    $mediaPath = $uploader->upload($_FILES["fichier_media"]);
                    $mediaTypeToSave = 'video';

                } else {
                    throw new \Exception("Type de média sélectionné ('{$typeMedia}') invalide.");
                }

                // Un media path est obligatoire à ce stade
                if (empty($mediaPath)) {
                    throw new \Exception("Une erreur inattendue est survenue, le chemin du média est vide.");
                }

                $contenuPageVideo = new ContenuPage();
                $contenuPageVideo->setNom($nom);
                $contenuPageVideo->setVideo($mediaPath);
                $contenuPageVideo->setPage($page);
                $maxOrdre = $this->findMaxOrdre();
                $contenuPageVideo->setOrdre($maxOrdre + 1);

                $this->entityManager->persist($contenuPageVideo);
                $this->entityManager->flush();

                $_SESSION['success_message'] = "La page " . $nom . " a été modifiée avec succès";
                $this->redirect("/avva-admin/page/liste");
                exit();
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        $events = [];

        foreach ($datesEvent as $dateEvent) {
            $events[] = [
                'id' => $dateEvent->getId(),
                'title' => $dateEvent->getTitre(),
                'description' => $dateEvent->getDescription(),
                'start' => $dateEvent->getDateStart()->format('Y-m-d\TH:i:s'),
                'end' => $dateEvent->getDateEnd() ? $dateEvent->getDateEnd()->format('Y-m-d\TH:i:s') : null,
                'categorieId' => $dateEvent->getCategorieEvent()?->getId() ?? null,
                'categorieName' => $dateEvent->getCategorieEvent()?->getNom() ?? '',
                'compteRendu' => $dateEvent->getCompteRendu() ?? '',
                'gpxFilePath' => $dateEvent->getGpxFilePath() ?? ''
            ];
        }

        $this->render('/admin/pages/inserer-contenu-page-video', [
            'user' => $user,
            'active6' => $active6,
            'active10' => $active10,
            'active11' => $active11,
            'pages' => $pages,
            'error' => $error ?? '',
            'utilisateur' => $_SESSION['utilisateur'] ?? null,
            'page' => $page,
            'contenus' => $contenusAssocies,
            'dispositionsPageAccueil' => $dispositionsPageAccueil,
            'dateEvent' => $dateEvent,
            'categoriesEvent' => $categoriesEvent,
            'events' => json_encode($events), // passer au JS du calendrier
            'medias' => $medias,
            'fichiersPdf' => $fichiersPdf
        ]);
    }

    public function InsererContenuPagePdf(int $id): void
    {
        session_start();

        // Gestion des menus actifs
        $active10 = ($id == 6);
        $active11 = ($id == 7);
        $active6 = (!in_array($id, [6, 7]));

        if (!$this->isUserLoggedIn()) {
            header("Location: /admin/login");
            exit;
        }

        $user = $_SESSION['user'];

        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        $page = $this->entityManager->getRepository(Page::class)->find($id);

        $dispositionsPageAccueil = $this->entityManager->getRepository(DispositionPageAccueil::class)->findAll();

        $datesEvent = $this->entityManager->getRepository(DateEvent::class)->findAll();

        $categoriesEvent = $this->entityManager->getRepository(CategorieEvent::class)->findAll();

        $medias = $this->entityManager->getRepository(PhotoVideo::class)->findAll();

        $fichiersPdf = $this->entityManager->getRepository(PageCommentAdhererPdf::class)->findAll();

        $contenusAssocies = $this->entityManager->getRepository(ContenuPage::class)->findBy(['page' => $page], ['ordre' => 'ASC']);

        if (!$page) {
            $_SESSION['error_message'] = "Page introuvable.";
            $this->redirect("/avva-admin/page/liste");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nom = $_POST['nom_contenu_page'];

            try {
                if (empty($nom)) {
                    throw new \Exception("Le nom du document est obligatoire.");
                }

                $pdfPath = null;

                // --- Logique d'Upload de Fichier PDF ---
                if (isset($_FILES["fichier_media"]) && $_FILES["fichier_media"]["error"] === UPLOAD_ERR_OK) {

                    // Vérification de l'extension par sécurité
                    $extension = pathinfo($_FILES["fichier_media"]["name"], PATHINFO_EXTENSION);
                    if (strtolower($extension) !== 'pdf') {
                        throw new \Exception("Seuls les fichiers PDF sont autorisés.");
                    }

                    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/medias/';
                    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
                        throw new \Exception("Erreur lors de la création du dossier documents.");
                    }

                    $uploader = new UploaderService($uploadDir);
                    $pdfPath = $uploader->upload($_FILES["fichier_media"]);
                } else {
                    throw new \Exception("Veuillez sélectionner un fichier PDF valide.");
                }

                if (!$pdfPath) {
                    throw new \Exception("Le transfert du fichier a échoué.");
                }

                // Création du contenu
                $contenuPagePdf = new ContenuPage();
                $contenuPagePdf->setNom($nom);

                // Note : Vérifiez si votre entité possède setPdf() ou setFichier()
                // J'utilise setPdf() ici par logique de suite
                $contenuPagePdf->setPdf($pdfPath);

                $contenuPagePdf->setPage($page);
                $maxOrdre = $this->findMaxOrdre();
                $contenuPagePdf->setOrdre($maxOrdre + 1);

                $this->entityManager->persist($contenuPagePdf);
                $this->entityManager->flush();

                $_SESSION['success_message'] = "Le document PDF '" . $nom . "' a été ajouté avec succès.";
                $this->redirect("/avva-admin/page/liste");
                exit();

            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        $events = [];

        foreach ($datesEvent as $dateEvent) {
            $events[] = [
                'id' => $dateEvent->getId(),
                'title' => $dateEvent->getTitre(),
                'description' => $dateEvent->getDescription(),
                'start' => $dateEvent->getDateStart()->format('Y-m-d\TH:i:s'),
                'end' => $dateEvent->getDateEnd() ? $dateEvent->getDateEnd()->format('Y-m-d\TH:i:s') : null,
                'categorieId' => $dateEvent->getCategorieEvent()?->getId() ?? null,
                'categorieName' => $dateEvent->getCategorieEvent()?->getNom() ?? '',
                'compteRendu' => $dateEvent->getCompteRendu() ?? '',
                'gpxFilePath' => $dateEvent->getGpxFilePath() ?? ''
            ];
        }

        $this->render('/admin/pages/inserer-contenu-page-pdf', [
            'user' => $user,
            'active6' => $active6,
            'active10' => $active10,
            'active11' => $active11,
            'pages' => $pages,
            'error' => $error ?? '',
            'page' => $page,
            'contenus' => $contenusAssocies,
            'dispositionsPageAccueil' => $dispositionsPageAccueil,
            'categoriesEvent' => $categoriesEvent,
            'events' => json_encode($events),
            'medias' => $medias,
            'fichiersPdf' => $fichiersPdf
        ]);
    }

    public function modifierContenuPageTexte(int $id): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Sécurité & Authentification
        if (!$this->isUserLoggedIn()) {
            $this->redirect("/admin/login");
            return;
        }

        // 2. Récupération de l'existant
        $contenu = $this->entityManager->getRepository(ContenuPage::class)->find($id);

        if (!$contenu) {
            $_SESSION['error_message'] = "Le contenu à modifier est introuvable.";
            $this->redirect("/avva-admin/page/liste");
            return;
        }

        // 3. Traitement du formulaire (POST)
        $error = null;
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                $nom = trim($_POST['nom_contenu_page'] ?? '');
                $texte = trim($_POST['texte_contenu_page'] ?? '');

                if (empty($nom) || empty($texte)) {
                    throw new \Exception("Le nom et le texte sont obligatoires.");
                }

                // Mise à jour de l'objet existant
                $contenu->setNom($nom);
                $contenu->setTexte($texte);

                $this->entityManager->flush();

                $_SESSION['success_message'] = "Le contenu '{$nom}' a été mis à jour avec succès.";
                $this->redirect("/avva-admin/page/liste");
                return;

            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        // 4. Gestion des états de menu (Active tabs)
        $activeFlags = [
            'active6' => !in_array($id, [6, 7]),
            'active10' => ($id === 6),
            'active11' => ($id === 7),
        ];

        // 5. Rendu de la vue
        // On ne passe que le strict nécessaire pour l'édition
        $this->render('/admin/pages/modifier-contenu-page-texte', array_merge($activeFlags, [
            'user' => $_SESSION['user'] ?? null,
            'contenu' => $contenu,
            'error' => $error
        ]));
    }

    public function modifierContenuPageImage(int $id): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Authentification
        if (!$this->isUserLoggedIn()) {
            $this->redirect("/admin/login");
            return;
        }

        // 2. Récupération du contenu existant
        $contenu = $this->entityManager->getRepository(ContenuPage::class)->find($id);

        if (!$contenu) {
            $_SESSION['error_message'] = "Contenu introuvable.";
            $this->redirect("/avva-admin/page/liste");
            return;
        }

        $error = null;

        // 3. Traitement du formulaire
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                $nom = trim($_POST['nom_contenu_page'] ?? '');

                if (empty($nom)) {
                    throw new \Exception("Le nom du contenu est obligatoire.");
                }

                // Gestion de l'image
                if (isset($_FILES["fichier_media"]) && $_FILES["fichier_media"]["error"] === UPLOAD_ERR_OK) {

                    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/medias/';

                    // On prépare l'uploader
                    $uploader = new UploaderService($uploadDir);
                    $nouveauPath = $uploader->upload($_FILES["fichier_media"]);

                    if ($nouveauPath) {
                        // Supprimer l'ancienne image physiquement si elle existe
                        $ancienFichier = $_SERVER['DOCUMENT_ROOT'] . $contenu->getImage();
                        if (!empty($contenu->getImage()) && file_exists($ancienFichier) && is_file($ancienFichier)) {
                            unlink($ancienFichier);
                        }

                        // Mettre à jour le nouveau chemin
                        $contenu->setImage($nouveauPath);
                    }
                }

                // Mise à jour des autres champs
                $contenu->setNom($nom);

                $this->entityManager->flush();

                $_SESSION['success_message'] = "Le contenu image '{$nom}' a été mis à jour.";
                $this->redirect("/avva-admin/page/liste");
                return;

            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        // 4. Flags menu
        $activeFlags = [
            'active6' => !in_array($id, [6, 7]),
            'active10' => ($id === 6),
            'active11' => ($id === 7),
        ];

        // 5. Rendu
        $this->render('/admin/pages/modifier-contenu-page-image', array_merge($activeFlags, [
            'user' => $_SESSION['user'] ?? null,
            'contenu' => $contenu,
            'error' => $error
        ]));
    }

    public function modifierContenuPageVideo(int $id): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Authentification
        if (!$this->isUserLoggedIn()) {
            $this->redirect("/admin/login");
            return;
        }

        // 2. Récupération du contenu
        $contenu = $this->entityManager->getRepository(ContenuPage::class)->find($id);

        if (!$contenu) {
            $_SESSION['error_message'] = "Vidéo introuvable.";
            $this->redirect("/avva-admin/page/liste");
            return;
        }

        $error = null;

        // 3. Traitement POST
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                $nom = trim($_POST['nom_contenu_page'] ?? '');

                if (empty($nom)) {
                    throw new \Exception("Le titre de la vidéo est obligatoire.");
                }

                // Gestion de l'upload vidéo
                if (isset($_FILES["fichier_media"]) && $_FILES["fichier_media"]["error"] === UPLOAD_ERR_OK) {

                    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/medias/';
                    $uploader = new UploaderService($uploadDir);
                    $nouveauPath = $uploader->upload($_FILES["fichier_media"]);

                    if ($nouveauPath) {
                        // Suppression de l'ancienne vidéo physique (si ce n'est pas une URL externe)
                        $ancienChemin = $contenu->getVideo();
                        if (!empty($ancienChemin) && !filter_var($ancienChemin, FILTER_VALIDATE_URL)) {
                            $fichierPhysique = $_SERVER['DOCUMENT_ROOT'] . $ancienChemin;
                            if (file_exists($fichierPhysique) && is_file($fichierPhysique)) {
                                unlink($fichierPhysique);
                            }
                        }
                        $contenu->setVideo($nouveauPath);
                    }
                }

                $contenu->setNom($nom);
                $this->entityManager->flush();

                $_SESSION['success_message'] = "La vidéo '{$nom}' a été mise à jour.";
                $this->redirect("/avva-admin/page/liste");
                return;

            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        // 4. Flags menu actif
        $activeFlags = [
            'active6' => !in_array($id, [6, 7]),
            'active10' => ($id === 6),
            'active11' => ($id === 7),
        ];

        // 5. Rendu
        $this->render('/admin/pages/modifier-contenu-page-video', array_merge($activeFlags, [
            'user' => $_SESSION['user'] ?? null,
            'contenu' => $contenu,
            'error' => $error
        ]));
    }

    public function modifierContenuPagePdf(int $id): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Authentification
        if (!$this->isUserLoggedIn()) {
            $this->redirect("/admin/login");
            return;
        }

        // 2. Récupération du contenu existant (par son ID propre)
        $contenu = $this->entityManager->getRepository(ContenuPage::class)->find($id);

        if (!$contenu) {
            $_SESSION['error_message'] = "Document PDF introuvable.";
            $this->redirect("/avva-admin/page/liste");
            return;
        }

        $error = null;

        // 3. Traitement du formulaire (POST)
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                $nom = trim($_POST['nom_contenu_page'] ?? '');

                if (empty($nom)) {
                    throw new \Exception("Le nom du document est obligatoire.");
                }

                // Gestion de l'upload PDF
                if (isset($_FILES["fichier_media"]) && $_FILES["fichier_media"]["error"] === UPLOAD_ERR_OK) {

                    // Vérification extension
                    $extension = pathinfo($_FILES["fichier_media"]["name"], PATHINFO_EXTENSION);
                    if (strtolower($extension) !== 'pdf') {
                        throw new \Exception("Seuls les fichiers PDF sont acceptés.");
                    }

                    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/medias/';
                    $uploader = new UploaderService($uploadDir);
                    $nouveauPath = $uploader->upload($_FILES["fichier_media"]);

                    if ($nouveauPath) {
                        // Suppression de l'ancien PDF physique
                        $ancienPdf = $_SERVER['DOCUMENT_ROOT'] . $contenu->getPdf();
                        if (!empty($contenu->getPdf()) && file_exists($ancienPdf) && is_file($ancienPdf)) {
                            unlink($ancienPdf);
                        }
                        $contenu->setPdf($nouveauPath);
                    }
                }

                $contenu->setNom($nom);
                $this->entityManager->flush();

                $_SESSION['success_message'] = "Le document '{$nom}' a été mis à jour.";
                $this->redirect("/avva-admin/page/liste");
                return;

            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        // 4. Gestion des menus actifs
        $activeFlags = [
            'active6' => !in_array($id, [6, 7]),
            'active10' => ($id === 6),
            'active11' => ($id === 7),
        ];

        // 5. Rendu
        $this->render('/admin/pages/modifier-contenu-page-pdf', array_merge($activeFlags, [
            'user' => $_SESSION['user'] ?? null,
            'contenu' => $contenu,
            'error' => $error
        ]));
    }

    public function supprimerContenuPage(int $id): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Authentification
        if (!$this->isUserLoggedIn()) {
            $this->redirect("/admin/login");
            return;
        }

        // 2. Récupération de l'entité
        $contenuPage = $this->entityManager->getRepository(ContenuPage::class)->find($id);

        if (!$contenuPage) {
            $_SESSION['error_message'] = "Contenu introuvable.";
            $this->redirect("/avva-admin/page/liste");
            return;
        }

        // 3. Préparation de la liste des fichiers à supprimer physiquement
        // On récupère tout AVANT de supprimer l'objet en BDD
        $fichiersASupprimer = array_filter([
            $contenuPage->getImage(),
            $contenuPage->getVideo(),
            $contenuPage->getPdf()
        ]);

        $nomContenu = $contenuPage->getNom(); // Sauvegarde pour le message de succès

        try {
            // 4. Suppression en Base de données
            $this->entityManager->remove($contenuPage);
            $this->entityManager->flush();

            // 5. Nettoyage du stockage (Fichiers physiques)
            foreach ($fichiersASupprimer as $cheminRelatif) {
                // On ignore les URLs externes (YouTube, etc.)
                if (filter_var($cheminRelatif, FILTER_VALIDATE_URL)) {
                    continue;
                }

                // Construction du chemin absolu sécurisé
                $cheminAbsolu = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . ltrim($cheminRelatif, '/\\');

                if (file_exists($cheminAbsolu) && is_file($cheminAbsolu)) {
                    if (!unlink($cheminAbsolu)) {
                        // Optionnel : Loguer l'échec sans bloquer l'utilisateur
                        error_log("Impossible de supprimer le fichier : " . $cheminAbsolu);
                    }
                }
            }

            $_SESSION['success_message'] = "Le contenu '{$nomContenu}' a été supprimé.";

        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Erreur technique : " . $e->getMessage();
        }

        $this->redirect("/avva-admin/page/liste");
    }

    public function findMaxOrdre(): int
    {
        return (int) $this->entityManager->getRepository(ContenuPage::class)->createQueryBuilder('c')
            ->select('MAX(c.ordre)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function creerPhotoVideo(): void
    {
        // 1. Authentification et Autorisation
        if (!$this->isUserLoggedIn()) {
            // Redirection préférable pour une requête non AJAX, mais on garde le JSON pour l'AJAX.
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                // Logique de redirection standard si non-authentifié en GET
                // $this->redirectToLogin(); 
                return;
            }
            $this->returnJson(['error' => 'Authentification requise.'], 401);
            return;
        }

        // Affichage du formulaire en GET
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->renderFormView();
            return;
        }

        // --- 2. Traitement du Formulaire POST (AJAX) ---

        // Récupération des données POST
        $titre = trim($_POST['titre_media'] ?? '');
        $typeMedia = $_POST['type_media'] ?? 'image'; // 'image', 'video_url', 'video_upload'
        $urlVideo = trim($_POST['url_video'] ?? '');

        try {
            // Validation basique
            if (empty($titre)) {
                throw new \Exception("Le titre du média est obligatoire.");
            }

            $mediaPath = null;
            $mediaTypeToSave = null;

            // --- Logique d'Upload de Fichier (Image OU Vidéo) ---
            if (in_array($typeMedia, ['image', 'video_upload'])) {

                // Vérification du fichier
                if (!isset($_FILES["fichier_media"]) || $_FILES["fichier_media"]["error"] !== UPLOAD_ERR_OK) {
                    // IMPORTANT : Pour l'upload, le fichier DOIT être là
                    throw new \Exception("Veuillez sélectionner un fichier valide pour l'upload.");
                }

                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/medias/';
                // Assurez-vous que le répertoire d'upload existe
                if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
                    // Utiliser error_get_last() pour plus de détails si mkdir échoue
                    throw new \Exception("Impossible de créer le répertoire d'upload : " . error_get_last()['message'] ?? 'Erreur inconnue.');
                }

                // Assurez-vous que UploaderService est bien défini pour gérer le chemin
                $uploader = new UploaderService($uploadDir);

                $mediaPath = $uploader->upload($_FILES["fichier_media"]);
                $mediaTypeToSave = ($typeMedia === 'video_upload') ? 'video' : 'image';

            }
            // --- Logique d'URL Externe (Vidéo uniquement) ---
            else if ($typeMedia === 'video_url') {
                if (empty($urlVideo) || !filter_var($urlVideo, FILTER_VALIDATE_URL)) {
                    throw new \Exception("Veuillez fournir une URL de vidéo valide (YouTube/Vimeo/etc.).");
                }
                $mediaPath = $urlVideo;
                $mediaTypeToSave = 'video';
            } else {
                throw new \Exception("Type de média sélectionné ('{$typeMedia}') invalide.");
            }

            // Un media path est obligatoire à ce stade
            if (empty($mediaPath)) {
                throw new \Exception("Une erreur inattendue est survenue, le chemin du média est vide.");
            }


            // 3. Création et Persistance de l'Entité PhotoVideo
            $photoVideo = new PhotoVideo(); // Assurez-vous que cette classe existe
            $photoVideo->setTitre($titre);
            $photoVideo->setType($mediaTypeToSave);
            $photoVideo->setFichier($mediaPath); // Chemin ou URL
            $photoVideo->setDateAjout(new \DateTime());

            // Assurez-vous que $this->entityManager existe et est correctement configuré (Doctrine/Similaire)
            $this->entityManager->persist($photoVideo);
            $this->entityManager->flush();

            // 4. Succès et Réponse JSON
            $this->returnJson([
                'success' => true,
                'message' => "Média '{$photoVideo->getTitre()}' (#{$photoVideo->getId()}) créé avec succès.",
                'redirect' => '/avva-admin/page/modifier/5' // Mettez l'URL de redirection correcte
            ], 201); // 201 Created

        } catch (\Exception $e) {
            // 5. Gérer les erreurs et Réponse JSON
            $this->returnJson([
                'success' => false,
                'error' => "Erreur lors de la création du média : " . $e->getMessage(),
                // Optionnel : renvoyer les données pour réaffichage, mais plus difficile en AJAX POST
                // 'formData' => ['titre' => $titre, 'type' => $typeMedia, 'url_video' => $urlVideo]
            ], 400); // 400 Bad Request
        }
    }

    // Les méthodes utilitaires restent inchangées

    /**
     * Méthode utilitaire pour rendre la vue du formulaire (appelée en GET).
     */
    private function renderFormView(array $formData = ['titre' => '', 'type' => 'image', 'url_video' => '']): void
    {
        session_start();
        $active6 = true;
        $user = $_SESSION['user'] ?? null;
        $error = $_SESSION['error_message'] ?? '';
        unset($_SESSION['error_message']);

        $this->render('/admin/pages/creer-photo-video', [
            'error' => $error,
            'active6' => $active6,
            'utilisateur' => $user,
            'formData' => $formData,
        ]);
    }

    /**
     * Méthode utilitaire pour retourner une réponse JSON et arrêter l'exécution.
     */
    private function returnJson(array $data, int $statusCode = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit();
    }

    public function modifierPhotoVideo(int $id): void
    {
        session_start();

        $active6 = true;

        // 1. Authentification et Autorisation
        if (!$this->isUserLoggedIn()) {
            $this->redirect("/admin/login");
            return;
        }

        $user = $_SESSION['user'];

        // 2. Récupération de l'entité à modifier
        $photoVideo = $this->entityManager->getRepository(PhotoVideo::class)->find($id);

        if (!$photoVideo) {
            $_SESSION['error_message'] = "Média introuvable.";
            $this->redirect("/admin/medias");
            return;
        }

        $error = '';
        // Données existantes
        $ancienTitre = $photoVideo->getTitre();
        $ancienType = $photoVideo->getType();
        $ancienChemin = $photoVideo->getFichier();

        // Pour pré-remplir le formulaire en cas d'erreur
        $formData = [
            'titre' => $ancienTitre,
            // Le type initial est soit 'image' ou 'video'. Si c'est une vidéo, nous devons déduire si c'est une URL ou un upload.
            // NOTE: Ceci dépend de comment votre vue gère la distinction entre video_url et video_upload.
            'type_form' => ($ancienType === 'image') ? 'image' :
                ((filter_var($ancienChemin, FILTER_VALIDATE_URL) ? 'video_url' : 'video_upload')),
            'url_video' => (filter_var($ancienChemin, FILTER_VALIDATE_URL)) ? $ancienChemin : '',
        ];


        // 3. Traitement du Formulaire POST
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Nettoyage et récupération des données du formulaire
            $formData['titre'] = trim($_POST['titre_media'] ?? '');
            $formData['type_form'] = $_POST['type_media'] ?? $formData['type_form']; // Le type choisi dans le formulaire (image, video_url, video_upload)
            $formData['url_video'] = trim($_POST['url_video'] ?? '');

            try {
                if (empty($formData['titre'])) {
                    throw new \Exception("Le titre du média est obligatoire.");
                }

                $mediaPath = $ancienChemin;
                $nouveauType = null;
                $fichierUpload = isset($_FILES["fichier_media"]) && $_FILES["fichier_media"]["error"] === UPLOAD_ERR_OK;

                // --- Logique de mise à jour ---

                if ($formData['type_form'] === 'image' || $formData['type_form'] === 'video_upload') {

                    // Cas 1: Upload d'un nouveau fichier OU changement vers un type d'upload
                    if ($fichierUpload) {
                        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/medias/';
                        $uploader = new UploaderService($uploadDir);

                        // 1. Upload du nouveau fichier
                        $mediaPath = $uploader->upload($_FILES["fichier_media"]);

                        // 2. Supprimer l'ancien fichier physique (uniquement s'il existait et n'était pas une URL)
                        if ($ancienType !== 'video' || !filter_var($ancienChemin, FILTER_VALIDATE_URL)) {
                            $fichierASupprimer = $_SERVER['DOCUMENT_ROOT'] . $ancienChemin;
                            if ($ancienChemin && file_exists($fichierASupprimer)) {
                                // Tente de supprimer l'ancien fichier
                                @unlink($fichierASupprimer);
                            }
                        }

                        // Définir le nouveau type BDD
                        $nouveauType = ($formData['type_form'] === 'image') ? 'image' : 'video';

                        // Cas 2: Changement de type vers un type d'upload SANS nouveau fichier (impossible/invalide)
                    } else if ($formData['type_form'] !== 'image' && $formData['type_form'] !== 'video_upload' && $ancienType !== 'image' && $ancienType !== 'video') {
                        // Si l'utilisateur passe d'une URL à un upload et n'upload rien, c'est une erreur, mais pour une modification on autorise si c'était déjà un upload.
                        // On garde l'ancien chemin si l'utilisateur ne touche pas au fichier existant et garde le même type.
                        $nouveauType = $ancienType;
                        $mediaPath = $ancienChemin;
                    }
                } else if ($formData['type_form'] === 'video_url') {

                    // Cas 3: Modification vers une URL externe
                    if (empty($formData['url_video']) || !filter_var($formData['url_video'], FILTER_VALIDATE_URL)) {
                        throw new \Exception("Veuillez fournir une URL de vidéo valide.");
                    }

                    // 1. Supprimer l'ancien fichier physique si l'ancien média ÉTAIT un fichier uploadé
                    if ($ancienType === 'image' || ($ancienType === 'video' && !filter_var($ancienChemin, FILTER_VALIDATE_URL))) {
                        $fichierASupprimer = $_SERVER['DOCUMENT_ROOT'] . $ancienChemin;
                        if ($ancienChemin && file_exists($fichierASupprimer)) {
                            @unlink($fichierASupprimer);
                        }
                    }

                    // Définir le nouveau type et chemin
                    $mediaPath = $formData['url_video'];
                    $nouveauType = 'video';
                }

                // Si le type a changé ou le chemin a changé
                $photoVideo->setTitre($formData['titre']);
                $photoVideo->setType($nouveauType ?? $ancienType);
                $photoVideo->setFichier($mediaPath);

                // 4. Persistance de l'Entité PhotoVideo
                $this->entityManager->flush();

                // 5. Succès et Redirection
                $_SESSION['success_message'] = "Média '{$photoVideo->getTitre()}' modifié avec succès !";
                $this->redirect("/avva-admin/page/modifier/5");

            } catch (\Exception $e) {
                $_SESSION['error_message'] = "Erreur lors de la modification du média : " . $e->getMessage();
                $error = $e->getMessage(); // Pour affichage dans la vue

                // Recharger le formulaire avec les données et l'erreur
                $formData['type'] = $formData['type_form'];
            }
        }

        // 6. Affichage du Formulaire (GET ou POST avec Erreur)
        $this->render('/admin/pages/modifier-photo-video', [
            'error' => $error,
            'active6' => $active6,
            'utilisateur' => $user,
            'media' => $photoVideo, // L'entité à jour ou avec les données postées
            'formData' => $formData, // Pour pré-remplir le formulaire
        ]);
    }

    public function supprimerPhotoVideo(int $id): void
    {
        session_start();

        // 1. Authentification et Autorisation
        if (!$this->isUserLoggedIn()) {
            // NOTE: $this->redirect("/admin/login"); est préférable à $this->redirect("admin/login");
            $this->redirect("/admin/login");
            return;
        }

        // 2. Récupération de l'entité à supprimer
        $photoVideo = $this->entityManager->getRepository(PhotoVideo::class)->find($id);

        if (!$photoVideo) {
            $_SESSION['error_message'] = "Média introuvable ou déjà supprimé.";
            $this->redirect("/avva-admin/page/modifier/5"); // Redirection vers la liste
            return;
        }

        $fichierChemin = $photoVideo->getFichier(); // Récupère le chemin (pour les images/vidéos uploadées) ou l'URL (pour les vidéos externes)
        $mediaType = $photoVideo->getType(); // 'image' ou 'video' (inclut les uploads et les URLs)

        try {
            // 3. Suppression de l'entrée dans la base de données
            $this->entityManager->remove($photoVideo);
            $this->entityManager->flush();

            // 4. Suppression du fichier physique (Uniquement si ce n'est PAS une URL externe)
            // Les médias uploadés (images ou vidéos) auront un chemin relatif, pas une URL http/https.

            // On vérifie que c'est un média local AVANT de tenter de manipuler le système de fichiers
            if (!empty($fichierChemin) && $mediaType !== 'video' && !filter_var($fichierChemin, FILTER_VALIDATE_URL)) {

                // Construit le chemin ABSOLU du fichier sur le serveur
                // Assurez-vous que $fichierChemin ne contient pas de '/' initial s'il est relatif au DOCUMENT_ROOT
                $fichierPhysique = $_SERVER['DOCUMENT_ROOT'] . $fichierChemin;

                // Vérification de la présence du fichier
                if (file_exists($fichierPhysique)) {
                    // Tente de supprimer le fichier
                    if (!unlink($fichierPhysique)) {
                        // La BDD est propre, mais on log l'erreur du fichier (ne devrait pas empêcher le succès BDD)
                        // NOTE: Il est préférable de jeter une exception pour loguer, mais de la rattraper SANS annuler le succès BDD.
                        // Pour simplifier ici, on loggue une erreur dans la session et on continue vers le succès.
                        throw new \Exception("Impossible de supprimer le fichier physique: " . $fichierPhysique);
                    }
                }
            }

            // 5. Succès et Redirection
            $_SESSION['success_message'] = "Média '{$photoVideo->getTitre()}' supprimé avec succès.";

        } catch (\Exception $e) {
            // Gère les erreurs (suppression de fichier ou BDD)
            $_SESSION['error_message'] = "Erreur lors de la suppression du média : " . $e->getMessage();
        }

        // Redirection systématique vers la liste des médias
        $this->redirect("/avva-admin/page/modifier/5");
    }

    public function creerPdf(): void
    {
        // 1. Authentification et Autorisation
        if (!$this->isUserLoggedIn()) {
            // Redirection préférable pour une requête non AJAX, mais on garde le JSON pour l'AJAX.
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                // Logique de redirection standard si non-authentifié en GET
                // $this->redirectToLogin(); 
                return;
            }
            $this->returnJson(['error' => 'Authentification requise.'], 401);
            return;
        }

        // Affichage du formulaire en GET
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->renderFormViewPdf();
            return;
        }

        // --- 2. Traitement du Formulaire POST (AJAX) ---

        // Récupération des données POST
        $thematique = $_POST['thematique_pdf'];
        $nom = $_POST['nom_pdf'];
        $description = $_POST['description_pdf'];
        $fichier = $_POST['fichier_pdf'];
        $estAfficher = isset($_POST['est_afficher_pdf']) ? 1 : 0;
        $estTelechargeable = isset($_POST['est_telechargeable_pdf']) ? 1 : 0;

        try {
            // Validation basique
            if (empty($thematique)) {
                throw new \Exception("La thématique du pdf est obligatoire.");
            }

            if (empty($nom)) {
                throw new \Exception("Le nom du pdf est obligatoire.");
            }

            if (empty($description)) {
                throw new \Exception("La description du pdf est obligatoire.");
            }

            $pdfPath = null;

            // Vérification du fichier
            if (!isset($_FILES["fichier_pdf"]) || $_FILES["fichier_pdf"]["error"] !== UPLOAD_ERR_OK) {
                // IMPORTANT : Pour l'upload, le fichier DOIT être là
                throw new \Exception("Veuillez sélectionner un fichier valide pour l'upload.");
            }

            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/page-comment-adherer/pdf/';
            // Assurez-vous que le répertoire d'upload existe
            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
                // Utiliser error_get_last() pour plus de détails si mkdir échoue
                throw new \Exception("Impossible de créer le répertoire d'upload : " . error_get_last()['message'] ?? 'Erreur inconnue.');
            }

            // Assurez-vous que UploaderService est bien défini pour gérer le chemin
            $uploader = new UploaderService($uploadDir);

            $pdfPath = $uploader->uploadPdf($_FILES["fichier_pdf"]);

            // Un media path est obligatoire à ce stade
            if (empty($pdfPath)) {
                throw new \Exception("Une erreur inattendue est survenue, le chemin du média est vide.");
            }


            // 3. Création et Persistance de l'Entité PhotoVideo
            $pdf = new PageCommentAdhererPdf(); // Assurez-vous que cette classe existe
            $pdf->setThematique($thematique);
            $pdf->setNom($nom);
            $pdf->setDescription($description);
            $pdf->setFichier($pdfPath);
            $pdf->setEstAfficher($estAfficher);
            $pdf->setEstTelechargeable($estTelechargeable);

            // Assurez-vous que $this->entityManager existe et est correctement configuré (Doctrine/Similaire)
            $this->entityManager->persist($pdf);
            $this->entityManager->flush();

            // 4. Succès et Réponse JSON
            $_SESSION['success_message'] = "PDF '{$pdf->getNom()}' créé avec succès !";
            $this->redirect("/avva-admin/page/modifier/8");

        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Erreur lors de la création du PDF : " . $e->getMessage();
            $error = $e->getMessage(); // Pour affichage dans la vue
        }
    }

    // Les méthodes utilitaires restent inchangées

    /**
     * Méthode utilitaire pour rendre la vue du formulaire (appelée en GET).
     */
    private function renderFormViewPdf(array $formData = ['thematique' => '', 'nom' => '', 'description' => '']): void
    {
        session_start();
        $active6 = true;
        $user = $_SESSION['user'] ?? null;
        $error = $_SESSION['error_message'] ?? '';
        unset($_SESSION['error_message']);

        $this->render('/admin/pages/creer-pdf', [
            'error' => $error,
            'active6' => $active6,
            'utilisateur' => $user,
            'formData' => $formData,
        ]);
    }

    public function modifierPdf(int $id): void
    {
        session_start();

        $active6 = true;

        // 1. Authentification et Autorisation
        if (!$this->isUserLoggedIn()) {
            $this->redirect("/admin/login");
            return;
        }

        $user = $_SESSION['user'];

        // 2. Récupération de l'entité à modifier
        $pdf = $this->entityManager->getRepository(PageCommentAdhererPdf::class)->find($id);

        $error = '';
        // Données existantes
        $ancienneThematique = $pdf->getThematique();
        $ancienNom = $pdf->getNom();
        $ancienneDescription = $pdf->getDescription();
        $ancienChemin = $pdf->getFichier();
        $estAfficher = $pdf->getEstAfficher();
        $estTelechargeable = $pdf->getEstTelechargeable();

        // Pour pré-remplir le formulaire en cas d'erreur
        $formData = [
            'thematique' => $ancienneThematique,
            'nom' => $ancienNom,
            'description' => $ancienneDescription,
            'estAfficher' => $estAfficher,
            'estTelechargeable' => $estTelechargeable,
        ];


        // 3. Traitement du Formulaire POST
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Nettoyage et récupération des données du formulaire
            $formData['thematique'] = $_POST['thematique_pdf'];
            $formData['nom'] = $_POST['nom_pdf'];
            $formData['description'] = $_POST['description_pdf'];
            $formData['estAfficher'] = isset($_POST['est_afficher_pdf']) ? 1 : 0;
            $formData['estTelechargeable'] = isset($_POST['est_telechargeable_pdf']) ? 1 : 0;

            try {
                if (empty($formData['thematique'])) {
                    throw new \Exception("La thématique du pdf est obligatoire.");
                }

                if (empty($formData['nom'])) {
                    throw new \Exception("Le nom du pdf est obligatoire.");
                }

                if (empty($formData['description'])) {
                    throw new \Exception("La description du pdf est obligatoire.");
                }

                $pdfPath = $ancienChemin;
                $fichierUpload = isset($_FILES["fichier_pdf"]) && $_FILES["fichier_pdf"]["error"] === UPLOAD_ERR_OK;

                // Cas 1: Upload d'un nouveau fichier OU changement vers un type d'upload
                if ($fichierUpload) {
                    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/page-comment-adherer/pdf';
                    $uploader = new UploaderService($uploadDir);

                    // 1. Upload du nouveau fichier
                    $pdfPath = $uploader->uploadPdf($_FILES["fichier_pdf"]);

                    // 2. Supprimer l'ancien fichier physique (uniquement s'il existait et n'était pas une URL)
                    if (!filter_var($ancienChemin, FILTER_VALIDATE_URL)) {
                        $fichierASupprimer = $_SERVER['DOCUMENT_ROOT'] . $ancienChemin;
                        if ($ancienChemin && file_exists($fichierASupprimer)) {
                            // Tente de supprimer l'ancien fichier
                            @unlink($fichierASupprimer);
                        }
                    }

                    // Cas 2: Changement de type vers un type d'upload SANS nouveau fichier (impossible/invalide)
                } else {
                    $pdfPath = $ancienChemin;
                }

                // Si le type a changé ou le chemin a changé
                $pdf->setThematique($formData['thematique']);
                $pdf->setNom($formData['nom']);
                $pdf->setDescription($formData['description']);
                $pdf->setEstAfficher($formData['estAfficher']);
                $pdf->setEstTelechargeable($formData['estTelechargeable']);
                $pdf->setFichier($pdfPath);

                // 4. Persistance de l'Entité PhotoVideo
                $this->entityManager->flush();

                // 5. Succès et Redirection
                $_SESSION['success_message'] = "PDF '{$pdf->getNom()}' modifié avec succès !";
                $this->redirect("/avva-admin/page/modifier/8");

            } catch (\Exception $e) {
                $_SESSION['error_message'] = "Erreur lors de la modification du PDF : " . $e->getMessage();
                $error = $e->getMessage(); // Pour affichage dans la vue
            }
        }

        // 6. Affichage du Formulaire (GET ou POST avec Erreur)
        $this->render('/admin/pages/modifier-pdf', [
            'error' => $error,
            'active6' => $active6,
            'utilisateur' => $user,
            'pdf' => $pdf, // L'entité à jour ou avec les données postées
            'formData' => $formData, // Pour pré-remplir le formulaire
        ]);
    }

    public function supprimerPdf(int $id): void
    {
        session_start();

        // 1. Authentification et Autorisation
        if (!$this->isUserLoggedIn()) {
            // NOTE: $this->redirect("/admin/login"); est préférable à $this->redirect("admin/login");
            $this->redirect("/admin/login");
            return;
        }

        // 2. Récupération de l'entité à supprimer
        $pdf = $this->entityManager->getRepository(PageCommentAdhererPdf::class)->find($id);

        $fichierChemin = $pdf->getFichier(); // Récupère le chemin (pour les images/vidéos uploadées) ou l'URL (pour les vidéos externes)

        try {
            // 3. Suppression de l'entrée dans la base de données
            $this->entityManager->remove($pdf);
            $this->entityManager->flush();

            // 4. Suppression du fichier physique (Uniquement si ce n'est PAS une URL externe)
            // Les médias uploadés (images ou vidéos) auront un chemin relatif, pas une URL http/https.

            // On vérifie que c'est un média local AVANT de tenter de manipuler le système de fichiers
            if (!filter_var($fichierChemin, FILTER_VALIDATE_URL)) {

                // Construit le chemin ABSOLU du fichier sur le serveur
                // Assurez-vous que $fichierChemin ne contient pas de '/' initial s'il est relatif au DOCUMENT_ROOT
                $fichierPhysique = $_SERVER['DOCUMENT_ROOT'] . $fichierChemin;

                // Vérification de la présence du fichier
                if (file_exists($fichierPhysique)) {
                    // Tente de supprimer le fichier
                    if (!unlink($fichierPhysique)) {
                        // La BDD est propre, mais on log l'erreur du fichier (ne devrait pas empêcher le succès BDD)
                        // NOTE: Il est préférable de jeter une exception pour loguer, mais de la rattraper SANS annuler le succès BDD.
                        // Pour simplifier ici, on loggue une erreur dans la session et on continue vers le succès.
                        throw new \Exception("Impossible de supprimer le fichier physique: " . $fichierPhysique);
                    }
                }
            }

            // 5. Succès et Redirection
            $_SESSION['success_message'] = "PDF '{$pdf->getNom()}' supprimé avec succès.";

        } catch (\Exception $e) {
            // Gère les erreurs (suppression de fichier ou BDD)
            $_SESSION['error_message'] = "Erreur lors de la suppression du PDF : " . $e->getMessage();
        }

        // Redirection systématique vers la liste des médias
        $this->redirect("/avva-admin/page/modifier/8");
    }

    public function uploadFile(): void
    {
        header('Content-Type: application/json');
        $fileKey = 'file';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES[$fileKey]) || $_FILES[$fileKey]["error"] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['error' => "Erreur lors de l'envoi du fichier."]);
            exit;
        }

        // --- MODIFICATION DEMANDÉE : Simplification de l'accès au fichier ---
        $file = $_FILES[$fileKey];
        $tempPath = $file['tmp_name'];

        // --- 2. Détermination du chemin et sécurité ---
        $relativePath = 'uploads/images/';
        // MODIFICATION DEMANDÉE : Utilisation de $_SERVER['DOCUMENT_ROOT']
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/' . $relativePath;
        $fileUrl = '/' . $relativePath;

        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur critique: Création du dossier impossible.']);
                exit;
            }
        }

        // Sécurité de base
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml', 'video/mp4', 'video/webm'];
        $maxFileSize = 10 * 1024 * 1024;

        if (!in_array($file['type'], $allowedMimeTypes) || $file['size'] > $maxFileSize) {
            http_response_code(400);
            echo json_encode(['error' => 'Format ou taille de fichier non autorisé.']);
            exit;
        }

        // --- 3. Déduplication et Sauvegarde ---
        $fileHash = hash_file('sha256', $tempPath);
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (empty($extension)) {
            $extension = 'jpg';
        }

        $safeFilename = $fileHash . '.' . $extension;
        $destinationPath = $uploadDir . $safeFilename;
        $finalFileUrl = $fileUrl . $safeFilename;

        if (file_exists($destinationPath)) {
            echo json_encode(['url' => $finalFileUrl]);
            exit;
        }

        if (move_uploaded_file($tempPath, $destinationPath)) {
            echo json_encode(['url' => $finalFileUrl]);
            exit;
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur 500: Échec de la sauvegarde du fichier sur le serveur. (Vérifiez les permissions 777 sur uploads/images/)']);
            exit;
        }
    }

    public function uploadUrl(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['imageUrl']) || !filter_var($_POST['imageUrl'], FILTER_VALIDATE_URL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Erreur 400: URL non valide ou requête incorrecte.']);
            exit;
        }
        $imageUrl = $_POST['imageUrl'];

        // --- 2. Détermination du chemin ---
        $relativePath = 'uploads/images/';
        // MODIFICATION DEMANDÉE : Utilisation de $_SERVER['DOCUMENT_ROOT']
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/' . $relativePath;
        $fileUrl = '/' . $relativePath;

        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur 500: Création du dossier impossible.']);
                exit;
            }
        }

        // --- 3. DÉTECTION DU MIME TYPE PAR EN-TÊTE HTTP (via cURL) et TÉLÉCHARGEMENT ---
        $extension = 'jpg';

        if (extension_loaded('curl')) {
            // ... Logique cURL pour la détection du MIME Type (inchangée) ...
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $imageUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');

            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $mimeType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            curl_close($ch);

            if ($httpCode === 200 && $mimeType) {
                $map = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif',
                    'image/webp' => 'webp',
                    'image/svg+xml' => 'svg',
                ];
                $cleanMimeType = explode(';', $mimeType)[0];
                $extension = $map[$cleanMimeType] ?? 'bin';
            }
        } else {
            $pathExt = strtolower(pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
            $extension = empty($pathExt) ? 'jpg' : $pathExt;
        }

        // TÉLÉCHARGEMENT DU CONTENU
        $context = stream_context_create(['http' => ['header' => 'User-Agent: Mozilla/5.0']]);
        $imageContent = @file_get_contents($imageUrl, false, $context);

        if ($imageContent === false) {
            http_response_code(400);
            echo json_encode(['error' => 'Erreur 400: Échec du téléchargement. Vérifiez allow_url_fopen (et cURL).']);
            exit;
        }


        // --- 4. Déduplication et Sauvegarde ---
        $fileHash = hash('sha256', $imageContent);
        $safeFilename = $fileHash . '.' . $extension;
        $destinationPath = $uploadDir . $safeFilename;
        $finalFileUrl = $fileUrl . $safeFilename;

        if (file_exists($destinationPath)) {
            echo json_encode(['url' => $finalFileUrl]);
            exit;
        }

        if (file_put_contents($destinationPath, $imageContent) !== false) {
            echo json_encode(['url' => $finalFileUrl]);
            exit;
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur 500: Échec de l\'écriture du fichier. Vérifiez les permissions (777) sur le dossier uploads/images/.']);
            exit;
        }
    }

    // ---------------------- FONCTIONS UTILITAIRES MISES À JOUR AVEC $_SERVER['DOCUMENT_ROOT'] ----------------------

    /**
     * Gère l'upload d'un fichier GPX et retourne son chemin relatif/public.
     * Nécessite l'utilisation de $_SERVER['DOCUMENT_ROOT'] pour un chemin absolu sur le serveur.
     * * @return string|null Chemin relatif/public du fichier GPX stocké, ou null en cas d'échec.
     */
    private function handleGpxUpload(): ?string
    {
        // --- 1. Vérification de la présence du fichier et des erreurs initiales ---
        if (!isset($_FILES['gpxFile']) || $_FILES['gpxFile']['error'] !== UPLOAD_ERR_OK) {
            // Optionnel : Enregistrer l'erreur dans un log (par exemple, fichier trop gros)
            // if (isset($_FILES['gpxFile']) && $_FILES['gpxFile']['error'] === UPLOAD_ERR_INI_SIZE) { ... }
            return null;
        }

        $file = $_FILES['gpxFile'];

        // --- 2. Définition des chemins et validation des types ---

        // Le chemin public à stocker en base de données
        $publicPath = '/uploads/gpx/';
        // Le chemin absolu sur le serveur
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . $publicPath;

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $mimeType = mime_content_type($file['tmp_name']); // Récupère le type MIME réel

        // Extensions et Types MIME acceptés
        $allowedExtensions = ['gpx'];
        // Les types MIME pour les fichiers GPX peuvent varier, généralement XML ou TEXT
        $allowedMimeTypes = ['application/xml', 'text/xml', 'application/gpx+xml', 'text/plain'];

        // Vérification de l'extension et du type MIME pour une sécurité renforcée
        if (!in_array(strtolower($extension), $allowedExtensions) || !in_array($mimeType, $allowedMimeTypes)) {
            // Le fichier n'est pas un GPX valide
            return null;
        }

        // --- 3. Préparation du répertoire d'upload ---

        // Création du répertoire s'il n'existe pas
        if (!is_dir($uploadDir)) {
            // Tentative de création du répertoire avec permissions récursives (0755 est généralement préféré)
            if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
                // Échec de la création du répertoire (problème de permission)
                // throw new \RuntimeException(sprintf('Directory "%s" was not created', $uploadDir));
                return null;
            }
        }

        // --- 4. Déplacement du fichier ---

        // Nom unique du fichier pour éviter les collisions et les problèmes de cache
        // Utilisez 'gpx_' pour une meilleure traçabilité
        $fileName = uniqid('gpx_', true) . '.' . $extension;
        $filePath = $uploadDir . $fileName; // Chemin absolu de destination

        // Déplacement du fichier temporaire vers sa destination finale
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Le fichier est en place. Retourne le chemin public pour la base de données.
            return $publicPath . $fileName;
        }

        // Échec du déplacement (problème de permission, ou fichier temp non trouvé)
        return null;
    }

    /**
     * Supprime un ancien fichier GPX en utilisant son chemin public stocké en DB.
     * * @param string|null $filePath Chemin relatif/public du fichier GPX à supprimer.
     */
    private function deleteOldGpxFile(?string $filePath): void
    {
        if ($filePath) {
            // Construction du chemin absolu sur le serveur
            $serverPath = $_SERVER['DOCUMENT_ROOT'] . $filePath;

            if (file_exists($serverPath)) {
                // Tentative de suppression
                if (!unlink($serverPath)) {
                    // Optionnel : Gérer une erreur si la suppression a échoué (problème de permissions)
                }
            }
        }
    }

    // ---------------------- FONCTION D'AJOUT D'ÉVÉNEMENT (Logique GPX mise à jour) ----------------------

    public function ajouterEvenement(): void
    {
        session_start();
        // Utiliser $_POST car le JS envoie un FormData
        $data = $_POST;

        if (empty($data['title']) || empty($data['start'])) {
            echo json_encode(['success' => false, 'message' => 'Champs manquants']);
            return;
        }

        $pageDate = new DateEvent();
        $pageDate->setTitre($data['title']);
        $pageDate->setDescription($data['description'] ?? '');
        $pageDate->setDateStart(new \DateTime($data['start']));

        if (!empty($data['end'])) {
            $pageDate->setDateEnd(new \DateTime($data['end']));
        }

        if (!empty($data['categorieId'])) {
            $categorie = $this->entityManager->getRepository(CategorieEvent::class)->find($data['categorieId']);
            if ($categorie) {
                $pageDate->setCategorieEvent($categorie);
            }
        }

        if (!empty($data['compteRendu'])) {
            $pageDate->setCompteRendu($data['compteRendu']);
        }

        // GESTION GPX
        $gpxFilePath = $this->handleGpxUpload();
        if ($gpxFilePath) {
            // Assurez-vous que setGpxFilePath existe dans votre entité
            $pageDate->setGpxFilePath($gpxFilePath);
        }

        $this->entityManager->persist($pageDate);
        $this->entityManager->flush();

        echo json_encode([
            'success' => true,
            'message' => 'Événement ajouté',
            'id' => $pageDate->getId(),
            'gpxFilePath' => $gpxFilePath
        ]);
    }

    // ---------------------- FONCTION DE MODIFICATION D'ÉVÉNEMENT (Logique GPX mise à jour) ----------------------

    public function modifierEvenement(): void
    {
        session_start();

        $data = $_POST;

        if (empty($data['id']) || empty($data['title']) || empty($data['start'])) {
            echo json_encode(['success' => false, 'message' => 'Champs manquants']);
            return;
        }

        $pageDate = $this->entityManager->getRepository(DateEvent::class)->find($data['id']);
        if (!$pageDate) {
            echo json_encode(['success' => false, 'message' => 'Événement introuvable']);
            return;
        }

        // Mise à jour des champs standards
        $pageDate->setTitre($data['title']);
        $pageDate->setDescription($data['description'] ?? '');
        $pageDate->setDateStart(new \DateTime($data['start']));

        if (!empty($data['end'])) {
            $pageDate->setDateEnd(new \DateTime($data['end']));
        } else {
            $pageDate->setDateEnd(null);
        }

        if (!empty($data['categorieId'])) {
            $categorie = $this->entityManager->getRepository(CategorieEvent::class)->find($data['categorieId']);
            if ($categorie) {
                $pageDate->setCategorieEvent($categorie);
            }
        }

        if (!empty($data['compteRendu'])) {
            $pageDate->setCompteRendu($data['compteRendu']);
        } else {
            $pageDate->setCompteRendu(null);
        }

        // GESTION GPX
        $gpxFilePath = $this->handleGpxUpload();

        if ($gpxFilePath) {
            // CAS 1: Un nouveau fichier a été uploadé (Remplacement)
            $this->deleteOldGpxFile($pageDate->getGpxFilePath());
            $pageDate->setGpxFilePath($gpxFilePath);
        } elseif (isset($data['deleteGpx']) && $data['deleteGpx'] === 'true') {
            // CAS 2: L'utilisateur a demandé explicitement la suppression de l'existant
            $this->deleteOldGpxFile($pageDate->getGpxFilePath());
            $pageDate->setGpxFilePath(null);
        }
        // CAS 3 & 4 (conservation de l'existant ou pas de fichier) ne nécessitent pas de modification ici.

        $this->entityManager->flush();

        echo json_encode(['success' => true, 'message' => 'Événement modifié avec succès']);
    }

    // ---------------------- FONCTION DE SUPPRESSION D'ÉVÉNEMENT (Logique GPX mise à jour) ----------------------

    public function supprimerEvenement(): void
    {
        session_start();

        // Utiliser json_decode pour la suppression simple par ID
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
            return;
        }

        $pageDate = $this->entityManager->getRepository(DateEvent::class)->find($data['id']);
        if (!$pageDate) {
            echo json_encode(['success' => false, 'message' => 'Événement introuvable']);
            return;
        }

        // SUPPRESSION GPX AVANT DE SUPPRIMER L'ENTITÉ
        $this->deleteOldGpxFile($pageDate->getGpxFilePath());

        $this->entityManager->remove($pageDate);
        $this->entityManager->flush();

        echo json_encode(['success' => true, 'message' => 'Événement supprimé']);
    }

    public function rapportsPasses(): void
    {
        session_start();

        if (!$this->isUserLoggedIn()) {
            $this->redirect('/avva-admin/login');
            return;
        }

        // Pour l'affichage dans le menu (active9 si c'est la 9ème entrée)
        $active13 = true;

        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        $now = new \DateTime();

        // Récupérer les événements dont la date de fin (ou de début) est passée
        $eventRepository = $this->entityManager->getRepository(DateEvent::class);

        // Requête DQL pour récupérer les événements passés, triés par date décroissante
        $query = $this->entityManager->createQuery(
            'SELECT e FROM App\Entity\DateEvent e 
         WHERE e.dateStart < :now 
         ORDER BY e.dateStart DESC'
        )->setParameter('now', $now->format('Y-m-d H:i:s'));

        $evenementsPasses = $query->getResult();

        // Récupérer les pages pour le menu si nécessaire
        // $pages = $this->entityManager->getRepository(Page::class)->findAll();

        $this->render('admin/rapports-passes', [
            'user' => $_SESSION['user'],
            'active13' => $active13,
            'evenements' => $evenementsPasses,
            'pages' => $pages,
        ]);
    }

    /**
     * Traite l'enregistrement du compte rendu pour un événement spécifique.
     * URL: /avva-admin/comptes-rendus/save (méthode POST)
     */
    public function saveCompteRendu(): void
    {
        session_start();

        if (!$this->isUserLoggedIn()) {
            $this->redirect('/avva-admin/login');
            return;
        }

        $evenementId = filter_input(INPUT_POST, 'evenement_id', FILTER_VALIDATE_INT);
        $compteRendu = $_POST['compte_rendu'] ?? null;

        if (!$evenementId) {
            $_SESSION['error_message'] = "ID d'événement manquant.";
            $this->redirect('/avva-admin/comptes-rendus');
            return;
        }

        $evenement = $this->entityManager->getRepository(DateEvent::class)->find($evenementId);

        if (!$evenement) {
            $_SESSION['error_message'] = "Événement introuvable.";
            $this->redirect('/avva-admin/comptes-rendus');
            return;
        }

        // --- LOGIQUE DE NETTOYAGE DU COMPTE RENDU ---
        if ($compteRendu !== null) {
            // 1. Définir les balises HTML que vous autorisez (Summernote utilise p, br, strong, etc.)
            // Si Summernote insère <p><br></p>, cela doit être considéré comme vide.
            // On supprime d'abord les balises HTML vides ou de simple retour à la ligne.

            // Remplace les <p> vides ou ne contenant que <br> ou &nbsp; par une chaîne vide
            $cleanedContent = preg_replace('/<p>\s*(<br\s*\/?>|&nbsp;)?\s*<\/p>/i', '', $compteRendu);

            // Supprime les balises <br> restantes en début/fin de contenu et les espaces
            $cleanedContent = trim($cleanedContent);
            $cleanedContent = preg_replace('/^<br\s*\/?>|<br\s*\/?>$/i', '', $cleanedContent);
            $cleanedContent = trim($cleanedContent);

            // Si, après nettoyage, le contenu est vide (ou ne contient que des espaces/balises non essentielles), on le met à null.
            if (empty($cleanedContent)) {
                $compteRendu = null;
            } else {
                $compteRendu = $cleanedContent;
            }
        }
        // --- FIN DE LA LOGIQUE DE NETTOYAGE ---

        try {
            // Enregistre le contenu nettoyé ou null
            $evenement->setCompteRendu($compteRendu);

            $this->entityManager->flush();

            $message = ($compteRendu !== null)
                ? "Compte rendu enregistré pour l'événement : " . $evenement->getTitre()
                : "Compte rendu supprimé pour l'événement : " . $evenement->getTitre();

            $_SESSION['success_message'] = $message;

        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Erreur lors de l'enregistrement : " . $e->getMessage();
        }

        $this->redirect('/avva-admin/comptes-rendus');
    }

    public function modifierPageAPropos(int $id): void
    {
        session_start();

        $active7 = true;

        // Vérifier si l'utilisateur est connecté
        if (!$this->isUserLoggedIn()) {
            header("Location: /admin/login");
            exit;
        }

        $user = $_SESSION['user']; // Utilisateur stocké dans la session

        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        // Récupérer l'ID du challenge que vous souhaitez modifier
        $pageId = $this->entityManager->getRepository(PageAPropos::class)->find($id)->getId();

        // Créer une instance de la UserStory ModifierPageAccueilChallenge
        $modifierPage = new ModifierPageAPropos($this->entityManager);

        // Vérifier si le formulaire a été soumis
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                // Validation des données
                $contenu = $_POST['contenu_page'];

                if (empty($contenu)) {
                    throw new \Exception("Tous les champs doivent être remplis.");
                }

                // Appeler la méthode pour mettre à jour les données
                $modifierPage->execute(
                    $pageId,
                    $contenu
                );

                $_SESSION['success_message'] = "La page à propos a été modifier avec succès";
                $this->redirect("/avva-admin/page/a-propos/1");
                exit();
            } catch (\Exception $e) {
                // Gérer les erreurs et afficher un message à l'utilisateur
                $error = $e->getMessage();
            }
        }

        // Passer les données à la vue pour pré-remplir le formulaire
        $this->render('/admin/pages/modifier-page-a-propos', [
            'user' => $_SESSION['user'],
            'active7' => $active7,
            'pages' => $pages,
            'error' => $error ?? '',
            'utilisateur' => $_SESSION['utilisateur'] ?? null,
            'page' => $this->entityManager->getRepository(PageAPropos::class)->find($id)
        ]);
    }

    public function modifierPageStatus(int $id): void
    {
        session_start();

        $active8 = true;

        // Vérifier si l'utilisateur est connecté
        if (!$this->isUserLoggedIn()) {
            header("Location: /admin/login");
            exit;
        }

        $user = $_SESSION['user']; // Utilisateur stocké dans la session

        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        // Récupérer l'ID du challenge que vous souhaitez modifier
        $pageId = $this->entityManager->getRepository(PageStatus::class)->find($id)->getId();

        // Créer une instance de la UserStory ModifierPageAccueilChallenge
        $modifierPage = new ModifierPageStatus($this->entityManager);

        // Vérifier si le formulaire a été soumis
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                // Validation des données
                $contenu = $_POST['contenu_page'];

                if (empty($contenu)) {
                    throw new \Exception("Tous les champs doivent être remplis.");
                }

                // Appeler la méthode pour mettre à jour les données
                $modifierPage->execute(
                    $pageId,
                    $contenu
                );

                $_SESSION['success_message'] = "La page status a été modifier avec succès";
                $this->redirect("/avva-admin/page/status/1");
                exit();
            } catch (\Exception $e) {
                // Gérer les erreurs et afficher un message à l'utilisateur
                $error = $e->getMessage();
            }
        }

        // Passer les données à la vue pour pré-remplir le formulaire
        $this->render('/admin/pages/modifier-page-status', [
            'user' => $_SESSION['user'],
            'active8' => $active8,
            'pages' => $pages,
            'error' => $error ?? '',
            'utilisateur' => $_SESSION['utilisateur'] ?? null,
            'page' => $this->entityManager->getRepository(PageStatus::class)->find($id)
        ]);
    }

    public function modifierPagePresentation(int $id): void
    {
        session_start();

        $active9 = true;

        // Vérifier si l'utilisateur est connecté
        if (!$this->isUserLoggedIn()) {
            header("Location: /admin/login");
            exit;
        }

        $user = $_SESSION['user']; // Utilisateur stocké dans la session

        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        // Récupérer l'ID du challenge que vous souhaitez modifier
        $pageId = $this->entityManager->getRepository(PagePresentation::class)->find($id)->getId();

        // Créer une instance de la UserStory ModifierPageAccueilChallenge
        $modifierPage = new ModifierPagePresentation($this->entityManager);

        // Vérifier si le formulaire a été soumis
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                // Validation des données
                $contenu = $_POST['contenu_page'];

                if (empty($contenu)) {
                    throw new \Exception("Tous les champs doivent être remplis.");
                }

                // Appeler la méthode pour mettre à jour les données
                $modifierPage->execute(
                    $pageId,
                    $contenu
                );

                $_SESSION['success_message'] = "La page présentation a été modifier avec succès";
                $this->redirect("/avva-admin/page/presentation/1");
                exit();
            } catch (\Exception $e) {
                // Gérer les erreurs et afficher un message à l'utilisateur
                $error = $e->getMessage();
            }
        }

        // Passer les données à la vue pour pré-remplir le formulaire
        $this->render('/admin/pages/modifier-page-presentation', [
            'user' => $_SESSION['user'],
            'active9' => $active9,
            'pages' => $pages,
            'error' => $error ?? '',
            'utilisateur' => $_SESSION['utilisateur'] ?? null,
            'page' => $this->entityManager->getRepository(PagePresentation::class)->find($id)
        ]);
    }

    public function gestionDefilement(): void
    {
        session_start();

        // Configuration de base
        $active6 = true; // Adaptez l'index selon votre menu admin
        $error = '';
        $success = '';

        // 1. Vérification de l'authentification
        if (!$this->isUserLoggedIn()) {
            $this->redirect("/admin/login");
            return;
        }

        $user = $_SESSION['user'];
        $pages = $this->entityManager->getRepository(Page::class)->findAll();
        $repository = $this->entityManager->getRepository(DefilementTexte::class);

        // 2. Récupération du texte actuel (on suppose qu'il n'y en a qu'un seul)
        $texteEntite = $repository->findOneBy([]) ?? new DefilementTexte();
        $contenuTexte = $texteEntite->getDefilementTexte() ?? '';
        $couleurTexte = $texteEntite->getCouleurDefilementTexte() ?? '';
        $fondTexte = $texteEntite->getFondDefilementTexte() ?? '';
        $tailleTexte = $texteEntite->getTailleDefilementTexte() ?? '';
        $positionTexte = $texteEntite->getPositionDefilementTexte() ?? '';

        // 3. Traitement de la soumission du formulaire
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                $nouveauTexte = $_POST['defilement_texte'] ?? '';
                $nouvelleCouleurTexte = $_POST['couleur_defilement_texte'] ?? '';
                $nouveauFondTexte = $_POST['fond_defilement_texte'] ?? '';
                $nouvelleTailleTexte = $_POST['taille_defilement_texte'] ?? '';
                $nouvellePositionTexte = $_POST['position_defilement_texte'] ?? '';

                if (empty($nouveauTexte)) {
                    throw new \Exception("Le texte de défilement ne peut pas être vide.");
                }

                // Mise à jour de l'entité
                $texteEntite->setDefilementTexte($nouveauTexte);
                $texteEntite->setCouleurDefilementTexte($nouvelleCouleurTexte);
                $texteEntite->setFondDefilementTexte($nouveauFondTexte);
                $texteEntite->setTailleDefilementTexte($nouvelleTailleTexte);
                $texteEntite->setPositionDefilementTexte($nouvellePositionTexte);

                // Persistance si c'est un nouvel objet, sinon Doctrine suit déjà l'objet
                if (!$texteEntite->getId()) {
                    $this->entityManager->persist($texteEntite);
                }

                $this->entityManager->flush();

                $_SESSION['success_message'] = "Le texte défilant a été mis à jour avec succès.";
                $this->redirect("/avva-admin/defilement"); // Adaptez l'URL de redirection
                return;

            } catch (\Exception $e) {
                $error = $e->getMessage();
                $contenuTexte = $_POST['defilement_texte'] ?? $contenuTexte;
                $couleurTexte = $_POST['couleur_defilement_texte'] ?? $couleurTexte;
                $fondTexte = $_POST['fond_defilement_texte'] ?? $fondTexte;
                $tailleTexte = $_POST['taille_defilement_texte'] ?? $tailleTexte;
                $positionTexte = $_POST['position_defilement_texte'] ?? $positionTexte;
            }
        }

        // Récupération du message de succès en session s'il existe
        if (isset($_SESSION['success_message'])) {
            $success = $_SESSION['success_message'];
            unset($_SESSION['success_message']);
        }

        // 4. Rendu de la vue
        $this->render('/admin/pages/gestion-defilement', [
            'user' => $user,
            'active6' => $active6,
            'pages' => $pages,
            'contenuTexte' => $contenuTexte,
            'couleurTexte' => $couleurTexte,
            'fondTexte' => $fondTexte,
            'tailleTexte' => $tailleTexte,
            'positionTexte' => $positionTexte,
            'error' => $error,
            'success' => $success
        ]);
    }

    public function creerSortie(): void
    {
        session_start();

        $active12 = true;
        $isEditing = false;
        $fuseauHoraire = new \DateTimeZone('Europe/Paris');
        $error = '';

        // 1. Vérification de l'authentification
        if (!$this->isUserLoggedIn()) {
            $this->redirect("/admin/login");
            return;
        }

        $user = $_SESSION['user'];
        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        // Initialisation/Récupération des types disponibles pour le select
        $typesSortiesDisponibles = $this->entityManager->getRepository(TypeSortie::class)->findAll();

        $repository = $this->entityManager->getRepository(MessageApresSortieHebdomadaire::class);
        $messageEntite = $repository->findOneBy([]) ?? new MessageApresSortieHebdomadaire();
        $contenuTexte = $messageEntite->getMessage() ?? '';

        $repository2 = $this->entityManager->getRepository(MessageSortieHebdomadaireADefinir::class);
        $messageEntite2 = $repository2->findOneBy([]) ?? new MessageSortieHebdomadaireADefinir();
        $contenuTexte2 = $messageEntite2->getMessage() ?? '';

        // Valeurs par défaut pour le formulaire
        $titre = 'Nouvelle Sortie Vélo';
        $description = '';
        $dateDepart = (new \DateTime('now', $fuseauHoraire))->format('Y-m-d\TH:i');
        $tempsParDefaut = (new \DateTime('08:00'))->format('H:i');
        $difficulte = '';

        // 2. Traitement de la soumission du formulaire
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                $titre = $_POST['titre_sortie'] ?? '';
                $description = $_POST['description_sortie'] ?? null;
                $dateString = $_POST['date_depart_sortie'] ?? '';
                $tempsString = $_POST['temps_sortie'] ?? '';
                $difficulte = $_POST['difficulte_sortie'] ?? null;

                // Récupération des IDs sélectionnés (tableau)
                $selectedTypeIds = $_POST['type_sortie'] ?? [];

                if (empty($titre) || empty($dateString)) {
                    throw new \Exception("Le titre et la date de départ sont obligatoires.");
                }

                // Conversion des dates
                $date = \DateTime::createFromFormat('Y-m-d\TH:i', $dateString, $fuseauHoraire);
                $tempsObj = \DateTime::createFromFormat('H:i', $tempsString);

                if (!$date || !$tempsObj) {
                    throw new \Exception("Format de date ou d'heure invalide.");
                }

                // 3. Récupération de la catégorie d'événement (ID 4)
                $categorieSortie = $this->entityManager->getRepository(CategorieEvent::class)->find(4);
                if (!$categorieSortie) {
                    throw new \Exception("La catégorie d'événement (ID 4) est introuvable.");
                }

                // 4. Création de l'entité Sortie
                $nouvelleSortie = new Sortie();
                $nouvelleSortie->setTitre($titre);
                $nouvelleSortie->setDescription($description);
                $nouvelleSortie->setDate($date);
                $nouvelleSortie->setTemps($tempsObj);
                $nouvelleSortie->setDifficulte($difficulte);

                // 5. Gestion de la sélection multiple des types
                if (!empty($selectedTypeIds)) {
                    $selectedTypesEntities = $this->entityManager
                        ->getRepository(TypeSortie::class)
                        ->findBy(['id' => $selectedTypeIds]);

                    foreach ($selectedTypesEntities as $typeEntity) {
                        $nouvelleSortie->setTypeSortie($typeEntity);
                    }
                }

                $this->entityManager->persist($nouvelleSortie);

                // 6. Création de l'entité DateEvent (Calendrier)
                $nouvelEvent = new DateEvent();
                $nouvelEvent->setTitre($titre);
                $nouvelEvent->setDescription($description ?? 'Sortie Vélo Hebdomadaire');
                $nouvelEvent->setDateStart($date);
                $nouvelEvent->setCategorieEvent($categorieSortie);

                $this->entityManager->persist($nouvelEvent);

                // 7. Sauvegarde finale
                $this->entityManager->flush();

                $_SESSION['success_message'] = "La sortie '" . htmlspecialchars($titre) . "' a été créée et ajoutée au calendrier.";
                $this->redirect("/avva-admin/sortie");
                return;

            } catch (\Exception $e) {
                $error = $e->getMessage();
                // On garde les valeurs saisies pour ré-affichage en cas d'erreur
                $dateDepart = $_POST['date_depart_sortie'] ?? $dateDepart;
            }
        }

        // 8. Récupération de la liste des sorties pour le tableau d'affichage
        $sorties = $this->entityManager->getRepository(Sortie::class)->findAll();

        // 9. Rendu de la vue
        $this->render('/admin/pages/gestion-sorties', [
            'user' => $user,
            'active12' => $active12,
            'pages' => $pages,
            'sorties' => $sorties,
            'titre' => $titre,
            'description' => $description,
            'dateDepart' => $dateDepart,
            'temps' => $tempsString ?? $tempsParDefaut,
            'difficulte' => $difficulte,
            'typesSorties' => $typesSortiesDisponibles, // Pour remplir le <select>
            'contenuTexte' => $contenuTexte,
            'contenuTexte2' => $contenuTexte2,
            'error' => $error,
            'isEditing' => $isEditing,
            'sortieEnEdition' => null,
        ]);
    }

    public function modifierSortie(int $id): void
    {
        session_start();

        $active12 = true;
        $isEditing = true;
        $fuseauHoraire = new \DateTimeZone('Europe/Paris');
        $error = '';

        // 1. Vérification de l'authentification
        if (!$this->isUserLoggedIn()) {
            $this->redirect("/admin/login");
            return;
        }

        $user = $_SESSION['user'];

        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        // 2. Récupérer l'objet Sortie à modifier
        $sortieEnEdition = $this->entityManager->getRepository(Sortie::class)->find($id);

        $repository = $this->entityManager->getRepository(MessageApresSortieHebdomadaire::class);
        $messageEntite = $repository->findOneBy([]) ?? new MessageApresSortieHebdomadaire();
        $contenuTexte = $messageEntite->getMessage() ?? '';

        $repository2 = $this->entityManager->getRepository(MessageSortieHebdomadaireADefinir::class);
        $messageEntite2 = $repository2->findOneBy([]) ?? new MessageSortieHebdomadaireADefinir();
        $contenuTexte2 = $messageEntite2->getMessage() ?? '';

        if (!$sortieEnEdition) {
            $_SESSION['error_message'] = "Erreur : La sortie demandée pour modification n'existe pas.";
            $this->redirect("/avva-admin/sortie");
            return;
        }

        // Initialisation des variables pour pré-remplir le formulaire
        $titre = htmlspecialchars($sortieEnEdition->getTitre());
        $description = $sortieEnEdition->getDescription();
        $dateDepart = $sortieEnEdition->getDate()->format('Y-m-d\TH:i');
        $temps = $sortieEnEdition->getTemps()->format('H:i');
        $difficulte = $sortieEnEdition->getDifficulte();

        // 3. Récupérer toutes les sorties pour l'affichage de la liste
        $sorties = $this->entityManager->getRepository(Sortie::class)->findAll();

        // 4. Traitement du formulaire POST (Tentative de modification)
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // --- Récupération des données avant validation (pour conservation en cas d'erreur) ---
            $nouveauTitre = $_POST['titre_sortie'] ?? '';
            $nouvelleDescription = $_POST['description_sortie'] ?? null;
            $nouvelleDateString = $_POST['date_depart_sortie'] ?? '';
            $nouveauTemps = $_POST['temps_sortie'] ?? '';
            $nouvelleDifficulte = $_POST['difficulte_sortie'] ?? null;

            try {
                // Validation et conversion de la nouvelle date
                if (empty($nouveauTitre) || empty($nouvelleDateString)) {
                    throw new \Exception("Le titre et la date de départ sont obligatoires.");
                }

                $nouvelleDate = \DateTime::createFromFormat('Y-m-d\TH:i', $nouvelleDateString, $fuseauHoraire);

                if ($nouvelleDate === false) {
                    throw new \Exception("Format de date invalide.");
                }

                $nouveauTemps = \DateTime::createFromFormat('H:i', $nouveauTemps);

                // 5. SYNCHRONISATION : Récupérer et Mettre à jour DateEvent

                // On récupère la catégorie Sortie Vélo
                $categorieSortie = $this->entityManager->getRepository(CategorieEvent::class)->find(4);

                if (!$categorieSortie) {
                    throw new \Exception("La catégorie d'événement requise pour la synchronisation n'a pas été trouvée.");
                }

                // Recherche de l'événement correspondant dans DateEvent
                // Méthode de recherche : par l'ancien titre et l'ancienne date de début (avant modification)
                // C'est une recherche manuelle car il n'y a pas de relation entre les deux entités.
                $eventAChercher = $this->entityManager->getRepository(DateEvent::class)->findOneBy([
                    'titre' => $sortieEnEdition->getTitre(),
                    'dateStart' => $sortieEnEdition->getDate(),
                    'categorieEvent' => $categorieSortie,
                ]);

                if ($eventAChercher) {
                    // Mise à jour de l'événement trouvé
                    $eventAChercher->setTitre($nouveauTitre);
                    $eventAChercher->setDescription($nouvelleDescription ?? 'Sortie Vélo Hebdomadaire');
                    $eventAChercher->setDateStart($nouvelleDate);
                    // Si vous aviez une date de fin, il faudrait la mettre à jour ici aussi.

                    $this->entityManager->persist($eventAChercher); // Marque pour la synchronisation
                } else {
                    // Optionnel : Enregistrer un message d'alerte ou créer un nouvel événement si l'ancien est introuvable.
                    // Pour l'instant, on ignore s'il est introuvable, mais la Sortie est quand même mise à jour.
                    error_log("Alerte: L'événement DateEvent pour la sortie ID {$id} n'a pas été trouvé pour synchronisation.");
                }

                // 6. Mise à jour de l'entité SORTIE
                $sortieEnEdition->setTitre($nouveauTitre);
                $sortieEnEdition->setDescription($nouvelleDescription);
                $sortieEnEdition->setDate($nouvelleDate);
                $sortieEnEdition->setTemps($nouveauTemps);
                $sortieEnEdition->setDifficulte($nouvelleDifficulte);

                $this->entityManager->persist($sortieEnEdition); // Marque pour la synchronisation
                $this->entityManager->flush(); // Exécute les deux mises à jour

                $_SESSION['success_message'] = "La sortie " . htmlspecialchars($nouveauTitre) . " a été modifiée avec succès et l'événement a été synchronisé.";

                $this->redirect("/avva-admin/sortie");
                return;

            } catch (\Exception $e) {
                $error = "Erreur lors de la modification : " . $e->getMessage();

                // Conserver les données saisies en cas d'erreur
                $titre = $nouveauTitre;
                $description = $nouvelleDescription;
                $dateDepart = $nouvelleDateString;
                $temps = $nouveauTemps;
                $difficulte = $nouvelleDifficulte;
            }
        }

        // 7. Affichage de la vue
        $this->render('/admin/pages/gestion-sorties', [
            'user' => $user,
            'active12' => $active12,
            'pages' => $pages,
            'sorties' => $sorties,
            'error' => $error,

            // Variables pour pré-remplir le formulaire
            'titre' => $titre,
            'description' => $description,
            'dateDepart' => $dateDepart,
            'temps' => $nouveauTemps,
            'difficulte' => $difficulte,

            'utilisateur' => $_SESSION['utilisateur'] ?? null,
            'sortieEnEdition' => $sortieEnEdition,
            'contenuTexte' => $contenuTexte,
            'contenuTexte2' => $contenuTexte2,
            'isEditing' => $isEditing,
        ]);
    }

    public function supprimerSortie(int $id): void
    {
        session_start();

        // 1. Vérification de l'authentification
        if (!$this->isUserLoggedIn()) {
            $this->redirect("/admin/login");
            return;
        }

        // 2. Récupérer l'objet Sortie à supprimer
        $sortie = $this->entityManager->getRepository(Sortie::class)->find($id);

        if (!$sortie) {
            $_SESSION['error_message'] = "Erreur : La sortie que vous tentez de supprimer n'existe pas.";
            $this->redirect("/avva-admin/sortie");
            return;
        }

        // Sauvegarde du titre pour le message de succès
        $titreSortie = htmlspecialchars($sortie->getTitre());

        try {
            // --- SYNCHRONISATION : Suppression de l'événement lié dans DateEvent ---

            // Récupérer la catégorie "Sortie Vélo"
            // $categorieSortie = $this->entityManager->getRepository(CategorieEvent::class)->find(4);

            // if ($categorieSortie) {
            //     // Recherche de l'événement DateEvent correspondant par titre, date et catégorie
            //     $eventASupprimer = $this->entityManager->getRepository(DateEvent::class)->findOneBy([
            //         'titre' => $sortie->getTitre(),
            //         'dateStart' => $sortie->getDate(), // Utilise la date de la Sortie pour la recherche
            //         'categorieEvent' => $categorieSortie,
            //     ]);

            //     if ($eventASupprimer) {
            //         // Suppression de l'événement trouvé
            //         $this->entityManager->remove($eventASupprimer);
            //         // Le flush sera fait à la fin pour les deux suppressions
            //     } else {
            //         // Alerte si l'événement n'est pas trouvé (optionnel, mais utile pour le débogage)
            //         error_log("Avertissement: L'événement DateEvent pour la sortie '{$titreSortie}' n'a pas été trouvé pour synchronisation de suppression.");
            //     }
            // }

            // --- Suppression de l'entité Sortie principale ---
            $this->entityManager->remove($sortie);

            // Exécution des suppressions
            $this->entityManager->flush();

            $_SESSION['success_message'] = "La sortie " . $titreSortie . " a été supprimée avec succès et son événement a été retiré du calendrier.";

        } catch (\Exception $e) {
            // Gérer les erreurs de base de données (e.g., contrainte d'intégrité)
            $_SESSION['error_message'] = "Erreur lors de la suppression de la sortie et de son événement : " . $e->getMessage();
        }

        // 4. Redirection vers la liste des sorties
        $this->redirect("/avva-admin/sortie");
        return;
    }

    public function gestionMessageApresSortieHebdomadaire(): void
    {
        session_start();

        // Configuration de base
        $active12 = true;
        $error = '';
        $success = '';

        // 1. Vérification de l'authentification
        if (!$this->isUserLoggedIn()) {
            $this->redirect("/admin/login");
            return;
        }

        $user = $_SESSION['user'];
        $pages = $this->entityManager->getRepository(Page::class)->findAll();
        $repository = $this->entityManager->getRepository(MessageApresSortieHebdomadaire::class);

        // 2. Récupération du texte actuel (on suppose qu'il n'y en a qu'un seul)
        $messageEntite = $repository->findOneBy([]) ?? new MessageApresSortieHebdomadaire();
        $contenuTexte = $messageEntite->getMessage() ?? '';
        // $couleurTexte = $texteEntite->getCouleurDefilementTexte() ?? '';
        // $fondTexte = $texteEntite->getFondDefilementTexte() ?? '';
        // $tailleTexte = $texteEntite->getTailleDefilementTexte() ?? '';
        // $positionTexte = $texteEntite->getPositionDefilementTexte() ?? '';

        // 3. Traitement de la soumission du formulaire
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                $nouveauTexte = $_POST['message_apres_sortie_hebdomadaire'] ?? '';
                // $nouvelleCouleurTexte = $_POST['couleur_defilement_texte'] ?? '';
                // $nouveauFondTexte = $_POST['fond_defilement_texte'] ?? '';
                // $nouvelleTailleTexte = $_POST['taille_defilement_texte'] ?? '';
                // $nouvellePositionTexte = $_POST['position_defilement_texte'] ?? '';

                if (empty($nouveauTexte)) {
                    throw new \Exception("Le message après la sortie hebdomadaire ne peut pas être vide.");
                }

                // Mise à jour de l'entité
                $messageEntite->setMessage($nouveauTexte);
                // $texteEntite->setCouleurDefilementTexte($nouvelleCouleurTexte);
                // $texteEntite->setFondDefilementTexte($nouveauFondTexte);
                // $texteEntite->setTailleDefilementTexte($nouvelleTailleTexte);
                // $texteEntite->setPositionDefilementTexte($nouvellePositionTexte);

                // Persistance si c'est un nouvel objet, sinon Doctrine suit déjà l'objet
                if (!$messageEntite->getId()) {
                    $this->entityManager->persist($messageEntite);
                }

                $this->entityManager->flush();

                $_SESSION['success_message'] = "Le message après la sortie hebdomadaire a été mis à jour avec succès.";
                $this->redirect("/avva-admin/sortie"); // Adaptez l'URL de redirection
                return;

            } catch (\Exception $e) {
                $error = $e->getMessage();
                $contenuTexte = $_POST['message_apres_sortie_hebdomadaire'] ?? $contenuTexte;
                // $couleurTexte = $_POST['couleur_defilement_texte'] ?? $couleurTexte;
                // $fondTexte = $_POST['fond_defilement_texte'] ?? $fondTexte;
                // $tailleTexte = $_POST['taille_defilement_texte'] ?? $tailleTexte;
                // $positionTexte = $_POST['position_defilement_texte'] ?? $positionTexte;
            }
        }

        // Récupération du message de succès en session s'il existe
        if (isset($_SESSION['success_message'])) {
            $success = $_SESSION['success_message'];
            unset($_SESSION['success_message']);
        }

        // 4. Rendu de la vue
        $this->render('/admin/pages/gestion-sorties', [
            'user' => $user,
            'active12' => $active12,
            'pages' => $pages,
            'contenuTexte' => $contenuTexte,
            // 'couleurTexte' => $couleurTexte,
            // 'fondTexte' => $fondTexte,
            // 'tailleTexte' => $tailleTexte,
            // 'positionTexte' => $positionTexte,
            'error' => $error,
            'success' => $success
        ]);
    }

    public function gestionMessageSortieHebdomadaireADefinir(): void
    {
        session_start();

        // Configuration de base
        $active12 = true;
        $error = '';
        $success = '';

        // 1. Vérification de l'authentification
        if (!$this->isUserLoggedIn()) {
            $this->redirect("/admin/login");
            return;
        }

        $user = $_SESSION['user'];
        $pages = $this->entityManager->getRepository(Page::class)->findAll();
        $repository = $this->entityManager->getRepository(MessageSortieHebdomadaireADefinir::class);

        // 2. Récupération du texte actuel (on suppose qu'il n'y en a qu'un seul)
        $messageEntite = $repository->findOneBy([]) ?? new MessageSortieHebdomadaireADefinir();
        $contenuTexte = $messageEntite->getMessage() ?? '';
        // $couleurTexte = $texteEntite->getCouleurDefilementTexte() ?? '';
        // $fondTexte = $texteEntite->getFondDefilementTexte() ?? '';
        // $tailleTexte = $texteEntite->getTailleDefilementTexte() ?? '';
        // $positionTexte = $texteEntite->getPositionDefilementTexte() ?? '';

        // 3. Traitement de la soumission du formulaire
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                $nouveauTexte = $_POST['message_sortie_hebdomadaire_a_definir'] ?? '';
                // $nouvelleCouleurTexte = $_POST['couleur_defilement_texte'] ?? '';
                // $nouveauFondTexte = $_POST['fond_defilement_texte'] ?? '';
                // $nouvelleTailleTexte = $_POST['taille_defilement_texte'] ?? '';
                // $nouvellePositionTexte = $_POST['position_defilement_texte'] ?? '';

                if (empty($nouveauTexte)) {
                    throw new \Exception("Le message de la sortie hebdomadaire à définir ne peut pas être vide.");
                }

                // Mise à jour de l'entité
                $messageEntite->setMessage($nouveauTexte);
                // $texteEntite->setCouleurDefilementTexte($nouvelleCouleurTexte);
                // $texteEntite->setFondDefilementTexte($nouveauFondTexte);
                // $texteEntite->setTailleDefilementTexte($nouvelleTailleTexte);
                // $texteEntite->setPositionDefilementTexte($nouvellePositionTexte);

                // Persistance si c'est un nouvel objet, sinon Doctrine suit déjà l'objet
                if (!$messageEntite->getId()) {
                    $this->entityManager->persist($messageEntite);
                }

                $this->entityManager->flush();

                $_SESSION['success_message'] = "Le message de la sortie hebdomadaire à définir a été mis à jour avec succès.";
                $this->redirect("/avva-admin/sortie"); // Adaptez l'URL de redirection
                return;

            } catch (\Exception $e) {
                $error = $e->getMessage();
                $contenuTexte = $_POST['message_sortie_hebdomadaire_a_definir'] ?? $contenuTexte;
                // $couleurTexte = $_POST['couleur_defilement_texte'] ?? $couleurTexte;
                // $fondTexte = $_POST['fond_defilement_texte'] ?? $fondTexte;
                // $tailleTexte = $_POST['taille_defilement_texte'] ?? $tailleTexte;
                // $positionTexte = $_POST['position_defilement_texte'] ?? $positionTexte;
            }
        }

        // Récupération du message de succès en session s'il existe
        if (isset($_SESSION['success_message'])) {
            $success = $_SESSION['success_message'];
            unset($_SESSION['success_message']);
        }

        // 4. Rendu de la vue
        $this->render('/admin/pages/gestion-sorties', [
            'user' => $user,
            'active12' => $active12,
            'pages' => $pages,
            'contenuTexte2' => $contenuTexte,
            // 'couleurTexte' => $couleurTexte,
            // 'fondTexte' => $fondTexte,
            // 'tailleTexte' => $tailleTexte,
            // 'positionTexte' => $positionTexte,
            'error' => $error,
            'success' => $success
        ]);
    }

    /**
     * Gère l'upload d'un fichier et retourne le chemin relatif pour la BDD.
     * @param string $fileKey Clé dans $_FILES (ex: 'fichier_gpx').
     * @param string $targetSubDir Sous-répertoire de UPLOAD_DIR (ex: 'gpx_files').
     * @param string|null $requiredExtension Extension requise sans le point (ex: 'gpx').
     * @return string|null Chemin relatif du fichier ou null si aucun fichier n'a été uploadé.
     * @throws \Exception En cas d'erreur de téléchargement ou de validation.
     */
    private function handleFileUpload(string $fileKey, string $targetSubDir, string $requiredExtension = null): ?string
    {
        // Vérifie si un fichier a été soumis ou s'il y a une erreur
        if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] === UPLOAD_ERR_NO_FILE) {
            return null; // Aucun fichier soumis
        }

        $file = $_FILES[$fileKey];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            // Gère les erreurs d'upload (taille max, fichier corrompu, etc.)
            throw new \Exception("Erreur d'upload pour {$file['name']} (Code: {$file['error']}).");
        }

        $fileName = basename($file['name']);
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

        // 1. Validation de l'extension
        if ($requiredExtension && strtolower($fileExtension) !== strtolower($requiredExtension)) {
            throw new \Exception("Le fichier $fileName doit être un fichier .$requiredExtension.");
        }

        // 2. Création du chemin de destination
        $newFileName = uniqid($fileKey . '_', true) . '.' . $fileExtension;
        $fullTargetSubDir = $_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_DIR . $targetSubDir;

        // Créer les sous-dossiers si nécessaire
        if (!is_dir($fullTargetSubDir)) {
            @mkdir($fullTargetSubDir, 0777, true);
        }

        $targetPath = $fullTargetSubDir . '/' . $newFileName;

        // 3. Déplacement du fichier temporaire
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Retourne le chemin relatif (ex: 'images/photo_unique.jpg') pour la BDD
            return $targetSubDir . '/' . $newFileName;
        } else {
            throw new \Exception("Échec du déplacement du fichier téléchargé. Vérifiez les permissions du répertoire : " . $fullTargetSubDir);
        }
    }

    /**
     * Gère la création d'une nouvelle Randonnée et de ses Circuits associés.
     */
    public function creerRandonnee(): void
    {
        session_start();

        // 🔑 INITIALISATION DU MODULE STRIPE PHP
        // Assurez-vous que l'autoloader de Composer est chargé (e.g., via require_once 'vendor/autoload.php'; dans le fichier bootstrap/index.php)
        try {
            // IMPORTANT : Remplacez par votre clé secrète Stripe (sk_live_... ou sk_test_...).
            // Utilisez une variable d'environnement ou un fichier de configuration sécurisé pour la production.
            $stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'] ?? '';
            \Stripe\Stripe::setApiKey($stripeSecretKey);
        } catch (\Exception $e) {
            // En cas d'échec d'initialisation (clé invalide, librairie manquante), journaliser et continuer
            error_log("Erreur d'initialisation de Stripe: " . $e->getMessage());
        }
        // ------------------------------------

        // Initialisation des variables pour la vue (mode création)
        $active11 = true;
        $isEditing = false;
        $fuseauHoraire = new \DateTimeZone('Europe/Paris');
        $error = '';

        // Valeurs par défaut (conservées pour le réaffichage en cas d'erreur)
        $titre = '';
        $descriptionCourte = '';
        $descriptionComplete = '';
        $lieuDepart = '';
        $dateDepart = (new \DateTime('now', $fuseauHoraire))->format('Y-m-d\TH:i');
        $coordonneesGps = null;
        $imagePrincipale = null;
        $couleurThematique = '#4CAF50';
        $afficherCarte = true;
        $modelePage = 'tpl_defaut';
        $nombreParticipantsMax = 0;
        $statutInscription = 'Ouvert';
        $estAnnulee = false;
        $messageAnnulation = null;
        $statutPublication = 'Brouillon';
        $notesInternes = null;

        // NOUVEAU : Structure pour les circuits incluant le prix Stripe par défaut
        $circuitsData = [
            [
                'nom' => 'Circuit Principal',
                'distance_km' => 0.0,
                'duree_heures' => 0.0,
                'denivele_positif' => 0,
                'difficulte' => 'Modéré',
                'fichier_gpx_path' => null,
                'est_principal' => true,
                'prix_inscription_moins_18_ans_licencie_centimes' => 000,
                'prix_inscription_moins_18_ans_non_licencie_centimes' => 400,
                'prix_inscription_adulte_licencie_centimes' => 700,
                'prix_inscription_adulte_non_licencie_centimes' => 1000,
                'type' => null,
            ]
        ];

        if (!$this->isUserLoggedIn()) {
            $this->redirect("/admin/login");
            return;
        }

        $user = $_SESSION['user'];
        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                // --- 1. Récupération des données POST ---

                // A. Infos de base 
                $titre = $_POST['titre_randonnee'] ?? $titre;
                $lieuDepart = $_POST['lieu_depart_randonnee'] ?? $lieuDepart;
                $dateString = $_POST['date_depart_randonnee'] ?? $dateDepart;
                $coordonneesGps = $_POST['coordonnees_gps_randonnee'] ?? $coordonneesGps;
                $descriptionCourte = $_POST['description_courte_randonnee'] ?? $descriptionCourte;
                $descriptionComplete = $_POST['description_complete_randonnee'] ?? $descriptionComplete;

                // B. Circuits POST DATA
                $postedCircuits = $_POST['circuits'] ?? [];
                $principalIndex = (int) ($_POST['circuits_est_principal'] ?? 0);

                // C. Affichage & Médias 
                $couleurThematique = $_POST['couleur_thematique_randonnee'] ?? $couleurThematique;
                $afficherCarte = isset($_POST['afficher_carte_randonnee']);
                $modelePage = $_POST['modele_page_randonnee'] ?? $modelePage;

                // D. Planification & Événement 
                $nombreParticipantsMax = (int) ($_POST['nombre_participants_max_randonnee'] ?? $nombreParticipantsMax);
                $statutInscription = $_POST['statut_inscription_randonnee'] ?? $statutInscription;
                $estAnnulee = isset($_POST['est_annulee_randonnee']);
                $messageAnnulation = $_POST['message_annulation_randonnee'] ?? $messageAnnulation;

                // E. Administration & SEO 
                $statutPublication = $_POST['statut_publication_randonnee'] ?? $statutPublication;
                $notesInternes = $_POST['notes_internes_randonnee'] ?? $notesInternes;


                // --- 2. Validation de base ---
                if (empty($titre) || empty($dateString) || empty($lieuDepart)) {
                    throw new \Exception("Le titre, le lieu de départ, la date sont obligatoires.");
                }
                if (empty($postedCircuits)) {
                    throw new \Exception("Vous devez définir au moins un circuit pour cette randonnée.");
                }

                // Création de l'objet DateTime
                $date = \DateTime::createFromFormat('Y-m-d\TH:i', $dateString, $fuseauHoraire);
                if ($date === false) {
                    throw new \Exception("Format de date invalide.");
                }
                if ($date instanceof \DateTimeImmutable) {
                    $date = \DateTime::createFromImmutable($date);
                }

                // --- 3. Upload et Préparation des données complexes ---
                $categorieRandonnee = $this->entityManager->getRepository(CategorieEvent::class)->find(6);

                // 3.1 Upload de l'Image Principale 
                $imagePrincipalePath = $this->handleFileUpload('image_principale_randonnee', 'images');
                $imagePrincipale = $imagePrincipalePath ?? $imagePrincipale;

                // 3.2 Gestion et Upload des fichiers GPX et Préparation des données des circuits

                $gpxFiles = $_FILES['circuits'] ?? ['name' => [], 'type' => [], 'tmp_name' => [], 'error' => [], 'size' => []];
                $circuitsData = [];

                foreach ($postedCircuits as $index => $data) {

                    // Récupération du prix en euros et conversion en centimes
                    $prixInscriptionMoins18AnsLicencie = (float) ($data['prix_inscription_moins_18_ans_licencie'] ?? 0.0);
                    $prixInscriptionMoins18AnsNonLicencie = (float) ($data['prix_inscription_moins_18_ans_non_licencie'] ?? 0.0);
                    $prixInscriptionAdulteLicencie = (float) ($data['prix_inscription_adulte_licencie'] ?? 0.0);
                    $prixInscriptionAdulteNonLicencie = (float) ($data['prix_inscription_adulte_non_licencie'] ?? 0.0);

                    $circuit = [
                        'nom' => $data['nom'],
                        'distance_km' => (float) ($data['distance_km'] ?? 0.0),
                        'duree_heures' => (float) ($data['duree_heures'] ?? 0.0),
                        'denivele_positif' => (int) ($data['denivele_positif'] ?? 0),
                        'difficulte' => $data['difficulte'] ?? 'Modéré',
                        'fichier_gpx_path' => null,
                        'est_principal' => ($index === $principalIndex),
                        // Conversion du prix en centimes
                        'prix_inscription_moins_18_ans_licencie_centimes' => (int) round($prixInscriptionMoins18AnsLicencie * 100),
                        'prix_inscription_moins_18_ans_non_licencie_centimes' => (int) round($prixInscriptionMoins18AnsNonLicencie * 100),
                        'prix_inscription_adulte_licencie_centimes' => (int) round($prixInscriptionAdulteLicencie * 100),
                        'prix_inscription_adulte_non_licencie_centimes' => (int) round($prixInscriptionAdulteNonLicencie * 100),
                        'type' => $data['type'],
                    ];

                    // Logique d'upload de fichier GPX (inchangée)
                    if (isset($gpxFiles['error'][$index]['fichier_gpx']) && $gpxFiles['error'][$index]['fichier_gpx'] !== UPLOAD_ERR_NO_FILE) {

                        $tempFile = [
                            'name' => $gpxFiles['name'][$index]['fichier_gpx'],
                            'type' => $gpxFiles['type'][$index]['fichier_gpx'],
                            'tmp_name' => $gpxFiles['tmp_name'][$index]['fichier_gpx'],
                            'error' => $gpxFiles['error'][$index]['fichier_gpx'],
                            'size' => $gpxFiles['size'][$index]['fichier_gpx'],
                        ];

                        if ($tempFile['error'] !== UPLOAD_ERR_OK) {
                            throw new \Exception("Erreur d'upload pour le GPX du circuit {$circuit['nom']} (Code: {$tempFile['error']}).");
                        }

                        $fileName = basename($tempFile['name']);
                        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

                        if (strtolower($fileExtension) !== 'gpx') {
                            throw new \Exception("Le fichier GPX du circuit {$circuit['nom']} doit être un fichier .gpx.");
                        }

                        $newFileName = uniqid('gpx_files_', true) . '.' . $fileExtension;
                        $targetSubDir = 'gpx_files';
                        $fullTargetSubDir = $_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_DIR . $targetSubDir;

                        if (!is_dir($fullTargetSubDir)) {
                            @mkdir($fullTargetSubDir, 0777, true);
                        }

                        $targetPath = $fullTargetSubDir . '/' . $newFileName;

                        if (move_uploaded_file($tempFile['tmp_name'], $targetPath)) {
                            $circuit['fichier_gpx_path'] = $targetSubDir . '/' . $newFileName;
                        } else {
                            throw new \Exception("Échec du déplacement du fichier GPX pour le circuit {$circuit['nom']}.");
                        }
                    }

                    $circuitsData[] = $circuit;
                }


                // --- 4. Création et Hydratation des entités ---

                // Entité Randonnee 
                $nouvelleRandonnee = new Randonnee();
                $nouvelleRandonnee->setTitre($titre);
                $nouvelleRandonnee->setSlug($this->generateSlug($titre));
                $nouvelleRandonnee->setDescriptionCourte($descriptionCourte);
                $nouvelleRandonnee->setDescriptionComplete($descriptionComplete);
                $nouvelleRandonnee->setLieuDepart($lieuDepart);
                $nouvelleRandonnee->setCoordonneesGps(empty($coordonneesGps) ? null : $coordonneesGps);
                $nouvelleRandonnee->setDateRandonnee($date);
                $nouvelleRandonnee->setImagePrincipale(empty($imagePrincipale) ? null : $imagePrincipale);
                $nouvelleRandonnee->setCouleurThematique($couleurThematique);
                $nouvelleRandonnee->setAfficherCarte($afficherCarte);
                $nouvelleRandonnee->setModelePage($modelePage);
                $nouvelleRandonnee->setNombreParticipantsMax($nombreParticipantsMax);
                $nouvelleRandonnee->setStatutInscription($statutInscription);
                $nouvelleRandonnee->setEstAnnulee($estAnnulee);
                $nouvelleRandonnee->setMessageAnnulation(empty($messageAnnulation) ? null : $messageAnnulation);
                $nouvelleRandonnee->setStatutPublication($statutPublication);
                $nouvelleRandonnee->setNotesInternes(empty($notesInternes) ? null : $notesInternes);

                $this->entityManager->persist($nouvelleRandonnee);

                // Entité CircuitRandonnee 
                foreach ($circuitsData as $circuit) {
                    $nouveauCircuit = new CircuitRandonnee();

                    $nouveauCircuit->setNom($circuit['nom']);
                    $nouveauCircuit->setEstPrincipal($circuit['est_principal']);
                    $nouveauCircuit->setDistanceKm($circuit['distance_km']);
                    $nouveauCircuit->setDureeHeures($circuit['duree_heures']);
                    $nouveauCircuit->setDenivelePositif($circuit['denivele_positif']);
                    $nouveauCircuit->setDifficulte($circuit['difficulte']);

                    // ENREGISTREMENT DU PRIX EN CENTIMES
                    $nouveauCircuit->setPrixInscriptionMoins18AnsLicencieCentimes($circuit['prix_inscription_moins_18_ans_licencie_centimes']);
                    $nouveauCircuit->setPrixInscriptionMoins18AnsNonLicencieCentimes($circuit['prix_inscription_moins_18_ans_non_licencie_centimes']);
                    $nouveauCircuit->setPrixInscriptionAdulteLicencieCentimes($circuit['prix_inscription_adulte_licencie_centimes']);
                    $nouveauCircuit->setPrixInscriptionAdulteNonLicencieCentimes($circuit['prix_inscription_adulte_non_licencie_centimes']);

                    $nouveauCircuit->setType($circuit['type']);

                    $nouveauCircuit->setFichierGpx(empty($circuit['fichier_gpx_path']) ? null : $circuit['fichier_gpx_path']);

                    $nouvelleRandonnee->addCircuit($nouveauCircuit);
                    $this->entityManager->persist($nouveauCircuit);
                }

                // Entité DateEvent (Synchronisation Calendrier)
                $event = new DateEvent();
                $event->setTitre($titre);
                $event->setDescription($descriptionCourte);
                $event->setDateStart($date);
                $event->setCategorieEvent($categorieRandonnee);

                $this->entityManager->persist($event);

                // --- 5. Exécution ---
                $this->entityManager->flush();

                $_SESSION['success_message'] = "La randonnée " . htmlspecialchars($titre) . " et ses " . count($circuitsData) . " circuits ont été créés avec succès.";

                // Redirection (PRG pattern)
                $this->redirect("/avva-admin/randonnee");
                return;

            } catch (\Exception $e) {
                $error = "Erreur lors de la création : " . $e->getMessage();

                $randonnees = $this->entityManager->getRepository(Randonnee::class)->findAll();
            }
        }

        // Affichage de la vue
        $randonnees = $this->entityManager->getRepository(Randonnee::class)->findAll();

        // Préparation de $circuitsData pour la vue.
        $firstCircuit = $circuitsData[0] ?? ['distance_km' => 0.0, 'duree_heures' => 0.0, 'denivele_positif' => 0, 'difficulte' => 'Modéré', 'fichier_gpx_path' => null, 'prix_stripe_centimes' => 1000, 'type' => null];

        $this->render('/admin/pages/gestion-randonnees', [
            'user' => $user,
            'active11' => $active11,
            'pages' => $pages,
            'randonnees' => $randonnees,

            // Variables pour le formulaire de base
            'titre' => $titre,
            'descriptionCourte' => $descriptionCourte,
            'descriptionComplete' => $descriptionComplete,
            'lieuDepart' => $lieuDepart,
            'dateDepart' => $dateDepart,
            'coordonneesGps' => $coordonneesGps,
            'imagePrincipale' => $imagePrincipale,
            'couleurThematique' => $couleurThematique,
            'afficherCarte' => $afficherCarte,
            'modelePage' => $modelePage,
            'nombreParticipantsMax' => $nombreParticipantsMax,
            'statutInscription' => $statutInscription,
            'estAnnulee' => $estAnnulee,
            'messageAnnulation' => $messageAnnulation,
            'statutPublication' => $statutPublication,
            'notesInternes' => $notesInternes,

            // Données du circuit (pour le template dynamique)
            'circuitsData' => $circuitsData,

            // Variables pour la compatibilité
            'distanceKm' => $firstCircuit['distance_km'],
            'dureeHeures' => $firstCircuit['duree_heures'],
            'denivelePositif' => $firstCircuit['denivele_positif'],
            'difficulte' => $firstCircuit['difficulte'],
            'fichierGpx' => $firstCircuit['fichier_gpx_path'],
            'prix_inscription_moins_18_ans_licencie_centimes' => $firstCircuit['prix_inscription_moins_18_ans_licencie_centimes'],
            'prix_inscription_moins_18_ans_non_licencie_centimes' => $firstCircuit['prix_inscription_moins_18_ans_non_licencie_centimes'],
            'prix_inscription_adulte_licencie_centimes' => $firstCircuit['prix_inscription_adulte_licencie_centimes'],
            'prix_inscription_adulte_non_licencie_centimes' => $firstCircuit['prix_inscription_adulte_non_licencie_centimes'],

            'error' => $error,
            'utilisateur' => $_SESSION['utilisateur'] ?? null,
            'randonneeEnEdition' => null,
            'isEditing' => $isEditing,
        ]);
    }

    /**
     * Gère la modification d'une Randonnée existante et de ses Circuits associés.
     *
     * @param int $id L'ID de la randonnée à modifier.
     */
    public function modifierRandonnee(int $id): void
    {
        session_start();

        $active11 = true;
        $isEditing = true;
        $fuseauHoraire = new \DateTimeZone('Europe/Paris');
        $error = '';
        $randonneeEnEdition = null;

        if (!$this->isUserLoggedIn()) {
            $this->redirect("/admin/login");
            return;
        }

        // 🔑 INITIALISATION DU MODULE STRIPE PHP
        try {
            // IMPORTANT : Utiliser la même logique d'initialisation que dans creerRandonnee
            $stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'] ?? '';
            \Stripe\Stripe::setApiKey($stripeSecretKey);
        } catch (\Exception $e) {
            error_log("Erreur d'initialisation de Stripe: " . $e->getMessage());
        }
        // ------------------------------------

        // 1. Récupération de l'entité Randonnee
        $randonneeEnEdition = $this->entityManager->getRepository(Randonnee::class)->find($id);

        if (!$randonneeEnEdition) {
            $_SESSION['error_message'] = "Erreur : Randonnée avec l'ID {$id} non trouvée.";
            $this->redirect("/avva-admin/randonnee");
            return;
        }

        // --- Pré-remplissage des variables de la vue (Valeurs actuelles) ---

        // A. Variables Randonnee
        $titre = $randonneeEnEdition->getTitre();
        $descriptionCourte = $randonneeEnEdition->getDescriptionCourte();
        $descriptionComplete = $randonneeEnEdition->getDescriptionComplete();
        $lieuDepart = $randonneeEnEdition->getLieuDepart();
        // Le formatage pour l'input datetime-local
        $dateDepart = $randonneeEnEdition->getDateRandonnee()->format('Y-m-d\TH:i');
        $coordonneesGps = $randonneeEnEdition->getCoordonneesGps();
        $imagePrincipale = $randonneeEnEdition->getImagePrincipale();
        $couleurThematique = $randonneeEnEdition->getCouleurThematique();
        $afficherCarte = $randonneeEnEdition->isAfficherCarte();
        $modelePage = $randonneeEnEdition->getModelePage();
        $nombreParticipantsMax = $randonneeEnEdition->getNombreParticipantsMax();
        $statutInscription = $randonneeEnEdition->getStatutInscription();
        $estAnnulee = $randonneeEnEdition->isEstAnnulee();
        $messageAnnulation = $randonneeEnEdition->getMessageAnnulation();
        $statutPublication = $randonneeEnEdition->getStatutPublication();
        $notesInternes = $randonneeEnEdition->getNotesInternes();

        // B. Circuits existants pour le pré-remplissage du formulaire
        $circuitsData = $randonneeEnEdition->getCircuits()->map(function ($circuit) {
            // Le tableau est rempli avec l'entité CircuitRandonnee elle-même (pour la vue)
            // La vue se charge d'extraire les propriétés et de convertir prixStripeCentimes en Euros.
            return $circuit;
        })->toArray();

        // S'assurer qu'il y a au moins un circuit (même si vide) pour afficher le formulaire
        if (empty($circuitsData)) {
            // Ceci est une entité temporaire ou un tableau pour la vue uniquement
            $circuitsData[] = [
                'id' => null,
                'nom' => 'Nouveau Circuit Principal',
                'distance_km' => 0.0,
                'duree_heures' => 0.0,
                'denivele_positif' => 0,
                'difficulte' => 'Modéré',
                'fichier_gpx_path' => null,
                'est_principal' => true,
                'prix_inscription_moins_18_ans_licencie_centimes' => 000,
                'prix_inscription_moins_18_ans_non_licencie_centimes' => 400,
                'prix_inscription_adulte_licencie_centimes' => 700,
                'prix_inscription_adulte_non_licencie_centimes' => 1000,
                'type' => null
            ];
        }


        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                // --- 2. Récupération des données POST ---

                // ... (Récupération des données Randonnee inchangée)
                $titre = $_POST['titre_randonnee'] ?? $titre;
                $lieuDepart = $_POST['lieu_depart_randonnee'] ?? $lieuDepart;
                $dateString = $_POST['date_depart_randonnee'] ?? $dateDepart;
                $coordonneesGps = $_POST['coordonnees_gps_randonnee'] ?? $coordonneesGps;
                $descriptionCourte = $_POST['description_courte_randonnee'] ?? $descriptionCourte;
                $descriptionComplete = $_POST['description_complete_randonnee'] ?? $descriptionComplete;

                // B. Circuits POST DATA
                $postedCircuits = $_POST['circuits'] ?? [];
                $principalIndex = (int) ($_POST['circuits_est_principal'] ?? 0);

                // C. Affichage, Événement, SEO (Randonnee)
                $couleurThematique = $_POST['couleur_thematique_randonnee'] ?? $couleurThematique;
                $afficherCarte = isset($_POST['afficher_carte_randonnee']);
                $modelePage = $_POST['modele_page_randonnee'] ?? $modelePage;
                $nombreParticipantsMax = (int) ($_POST['nombre_participants_max_randonnee'] ?? $nombreParticipantsMax);
                $statutInscription = $_POST['statut_inscription_randonnee'] ?? $statutInscription;
                $estAnnulee = isset($_POST['est_annulee_randonnee']);
                $messageAnnulation = $_POST['message_annulation_randonnee'] ?? $messageAnnulation;
                $statutPublication = $_POST['statut_publication_randonnee'] ?? $statutPublication;
                $notesInternes = $_POST['notes_internes_randonnee'] ?? $notesInternes;

                // --- 3. Validation et Mise à Jour des données Randonnee ---

                if (empty($titre) || empty($dateString) || empty($lieuDepart)) {
                    throw new \Exception("Le titre, le lieu de départ, la date sont obligatoires.");
                }
                if (empty($postedCircuits)) {
                    throw new \Exception("Vous devez définir au moins un circuit pour cette randonnée.");
                }

                $date = \DateTime::createFromFormat('Y-m-d\TH:i', $dateString, $fuseauHoraire);
                if ($date === false) {
                    throw new \Exception("Format de date invalide.");
                }

                // Hydratation de l'entité Randonnee (inchangée)
                $randonneeEnEdition->setTitre($titre);
                $randonneeEnEdition->setSlug($this->generateSlug($titre));
                $randonneeEnEdition->setDateMiseAJour(new \DateTime());
                $randonneeEnEdition->setDescriptionCourte($descriptionCourte);
                $randonneeEnEdition->setDescriptionComplete($descriptionComplete);
                $randonneeEnEdition->setLieuDepart($lieuDepart);
                $randonneeEnEdition->setCoordonneesGps(empty($coordonneesGps) ? null : $coordonneesGps);
                $randonneeEnEdition->setDateRandonnee($date);

                // ... (Logique d'upload d'image et mise à jour des autres champs Randonnee) ...
                $imagePrincipalePath = $this->handleFileUpload('image_principale_randonnee', 'images');
                if ($imagePrincipalePath !== null) {
                    $randonneeEnEdition->setImagePrincipale($imagePrincipalePath);
                }

                $randonneeEnEdition->setCouleurThematique($couleurThematique);
                $randonneeEnEdition->setAfficherCarte($afficherCarte);
                $randonneeEnEdition->setModelePage($modelePage);
                $randonneeEnEdition->setNombreParticipantsMax($nombreParticipantsMax);
                $randonneeEnEdition->setStatutInscription($statutInscription);
                $randonneeEnEdition->setEstAnnulee($estAnnulee);
                $randonneeEnEdition->setMessageAnnulation(empty($messageAnnulation) ? null : $messageAnnulation);
                $randonneeEnEdition->setStatutPublication($statutPublication);
                $randonneeEnEdition->setNotesInternes(empty($notesInternes) ? null : $notesInternes);

                // --- 5. Synchronisation des Circuits (MISE À JOUR IMPORTANTE) ---

                $existingCircuits = $randonneeEnEdition->getCircuits()->toArray();
                $circuitsToKeepIds = [];
                $circuitsData = []; // Pour le réaffichage en cas d'erreur

                // Récupération des données des fichiers uploadés pour les circuits
                $gpxFiles = $_FILES['circuits'] ?? ['name' => [], 'error' => []];

                foreach ($postedCircuits as $index => $data) {
                    $circuitId = $data['id'] ?? null;
                    $circuit = null;
                    $gpxPath = $data['fichier_gpx_current'] ?? null; // Récupère l'ancien chemin GPX (champ caché)

                    // A. Identification ou Création de l'Entité Circuit
                    if (!empty($circuitId)) {
                        // Recherche du circuit existant
                        $circuit = current(array_filter($existingCircuits, fn($c) => $c->getId() == $circuitId));
                        $circuitsToKeepIds[] = $circuitId;
                    }

                    if (!$circuit) {
                        // Création si nouveau
                        $circuit = new CircuitRandonnee();
                        $randonneeEnEdition->addCircuit($circuit);
                    }

                    // B. Upload du Fichier GPX (si soumis)
                    // (La logique d'upload de fichier GPX est conservée ici)
                    if (isset($gpxFiles['error'][$index]['fichier_gpx']) && $gpxFiles['error'][$index]['fichier_gpx'] !== UPLOAD_ERR_NO_FILE) {

                        $tempFile = [
                            'name' => $gpxFiles['name'][$index]['fichier_gpx'],
                            'type' => $gpxFiles['type'][$index]['fichier_gpx'],
                            'tmp_name' => $gpxFiles['tmp_name'][$index]['fichier_gpx'],
                            'error' => $gpxFiles['error'][$index]['fichier_gpx'],
                            'size' => $gpxFiles['size'][$index]['fichier_gpx'],
                        ];

                        if ($tempFile['error'] !== UPLOAD_ERR_OK) {
                            throw new \Exception("Erreur d'upload pour le GPX du circuit {$data['nom']} (Code: {$tempFile['error']}).");
                        }
                        $fileExtension = pathinfo(basename($tempFile['name']), PATHINFO_EXTENSION);
                        if (strtolower($fileExtension) !== 'gpx') {
                            throw new \Exception("Le fichier GPX du circuit {$data['nom']} doit être un fichier .gpx.");
                        }

                        $newFileName = uniqid('gpx_files_', true) . '.' . $fileExtension;
                        $targetSubDir = 'gpx_files';
                        $fullTargetSubDir = $_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_DIR . $targetSubDir;

                        if (!is_dir($fullTargetSubDir)) {
                            @mkdir($fullTargetSubDir, 0777, true);
                        }

                        $targetPath = $fullTargetSubDir . '/' . $newFileName;

                        if (move_uploaded_file($tempFile['tmp_name'], $targetPath)) {
                            // Mise à jour du chemin GPX
                            $gpxPath = $targetSubDir . '/' . $newFileName;
                        } else {
                            throw new \Exception("Échec du déplacement du fichier GPX pour le circuit {$data['nom']}.");
                        }
                    } // Fin de l'upload GPX

                    // C. Hydratation de l'Entité Circuit (avec le prix Stripe)

                    // Conversion du prix Euros (float) en Centimes (int) pour la BDD
                    $prixInscriptionMoins18AnsLicencie = (float) ($data['prix_inscription_moins_18_ans_licencie'] ?? 0.0);
                    $prixInscriptionMoins18AnsLicencieCentimes = (int) round($prixInscriptionMoins18AnsLicencie * 100);

                    $prixInscriptionMoins18AnsNonLicencie = (float) ($data['prix_inscription_moins_18_ans_non_licencie'] ?? 0.0);
                    $prixInscriptionMoins18AnsNonLicencieCentimes = (int) round($prixInscriptionMoins18AnsNonLicencie * 100);

                    $prixInscriptionAdulteLicencie = (float) ($data['prix_inscription_adulte_licencie'] ?? 0.0);
                    $prixInscriptionAdulteLicencieCentimes = (int) round($prixInscriptionAdulteLicencie * 100);

                    $prixInscriptionAdulteNonLicencie = (float) ($data['prix_inscription_adulte_non_licencie'] ?? 0.0);
                    $prixInscriptionAdulteNonLicencieCentimes = (int) round($prixInscriptionAdulteNonLicencie * 100);

                    $circuit->setNom($data['nom']);
                    $circuit->setDistanceKm((float) $data['distance_km']);
                    $circuit->setDureeHeures((float) $data['duree_heures']);
                    $circuit->setDenivelePositif((int) $data['denivele_positif']);
                    $circuit->setDifficulte($data['difficulte']);
                    $circuit->setFichierGpx($gpxPath);
                    $circuit->setEstPrincipal($index === $principalIndex); // Définition du principal
                    $circuit->setPrixInscriptionMoins18AnsLicencieCentimes($prixInscriptionMoins18AnsLicencieCentimes);
                    $circuit->setPrixInscriptionMoins18AnsNonLicencieCentimes($prixInscriptionMoins18AnsNonLicencieCentimes);
                    $circuit->setPrixInscriptionAdulteLicencieCentimes($prixInscriptionAdulteLicencieCentimes);
                    $circuit->setPrixInscriptionAdulteNonLicencieCentimes($prixInscriptionAdulteNonLicencieCentimes);
                    $circuit->setType($data['type']);

                    $this->entityManager->persist($circuit);

                    // Préparation pour le réaffichage en cas d'erreur
                    $circuitsData[] = $circuit; // Stocke l'entité/objet pour la vue (si erreur)
                }

                // D. Suppression des Circuits Manquants
                foreach ($randonneeEnEdition->getCircuits() as $existingCircuit) {
                    if (!in_array($existingCircuit->getId(), $circuitsToKeepIds) && $existingCircuit->getId() !== null) {
                        $randonneeEnEdition->removeCircuit($existingCircuit);
                        $this->entityManager->remove($existingCircuit);
                    }
                }

                // --- 6. Mise à Jour de l'événement DateEvent ---

                // Retrouver l'événement lié (utiliser une relation bidirectionnelle si possible, sinon la recherche par titre)
                $event = $this->entityManager->getRepository(DateEvent::class)
                    ->findOneBy(['titre' => $titre]);

                if ($event) {
                    $event->setTitre($titre);
                    $event->setDescription($descriptionCourte);
                    $event->setDateStart($date);
                    $this->entityManager->persist($event);
                }


                // --- 7. Exécution ---
                $this->entityManager->flush();

                $_SESSION['success_message'] = "La randonnée '" . htmlspecialchars($titre) . "' et ses circuits ont été mis à jour avec succès.";

                $this->redirect("/avva-admin/randonnee");
                return;

            } catch (\Exception $e) {
                $error = "Erreur lors de la modification : " . $e->getMessage();
                // $circuitsData est déjà mise à jour par l'itération du POST et prête pour la vue

                // Re-fetch des randonnées pour l'affichage de la liste
                $randonnees = $this->entityManager->getRepository(Randonnee::class)->findAll();
            }
        }

        // Affichage de la vue (Préparation des données pour le template)
        $randonnees = $this->entityManager->getRepository(Randonnee::class)->findAll();
        $user = $_SESSION['user'];
        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        // Variables Circuit pour la compatibilité du template (doivent être dérivées de $circuitsData)
        // $circuitsData contient soit les entités Doctrine (GET), soit les entités hydratées/nouvelles (POST en cas d'erreur).
        $firstCircuit = $circuitsData[0] ?? null;

        $this->render('/admin/pages/gestion-randonnees', [
            'user' => $user,
            'active11' => $active11,
            'pages' => $pages,
            'randonnees' => $randonnees,

            // Variables de la Randonnée (Mises à jour ou pré-remplies)
            'titre' => $titre,
            'descriptionCourte' => $descriptionCourte,
            'descriptionComplete' => $descriptionComplete,
            'lieuDepart' => $lieuDepart,
            'dateDepart' => $dateDepart,
            'coordonneesGps' => $coordonneesGps,
            'imagePrincipale' => $imagePrincipale,
            'couleurThematique' => $couleurThematique,
            'afficherCarte' => $afficherCarte,
            'modelePage' => $modelePage,
            'nombreParticipantsMax' => $nombreParticipantsMax,
            'statutInscription' => $statutInscription,
            'estAnnulee' => $estAnnulee,
            'messageAnnulation' => $messageAnnulation,
            'statutPublication' => $statutPublication,
            'notesInternes' => $notesInternes,

            // Tableau des Circuits (utilisé pour boucler dans la vue)
            'circuitsData' => $circuitsData,

            // Variables du Circuit Principal (pour compatibilité)
            'distanceKm' => $firstCircuit ? $firstCircuit->getDistanceKm() : 0.0,
            'dureeHeures' => $firstCircuit ? $firstCircuit->getDureeHeures() : 0.0,
            'denivelePositif' => $firstCircuit ? $firstCircuit->getDenivelePositif() : 0,
            'difficulte' => $firstCircuit ? $firstCircuit->getDifficulte() : 'Modéré',
            'fichierGpx' => $firstCircuit ? $firstCircuit->getFichierGpx() : null,
            // Prix en centimes du circuit principal pour compatibilité si le template en a besoin
            'prixInscriptionMoins18AnsLicencieCentimes' => $firstCircuit ? $firstCircuit->getPrixInscriptionMoins18AnsLicencieCentimes() : 0,
            'prixInscriptionMoins18AnsNonLicencieCentimes' => $firstCircuit ? $firstCircuit->getPrixInscriptionMoins18AnsNonLicencieCentimes() : 0,
            'prixInscriptionAdulteLicencieCentimes' => $firstCircuit ? $firstCircuit->getPrixInscriptionAdulteLicencieCentimes() : 0,
            'prixInscriptionAdulteNonLicencieCentimes' => $firstCircuit ? $firstCircuit->getPrixInscriptionAdulteNonLicencieCentimes() : 0,

            'error' => $error,
            'utilisateur' => $_SESSION['utilisateur'] ?? null,
            'randonneeEnEdition' => $randonneeEnEdition,
            'isEditing' => $isEditing,
        ]);
    }

    /**
     * Supprime une Randonnée, ses circuits associés, son événement de calendrier
     * et les fichiers physiques (Image Principale et Fichiers GPX) liés.
     *
     * @param int $id L'ID de la randonnée à supprimer.
     */
    public function supprimerRandonnee(int $id): void
    {
        session_start();

        // 1. Vérification de l'authentification
        if (!$this->isUserLoggedIn()) {
            $this->redirect("/admin/login");
            return;
        }

        // 2. Récupération de la Randonnée et vérification
        $randonneeASupprimer = $this->entityManager->getRepository(Randonnee::class)->find($id);
        $categorieRandonnee = $this->entityManager->getRepository(CategorieEvent::class)->find(6);

        if (!$randonneeASupprimer) {
            $_SESSION['error_message'] = "Erreur : Randonnée avec l'ID {$id} non trouvée.";
            $this->redirect("/avva-admin/randonnee");
            return;
        }

        $titreRandonnee = $randonneeASupprimer->getTitre();

        try {
            // --- 3. Suppression des Fichiers Physiques (Crucial) ---

            // 3.1. Suppression de l'Image Principale
            $imagePath = $randonneeASupprimer->getImagePrincipale();
            if ($imagePath) {
                $fullPath = $_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_DIR . $imagePath;
                if (file_exists($fullPath)) {
                    @unlink($fullPath); // @ pour ignorer les erreurs de permission si elles surviennent
                }
            }

            // 3.2. Suppression des Fichiers GPX de tous les circuits
            foreach ($randonneeASupprimer->getCircuits() as $circuit) {
                $gpxPath = $circuit->getFichierGpx();
                if ($gpxPath) {
                    $fullPath = $_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_DIR . $gpxPath;
                    if (file_exists($fullPath)) {
                        @unlink($fullPath);
                    }
                }
            }

            // --- 4. Suppression de l'événement de calendrier (DateEvent) ---

            $event = $this->entityManager->getRepository(DateEvent::class)
                ->findOneBy([
                    'titre' => $titreRandonnee,
                    'categorieEvent' => $categorieRandonnee
                ]);

            if ($event) {
                $this->entityManager->remove($event);
            }

            // --- 5. Suppression de la Randonnee ---
            // 'orphanRemoval=true' sur la relation Randonnee::circuits() garantit 
            // que les entités CircuitRandonnee sont supprimées de la BDD.
            $this->entityManager->remove($randonneeASupprimer);

            // 6. Exécution
            $this->entityManager->flush();

            $_SESSION['success_message'] = "La randonnée '" . htmlspecialchars($titreRandonnee) . "' a été supprimée avec succès (y compris les fichiers et l'événement).";

        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Erreur lors de la suppression de la randonnée : " . $e->getMessage();
        }

        // 7. Redirection vers la liste
        $this->redirect("/avva-admin/randonnee");
    }

    /**
     * Génère un slug (chaine lisible pour URL) à partir d'une chaine de texte.
     * * @param string $text Le texte source (généralement le titre).
     * @return string Le slug formaté (ex: "ma-nouvelle-randonnee").
     */
    private function generateSlug(string $text): string
    {
        // 1. Convertir en minuscules
        $text = mb_strtolower($text, 'UTF-8');

        // 2. Supprimer les accents et les caractères non ASCII (normalisation)
        // Utile pour transformer 'Étape Aiguës' en 'etape-aigues'
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);

        // 3. Remplacer tous les caractères non alphanumériques (sauf le tiret) par un espace
        $text = preg_replace('/[^a-z0-9\s-]/', ' ', $text);

        // 4. Remplacer les espaces et les tirets multiples par un seul tiret
        $text = preg_replace('/[\s-]+/', '-', $text);

        // 5. Supprimer les tirets en début et fin de chaine
        $text = trim($text, '-');

        return $text;
    }

    public function apercuPage(int $id): void
    {
        session_start();

        if (!$this->isUserLoggedIn()) {
            header("Location: /avva-admin/login");
            exit;
        }


        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        $page = $this->entityManager->getRepository(Page::class)->find($id);

        $page_preview = [
            'nom' => $_POST['nom_page'] ?? $page->getNom(),
            'url' => $_POST['url_page'] ?? $page->getUrl(),
            'contenu' => $_POST['contenu_page'] ?? $page->getContenu(),
            'ordre_accueil' => $_POST['ordre_accueil_page'] ?? $page->getOrdrePageAccueil(),
        ];

        // Détecter live preview
        $isLive = isset($_GET['live']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

        if ($isLive) {
            // Affiche uniquement le contenu de la page pour le live preview
            echo $this->renderPartial('/pages/contenu-page', ['page' => $page_preview]);
            exit;
        } else {
            // Aperçu complet dans un onglet
            $this->render('/admin/pages/apercu-page', [
                'user' => $_SESSION['user'],
                'pages' => $pages,
                'page_preview' => $page_preview
            ]);
        }
    }

    public function apercuPageAPropos(int $id): void
    {
        session_start();

        if (!$this->isUserLoggedIn()) {
            header("Location: /avva-admin/login");
            exit;
        }

        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        $page = $this->entityManager->getRepository(PageAPropos::class)->find($id);

        $page_preview = [
            'contenu' => $_POST['contenu_page'] ?? $page->getContenu(),
        ];

        // Détecter live preview
        $isLive = isset($_GET['live']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

        if ($isLive) {
            // Affiche uniquement le contenu de la page pour le live preview
            echo $this->renderPartial('/pages/contenu-page', ['page' => $page_preview]);
            exit;
        } else {
            // Aperçu complet dans un onglet
            $this->render('/admin/pages/apercu-page', [
                'user' => $_SESSION['user'],
                'pages' => $pages,
                'page_preview' => $page_preview
            ]);
        }
    }

    public function apercuPageStatus(int $id): void
    {
        session_start();

        if (!$this->isUserLoggedIn()) {
            header("Location: /avva-admin/login");
            exit;
        }

        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        $page = $this->entityManager->getRepository(PageStatus::class)->find($id);

        $page_preview = [
            'contenu' => $_POST['contenu_page'] ?? $page->getContenu(),
        ];

        // Détecter live preview
        $isLive = isset($_GET['live']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

        if ($isLive) {
            // Affiche uniquement le contenu de la page pour le live preview
            echo $this->renderPartial('/pages/contenu-page', ['page' => $page_preview]);
            exit;
        } else {
            // Aperçu complet dans un onglet
            $this->render('/admin/pages/apercu-page', [
                'user' => $_SESSION['user'],
                'pages' => $pages,
                'page_preview' => $page_preview
            ]);
        }
    }

    public function apercuPagePresentation(int $id): void
    {
        session_start();

        if (!$this->isUserLoggedIn()) {
            header("Location: /avva-admin/login");
            exit;
        }

        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        $page = $this->entityManager->getRepository(PagePresentation::class)->find($id);

        $page_preview = [
            'contenu' => $_POST['contenu_page'] ?? $page->getContenu(),
        ];

        // Détecter live preview
        $isLive = isset($_GET['live']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

        if ($isLive) {
            // Affiche uniquement le contenu de la page pour le live preview
            echo $this->renderPartial('/pages/contenu-page', ['page' => $page_preview]);
            exit;
        } else {
            // Aperçu complet dans un onglet
            $this->render('/admin/pages/apercu-page', [
                'user' => $_SESSION['user'],
                'pages' => $pages,
                'page_preview' => $page_preview
            ]);
        }
    }

    public function ajaxSearch(): void
    {
        $query = isset($_GET['query']) ? trim($_GET['query']) : '';

        $results = ['pages' => [], 'events' => []];

        if ($query !== '') {
            $queryLower = mb_strtolower(substr($query, 0, 100));

            // Tente de déterminer si la requête est une date (format YYYY-MM-DD ou YYYY-MM ou YYYY, etc.)
            // On pourrait utiliser une expression régulière plus stricte, mais ici on est tolérant pour l'exemple.
            $isDateQuery = (bool) strtotime($query); // Vérifie si PHP peut interpréter la chaîne comme une date

            // Pages (la recherche par mot-clé reste inchangée)
            foreach ($this->entityManager->getRepository(Page::class)->findAll() as $page) {
                if (
                    strpos(mb_strtolower($page->getNom()), $queryLower) !== false ||
                    strpos(mb_strtolower($page->getContenu()), $queryLower) !== false
                ) {
                    $results['pages'][] = [
                        'title' => htmlspecialchars($page->getNom()),
                        'url' => '/page/' . $page->getUrl()
                    ];
                }
            }

            // Comptes rendus
            foreach ($this->entityManager->getRepository(DateEvent::class)->findAll() as $event) {
                $isMatch = false;

                // 1. Recherche par mot-clé (titre/contenu)
                if (
                    $event->getCompteRendu() &&
                    (strpos(mb_strtolower($event->getTitre()), $queryLower) !== false ||
                        strpos(mb_strtolower($event->getCompteRendu()), $queryLower) !== false)
                ) {
                    $isMatch = true;
                }

                // 2. Recherche par date
                if (!$isMatch && $isDateQuery) {
                    $eventDate = $event->getDateStart(); // Supposons que getDate() retourne un objet \DateTime

                    if ($eventDate instanceof \DateTime) {
                        $dateString = $eventDate->format('Y-m-d'); // Format standard pour la comparaison

                        // La date de l'événement est-elle similaire à la requête ?
                        // Exemple : si query est "2025", on vérifie si $dateString commence par "2025"
                        // Exemple : si query est "2025-12", on vérifie si $dateString commence par "2025-12"
                        if (strpos($dateString, $query) === 0) {
                            $isMatch = true;
                        }
                    }
                }

                if ($isMatch) {
                    // Inclure la date dans le titre pour une meilleure clarté dans les résultats de recherche
                    $dateTitle = '';
                    if ($event->getDateStart() instanceof \DateTime) {
                        $dateTitle = '[' . $event->getDateStart()->format('d/m/Y') . '] ';
                    }

                    $results['events'][] = [
                        'title' => htmlspecialchars($dateTitle . $event->getTitre()),
                        'url' => '/page/' . $event->getCategorieEvent()->getUrl() . '/compte-rendu/' . $event->getId()
                    ];
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode($results);
        exit;
    }

    public function listeMembres(): void
    {
        session_start();

        $active3 = true;

        // Vérifier si l'utilisateur est connecté
        if (!$this->isUserLoggedIn() || !$_SESSION['user']['idRole'] == 1) {
            header("Location: /avva-admin/login");
            exit;
        }

        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        $membres = $this->entityManager->getRepository(Membre::class)->findAll();

        $this->render('/admin/liste-membres', [
            'user' => $_SESSION['user'],
            'active3' => $active3,
            'pages' => $pages,
            'membres' => $membres
        ]);
    }

    private function generateUniqueCleActivationNumber(): int
    {
        $repository = $this->entityManager->getRepository(Membre::class);
        $min = 10000000; // 8 chiffres minimum
        $max = 99999999; // 8 chiffres maximum
        $cleActivation = 0;
        $isUnique = false;

        // Boucle pour générer un nombre aléatoire jusqu'à ce qu'il soit unique
        while (!$isUnique) {
            // Utilisation de random_int pour une génération cryptographiquement sécurisée
            $cleActivation = random_int($min, $max);

            // Vérifie si ce numéro existe déjà en base
            $existCleActivation = $repository->findOneBy(['cleActivation' => $cleActivation]);

            if (!$existCleActivation) {
                $isUnique = true;
            }
        }

        return $cleActivation;
    }

    public function creerMembre(): void
    {
        session_start();

        $active3 = true;

        // Vérifier si l'utilisateur est connecté et est admin
        if (!$this->isUserLoggedIn() || ($_SESSION['user']['idRole'] ?? null) != 1) {
            header("Location: /avva-admin/login");
            exit;
        }

        $user = $_SESSION['user'];

        $pages = $this->entityManager->getRepository(Page::class)->findAll();
        $membres = $this->entityManager->getRepository(Membre::class)->findAll();

        $numeroLicence = '';
        $nom = '';
        $prenom = '';
        $dateNaissance = '';
        $sexe = '';
        $numeroVoie = '';
        $nomVoie = '';
        $codePostal = '';
        $ville = '';
        $numeroTelephone = '';
        $email = '';
        $error = null;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $numeroLicence = trim($_POST["numero_licence_membre"] ?? '');
            $nom = trim($_POST["nom_membre"] ?? '');
            $prenom = trim($_POST["prenom_membre"] ?? '');
            $dateNaissance = trim($_POST["date_naissance_membre"] ?? '');
            $sexe = trim($_POST["sexe_membre"] ?? '');
            $numeroVoie = trim($_POST["numero_voie_membre"] ?? '');
            $nomVoie = trim($_POST["nom_voie_membre"] ?? '');
            $codePostal = trim($_POST["code_postal_membre"] ?? '');
            $ville = trim($_POST["ville_membre"] ?? '');
            $numeroTelephone = trim($_POST["numero_telephone_membre"] ?? '');
            $email = filter_var(trim($_POST["email_membre"] ?? ''), FILTER_VALIDATE_EMAIL);

            try {
                if (!$email) {
                    throw new \Exception("Veuillez fournir une adresse email valide.");
                }

                // Génération d'une clé d'activation unique
                $cleActivation = $this->generateUniqueCleActivationNumber();

                // 1. Recherche d'un membre existant avec cet email
                $membreExistant = $this->entityManager->getRepository(Membre::class)->findOneBy(['email' => $email]);

                if ($membreExistant) {
                    // Si l'email est associé à un compte d'essai (trial), on remplace/écrase les données
                    if ($membreExistant->getPlan() === 'trial') {
                        $membre = $membreExistant;
                        $membre->setPlan('member'); // Passage au plan définitif
                        $membre->setDateFinEssai(null); // Réinitialisation de la date d'essai
                    } else {
                        // Si le membre a déjà un compte actif autre que trial, on refuse le doublon
                        throw new \Exception("Un membre avec l'adresse email '$email' existe déjà.");
                    }
                } else {
                    // Sinon, création d'une nouvelle entité Membre
                    $membre = new Membre();
                    $membre->setEmail($email);
                    $membre->setPlan('member');
                }

                // 2. Mise à jour / Affectation de l'ensemble des données
                $membre->setNumeroLicence(!empty($numeroLicence) ? (int) $numeroLicence : null);
                $membre->setNom($nom);
                $membre->setPrenom($prenom);

                if (!empty($dateNaissance)) {
                    $membre->setDateNaissance(new \DateTime($dateNaissance));
                }

                $membre->setSexe($sexe);
                $membre->setNumeroVoie(!empty($numeroVoie) ? (int) $numeroVoie : null);
                $membre->setNomVoie($nomVoie);
                $membre->setCodePostal($codePostal);
                $membre->setVille($ville);
                $membre->setNumeroTelephone($numeroTelephone);
                $membre->setCleActivation($cleActivation);

                // Persistance BDD
                $this->entityManager->persist($membre);
                $this->entityManager->flush();

                // 3. Envoi du mail de confirmation avec la clé d'activation
                $this->envoyerCleActivationParMail($email, $prenom, $nom, $numeroLicence, $cleActivation);

                $_SESSION['success_message'] = "Le membre a été enregistré/converti avec succès !";
                $this->redirect("/avva-admin/liste-membres");
                return;

            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        $this->render('/admin/creer-membre', [
            'user' => $_SESSION['user'],
            'active3' => $active3,
            'pages' => $pages,
            'membres' => $membres,
            'error' => $error ?? '',
            'utilisateur' => $_SESSION['utilisateur'] ?? null,
        ]);
    }

    // Assurez-vous d'importer PhpSpreadsheet en haut de votre fichier si vous gérez le format Excel :
// use PhpOffice\PhpSpreadsheet\IOFactory;

    /**
     * Traitement de l'importation de membres (CSV / Excel)
     */
    public function importMembres(): void
    {
        session_start();

        if (!$this->isUserLoggedIn() || ($_SESSION['user']['idRole'] ?? null) != 1) {
            header("Location: /avva-admin/login");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['fichier_import'])) {
            $file = $_FILES['fichier_import'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['error_message'] = "Erreur lors du téléchargement du fichier.";
                $this->redirect("/avva-admin/liste-membres");
                return;
            }

            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filePath = $file['tmp_name'];

            $succesCount = 0;
            $erreurs = [];

            try {
                if ($extension !== 'csv') {
                    throw new \Exception("Veuillez importer un fichier au format .csv.");
                }

                $rows = $this->parseCsvFile($filePath);

                if (empty($rows)) {
                    throw new \Exception("Le fichier CSV est vide.");
                }

                // Extraction et nettoyage de l'entête
                $rawHeaders = array_shift($rows);
                $headers = array_map(function ($h) {
                    $clean = preg_replace('/[\x00-\x1F\x7F\xEF\xBB\xBF]/', '', (string) $h);
                    return strtolower(trim($clean));
                }, $rawHeaders);

                // Vérification : est-ce que les colonnes ont bien été découpées ?
                if (count($headers) <= 1) {
                    throw new \Exception("Le fichier semble utiliser un autre séparateur que la virgule. En-tête lue : '" . implode('', $headers) . "'");
                }

                foreach ($rows as $index => $row) {
                    $ligneNb = $index + 2;

                    // Passer les lignes vides
                    if (empty(array_filter($row, 'trim'))) {
                        continue;
                    }

                    // Reconstruction du tableau associatif
                    $data = [];
                    foreach ($headers as $colIndex => $colName) {
                        if ($colName !== '') {
                            $data[$colName] = $row[$colIndex] ?? '';
                        }
                    }

                    try {
                        $this->traiterEnregistrementMembre($data);
                        $succesCount++;
                    } catch (\Exception $e) {
                        $erreurs[] = "Ligne {$ligneNb} : " . $e->getMessage();
                    }
                }

                $this->entityManager->flush();

                if ($succesCount > 0) {
                    $_SESSION['success_message'] = "{$succesCount} membre(s) importé(s) / mis à jour avec succès.";
                }

                if (!empty($erreurs)) {
                    $msgErreurs = implode('<br>', array_slice($erreurs, 0, 5));
                    if (count($erreurs) > 5) {
                        $msgErreurs .= '<br>... et ' . (count($erreurs) - 5) . ' autre(s) erreur(s).';
                    }
                    $_SESSION['error_message'] = "Avertissements lors de l'import :<br>" . $msgErreurs;
                }

            } catch (\Exception $e) {
                $_SESSION['error_message'] = "Erreur lors de l'import : " . $e->getMessage();
            }
        }

        $this->redirect("/avva-admin/liste-membres");
    }

    /**
     * Logique métier d'enregistrement / mise à jour d'un membre à partir d'un tableau de données (CSV/Excel)
     */
    private function traiterEnregistrementMembre(array $data): void
    {
        // 1. Recherche dynamique ou fallback par pattern sur l'adresse email
        $emailRaw = '';
        foreach ($data as $key => $val) {
            $valTrim = trim((string) $val);
            if (in_array($key, ['email_membre', 'email', 'mail', 'adresse_email']) && !empty($valTrim)) {
                $emailRaw = $valTrim;
                break;
            }
            if (filter_var($valTrim, FILTER_VALIDATE_EMAIL)) {
                $emailRaw = $valTrim;
                break;
            }
        }

        $email = filter_var($emailRaw, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            throw new \Exception("Colonne email manquante ou adresse invalide sur cette ligne.");
        }

        // 2. Extraction et assainissement des autres colonnes
        $numeroLicenceRaw = $data['numero_licence_membre'] ?? $data['numero_licence'] ?? '';
        $nom = trim((string) ($data['nom_membre'] ?? $data['nom'] ?? ''));
        $prenom = trim((string) ($data['prenom_membre'] ?? $data['prenom'] ?? ''));
        $dateNaissance = trim((string) ($data['date_naissance_membre'] ?? $data['date_naissance'] ?? ''));
        $sexe = trim((string) ($data['sexe_membre'] ?? $data['sexe'] ?? ''));
        $numeroVoieRaw = $data['numero_voie_membre'] ?? $data['numero_voie'] ?? '';
        $nomVoie = trim((string) ($data['nom_voie_membre'] ?? $data['nom_voie'] ?? ''));
        $codePostal = trim((string) ($data['code_postal_membre'] ?? $data['code_postal'] ?? ''));
        $ville = trim((string) ($data['ville_membre'] ?? $data['ville'] ?? ''));
        $numeroTelephone = trim((string) ($data['numero_telephone_membre'] ?? $data['numero_telephone'] ?? ''));

        // Conversion typée des entiers
        $numeroLicenceInt = !empty($numeroLicenceRaw) ? (int) $numeroLicenceRaw : 0;
        $numeroLicenceNullable = !empty($numeroLicenceRaw) ? (int) $numeroLicenceRaw : null;
        $numeroVoieNullable = !empty($numeroVoieRaw) ? (int) $numeroVoieRaw : null;

        // 3. Gestion du membre en BDD (Mise à jour compte d'essai ou création)
        $membreExistant = $this->entityManager->getRepository(Membre::class)->findOneBy(['email' => $email]);

        if ($membreExistant) {
            if ($membreExistant->getPlan() === 'trial') {
                $membre = $membreExistant;
                $membre->setPlan('member');
                $membre->setDateFinEssai(null);
            } else {
                throw new \Exception("Un membre avec l'email '$email' existe déjà.");
            }
        } else {
            $membre = new Membre();
            $membre->setEmail($email);
            $membre->setPlan('member');
        }

        // 4. Clé d'activation unique
        $cleActivation = $this->generateUniqueCleActivationNumber();

        // 5. Hydratation de l'entité Membre
        $membre->setNumeroLicence($numeroLicenceNullable);
        $membre->setNom($nom);
        $membre->setPrenom($prenom);

        if (!empty($dateNaissance)) {
            try {
                $dt = new \DateTime($dateNaissance);
                $membre->setDateNaissance($dt);
            } catch (\Exception $e) {
                $dt = \DateTime::createFromFormat('d/m/Y', $dateNaissance);
                if ($dt) {
                    $membre->setDateNaissance($dt);
                }
            }
        }

        $membre->setSexe($sexe);
        $membre->setNumeroVoie($numeroVoieNullable);
        $membre->setNomVoie($nomVoie);
        $membre->setCodePostal($codePostal);
        $membre->setVille($ville);
        $membre->setNumeroTelephone($numeroTelephone);
        $membre->setCleActivation($cleActivation);

        // 6. Persistence Doctrine
        $this->entityManager->persist($membre);

        // 7. Envoi du mail d'activation (Transtypage strict en 'int' pour éviter le TypeError 500)
        $this->envoyerCleActivationParMail($email, $prenom, $nom, $numeroLicenceInt, $cleActivation);
    }

    /**
     * Lecture d'un fichier CSV avec séparateur virgule et nettoyage UTF-8
     */
    private function parseCsvFile(string $filePath): array
    {
        $rows = [];
        $handle = fopen($filePath, "r");

        if ($handle !== false) {
            // Lecture ligne par ligne avec la virgule (,) en séparateur
            while (($data = fgetcsv($handle, 4096, ',')) !== false) {
                // Nettoyage des espaces et du BOM UTF-8 sur chaque cellule
                $cleanedRow = array_map(function ($cell) {
                    // Supprime les caractères invisibles / BOM UTF-8
                    $clean = preg_replace('/[\x00-\x1F\x7F\xEF\xBB\xBF]/', '', (string) $cell);
                    return trim($clean);
                }, $data);

                $rows[] = $cleanedRow;
            }
            fclose($handle);
        }

        return $rows;
    }

    /**
     * Lecture d'un fichier Excel (Utilise PhpSpreadsheet si disponible)
     */
    private function parseExcelFile(string $filePath): array
    {
        if (class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            return $sheet->toArray();
        }

        throw new \Exception("La bibliothèque PhpSpreadsheet n'est pas installée pour lire les fichiers Excel.");
    }

    private function envoyerCleActivationParMail(string $email, string $prenom, string $nom, int $numeroLicence, string $cleActivation): void
    {
        // Créer une instance de PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configuration du serveur SMTP (par exemple, avec Gmail)
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->CharSet = 'UTF-8';
            $mail->SMTPAuth = true;
            $mail->Username = 'dvmta39@gmail.com'; // Votre adresse email
            $mail->Password = 'pnnikshkztituxfj';    // Votre mot de passe ou mot de passe d'application
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Destinataire et expéditeur
            $mail->setFrom('dvmta39@gmail.com', 'Cartoguide AVVA39');

            $mail->addAddress($email);

            // Sujet et contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = 'Clé d\'activation pour la connexion du Cartoguide sur AVVA39';

            // **************************************************
            // CORPS DE L'EMAIL MODERNISÉ (HTML stylisé)
            // **************************************************
            $mail->Body = "
                <!DOCTYPE html>
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
                        .container { background-color: #ffffff; margin: 20px auto; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); max-width: 600px; border-top: 5px solid #3282b8; }
                        h1 { color: #3282b8; font-size: 24px; border-bottom: 1px solid #eeeeee; padding-bottom: 10px; }
                        p { color: #555; line-height: 1.6; }
                        .code-box { background-color: #e6f7ff; padding: 15px; border-radius: 5px; margin-top: 15px; margin-bottom: 15px; border: 1px dashed #3282b8; }
                        .code-box p { margin: 0; color: #1a1a2e; font-size: 1.1em; font-weight: bold; }
                        .footer { text-align: center; margin-top: 20px; font-size: 0.8em; color: #aaa; }
                        .licence { color: #ff4757; font-weight: bold; }
                        /* Style pour le slogan */
                        .slogan { 
                            font-size: 1.1em; 
                            font-weight: bold; 
                            color: #1a1a2e; /* Couleur sombre */
                            margin-top: 10px;
                            display: block;
                        }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <h1>🔑 Votre Clé d'Activation Cartoguide AVVA39</h1>
                        
                        <p>
                            Bonjour $prenom $nom,
                        </p>
                        <p>
                            En tant que membre d'AVVA39, vous bénéficiez d'un accès gratuit au Cartoguide. 
                            Voici les informations nécessaires pour vous connecter et commencer à tracer vos parcours :
                        </p>
                        
                        <div class='code-box'>
                            <p>Numéro de licence : <span class='licence'>$numeroLicence</span></p>
                            <p>Clé d'activation : <span class='licence'>$cleActivation</span></p>
                        </div>
                        
                        <p>
                            Utilisez ces informations sur notre plateforme pour valider votre accès.
                        </p>
                        
                        <div class='footer'>
                            <span class='slogan'>ÇAVVA ALLER</span>
                            Ceci est un email automatique. Veuillez ne pas y répondre.
                        </div>
                    </div>
                </body>
                </html>
            ";
            // Envoyer l'email
            $mail->send();

        } catch (\Exception $e) {
            // Si l'email n'a pas pu être envoyé
            error_log("L'email n'a pas pu être envoyé. Erreur : {$mail->ErrorInfo}");
            throw new \Exception("Erreur lors de l'envoi de l'email.");
        }
    }

    public function modifierMembre(int $id): void
    {
        session_start();

        $active3 = true;

        // Vérifier si l'utilisateur est connecté
        if (!$this->isUserLoggedIn() || !$_SESSION['user']['idRole'] == 1) {
            header("Location: /admin/login");
            exit;
        }

        $user = $_SESSION['user'];

        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        $membre = $this->entityManager->getRepository(Membre::class)->find($id);

        if (!$membre) {
            $_SESSION['error_message'] = "Membre introuvable.";
            $this->redirect("/avva-admin/liste-membres");
            exit();
        }

        $modifierMembre = new ModifierMembre($this->entityManager);

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                $numeroLicence = $_POST['numero_licence_membre'];
                $nom = $_POST['nom_membre'];
                $prenom = $_POST['prenom_membre'];
                $dateNaissance = $_POST['date_naissance_membre'];
                $sexe = $_POST['sexe_membre'];
                $numeroVoie = $_POST['numero_voie_membre'];
                $nomVoie = $_POST['nom_voie_membre'];
                $codePostal = $_POST['code_postal_membre'];
                $ville = $_POST['ville_membre'];
                $numeroTelephone = $_POST['numero_telephone_membre'];
                $email = $_POST['email_membre'];

                if (empty($numeroLicence) || empty($nom) || empty($prenom) || empty($dateNaissance) || empty($sexe) || empty($numeroVoie) || empty($nomVoie) || empty($codePostal) || empty($ville) || empty($numeroTelephone) || empty($email)) {
                    throw new \Exception("Tous les champs doivent être remplis.");
                }

                $modifierMembre->execute($membre->getId(), $numeroLicence, $nom, $prenom, $dateNaissance, $sexe, $numeroVoie, $nomVoie, $codePostal, $ville, $numeroTelephone, $email);

                $_SESSION['success_message'] = "Le membre a été modifiée avec succès";
                $this->redirect("/avva-admin/liste-membres");
                exit();
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        $this->render('/admin/modifier-membre', [
            'user' => $user,
            'active3' => $active3,
            'pages' => $pages,
            'error' => $error ?? '',
            'utilisateur' => $_SESSION['utilisateur'] ?? null,
            'membre' => $membre
        ]);
    }

    public function exportMembres(): void
    {
        // 1. Vérification de l'authentification et des droits
        // Utiliser la gestion de session de Symfony (si vous utilisez le SecurityBundle) 
        // ou la méthode de vérification que vous utilisez habituellement.
        session_start();
        if (!isset($_SESSION['isUserConnected']) || !$_SESSION['isUserConnected']) {
            header('Location: /avva-admin/login');
            exit;
        }

        // 2. Récupération de TOUS les Membres
        // On suppose que $this->entityManager est disponible
        $membres = $this->entityManager->getRepository(Membre::class)->findBy([], ['nom' => 'ASC', 'prenom' => 'ASC']);

        if (empty($membres)) {
            $_SESSION['error_message'] = "Aucun membre trouvé à exporter.";
            // Redirection vers une page de gestion des membres
            header('Location: /avva-admin/liste-membres');
            exit;
        }

        // 3. Préparation et Envoi du Fichier CSV

        $filename = 'Liste_Membres_' . date('Ymd_His') . '.csv';

        // En-têtes HTTP pour le téléchargement CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Ouvrir un flux de sortie PHP pour le CSV
        $output = fopen('php://output', 'w');

        // Assurez-vous que l'encodage est UTF-8 pour les accents
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Définition des en-têtes de colonnes (adaptés aux champs de l'entité Membre)
        $headers = [
            'ID',
            'N° Licence',
            'Nom',
            'Prénom',
            'Date Naissance',
            'Sexe',
            'Email',
            'Téléphone',
            'N° Voie',
            'Nom Voie',
            'Code Postal',
            'Ville',
        ];

        // Écrire l'en-tête
        fputcsv($output, $headers, ';'); // Utiliser le point-virgule comme séparateur

        // Écriture des données
        foreach ($membres as $membre) {
            $data = [
                $membre->getId(),
                $membre->getNumeroLicence(),
                $membre->getNom(),
                $membre->getPrenom(),
                // Assurez-vous que getDateNaissance() retourne un objet DateTimeInterface
                $membre->getDateNaissance() ? $membre->getDateNaissance()->format('d/m/Y') : '',
                $membre->getSexe(),
                $membre->getEmail(),
                $membre->getNumeroTelephone(),
                $membre->getNumeroVoie(),
                $membre->getNomVoie(),
                $membre->getCodePostal(),
                $membre->getVille(),
            ];

            fputcsv($output, $data, ';');
        }

        // Fermer le flux
        fclose($output);

        // Finir l'exécution PHP
        exit;
    }

    public function supprimerSection(int $id): void
    {
        session_start();

        // Vérifier si l'utilisateur est connecté
        if (!$this->isUserLoggedIn()) {
            header("Location: /admin/login");
            exit;
        }

        $user = $_SESSION['user']; // Utilisateur stocké dans la session
        // Vérifier si l'utilisateur est un administrateur
        if ($user->getAdmin() != 1 && $user->getDroitsPagesContenu() != 1) {
            header("Location: /admin/accueil"); // Rediriger si ce n'est pas un administrateur
            exit;
        }

        // Récupérer l'ID du challenge que vous souhaitez modifier
        $sectionId = $this->entityManager->getRepository(Section::class)->find($id)->getId();

        if (!empty($this->entityManager->getRepository(ContenuSection::class)->findBy(['section' => $id]))) {
            $_SESSION['error_message'] = "Vous ne pouvez pas supprimez la section car il y a du contenu qui appartient à la section";
            $this->redirect("/admin/pages/liste-section");
        }

        // Créer une instance de la UserStory ModifierPageAccueilChallenge
        $supprimerSection = new SupprimerSection($this->entityManager);

        // Vérifier si le formulaire a été soumis
        try {
            // Appeler la méthode pour mettre à jour les données
            $supprimerSection->execute(
                $sectionId
            );

            $_SESSION['success_message'] = "La section a été supprimer avec succès";
            $this->redirect("/admin/pages/liste-section");
            exit();
        } catch (\Exception $e) {
            // Gérer les erreurs et afficher un message à l'utilisateur
            $error = $e->getMessage();
        }
    }

    private function isUserLoggedIn(): bool
    {
        session_start();
        return isset($_SESSION['user']);
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