<?php

namespace App\Controller;

use App\Entity\InscriptionGravelRandonnee;
use App\Entity\InscriptionPedestreRandonnee;
use App\Entity\InscriptionRouteRandonnee;
use App\Entity\InscriptionVTTRandonnee;
use App\Entity\Page;
use App\Entity\Randonnee;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

class AccueilController extends AbstractController
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Afficher la page d'accueil de l'administrateur
    public function index(): void
    {
        session_start();  // Démarrer la session avant toute manipulation

        $active1 = true;

        $nombreVisite = $this->getUniqueVisitor();
        $nombreVisiteParMois = $this->getUniqueVisitorMonthly();

        $pages = $this->entityManager->getRepository(Page::class)->findAll();
        $nombrePages = $this->entityManager->getRepository(Page::class)->count();

        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user']) || !isset($_SESSION['password']) || !$_SESSION['user']['active'] == true) {
            $_SESSION['error_message'] = "Vous devez être connecté et actif pour accéder à cette page.";
            $this->redirect("/avva-admin/login");  // Redirige vers la page de connexion
        }

        $randonneeDerniereAjoutee = $this->entityManager->getRepository(Randonnee::class)->findOneBy(
            [
                'statutPublication' => 'Publié',
            ],
            ['dateCreation' => 'DESC']
        );

        $randonneeCible = null;
        $statutsPaiementConfirme = ['PAYÉ', 'CONFIRMÉ']; // Correction des statuts pour la robustesse

        if ($randonneeDerniereAjoutee) {

            // Obtenir la date de création de la dernière randonnée ajoutée
            $dateCreationRef = $randonneeDerniereAjoutee->getDateCreation();

            // 3.2. Priorité 1 : Chercher la prochaine randonnée à venir (dateRandonnee ASC)
            // SANS RANDONNÉES PLUS ANCIENNES QUE LA DERNIÈRE CRÉÉE.
            $qbProchaine = $this->entityManager->getRepository(Randonnee::class)->createQueryBuilder('r');
            $randonneeProchaine = $qbProchaine
                ->where('r.estAnnulee = :annulee')
                ->andWhere('r.statutPublication = :publie')
                ->andWhere('r.dateCreation >= :dateRef')
                ->orderBy('r.dateRandonnee', 'ASC')
                ->setMaxResults(1)
                ->setParameter('annulee', false)
                ->setParameter('publie', 'Publié')
                ->setParameter('dateRef', $dateCreationRef)
                ->getQuery()
                ->getOneOrNullResult();

            // Logique de Sélection Stricte:
            if ($randonneeProchaine) {
                // Cas 1 : La randonnée cible est la prochaine à venir (respectant la restriction de date de création)
                $randonneeCible = $randonneeProchaine;
            } else {
                // Cas 2 : Aucune prochaine randonnée respectant la restriction trouvée.
                // On utilise la dernière ajoutée (qui sert de référence).
                $randonneeCible = $randonneeDerniereAjoutee;
            }

        }
        // Si $randonneeDerniereAjoutee est null, $randonneeCible reste null.


        // Initialisation des variables de comptage
        $nombreTotalInscrits = 0;
        $nombreInscritsVTT = 0;
        $nombreInscritsGravel = 0;
        $nombreInscritsRoute = 0;
        $nombreInscritsPedestre = 0;
        $nombreMaxParticipants = null;

        // --- 3.3. Calcul des inscriptions (UNIQUEMENT si une randonnée cible a été sélectionnée) ---

        if ($randonneeCible) {

            $circuitIds = $randonneeCible->getCircuits()->map(fn($c) => $c->getId())->toArray();

            if (!empty($circuitIds)) {

                $countConfirmedInscriptions = function (string $entityClass, array $ids, array $statuts) {
                    /** @var QueryBuilder $qb */
                    $qb = $this->entityManager->getRepository($entityClass)->createQueryBuilder('i');
                    return $qb->select('count(i.id)')
                        ->where($qb->expr()->in('i.circuitRandonnee', ':ids'))
                        ->andWhere($qb->expr()->in('i.statutPaiement', ':statuts'))
                        ->setParameter('ids', $ids)
                        ->setParameter('statuts', $statuts)
                        ->getQuery()
                        ->getSingleScalarResult();
                };

                // Calcul des inscrits pour toutes les catégories
                $nombreInscritsVTT = $countConfirmedInscriptions(InscriptionVTTRandonnee::class, $circuitIds, $statutsPaiementConfirme);
                $nombreInscritsGravel = $countConfirmedInscriptions(InscriptionGravelRandonnee::class, $circuitIds, $statutsPaiementConfirme);
                $nombreInscritsRoute = $countConfirmedInscriptions(InscriptionRouteRandonnee::class, $circuitIds, $statutsPaiementConfirme);
                $nombreInscritsPedestre = $countConfirmedInscriptions(InscriptionPedestreRandonnee::class, $circuitIds, $statutsPaiementConfirme);

                $nombreTotalInscrits = $nombreInscritsVTT + $nombreInscritsGravel + $nombreInscritsRoute + $nombreInscritsPedestre;
            }

            $nombreMaxParticipants = $randonneeCible->getNombreParticipantsMax();
        }

        // Vérifiez si l'utilisateur est connecté et actif
        $_SESSION['isUserConnected'] = isset($_SESSION['user']) && isset($_SESSION['password']) && $_SESSION['user']['active'] === true;

        // Vérifier si l'utilisateur est un administrateur
        $_SESSION['isUserAdmin'] = $_SESSION['isUserConnected'] && $_SESSION['user']['admin'] === true;

        // Si l'utilisateur est connecté, afficher la page d'accueil
        $this->render('admin/accueil', [
            'active1' => $active1,
            'nombreVisite' => $nombreVisite,
            'nombreVisiteParMois' => $nombreVisiteParMois,
            'pages' => $pages,
            'nombrePages' => $nombrePages,
            'successMessage' => $_SESSION['success_message'] ?? null,
            'user' => $_SESSION['user'],  // Passer l'utilisateur connecté à la vue
            'errorMessage' => $_SESSION['error_message'] ?? null,
            'isUserConnected' => $_SESSION['isUserConnected'],
            'isUserAdmin' => $_SESSION['isUserAdmin'],
            'prochaineRandonnee' => $randonneeCible,
            'nombreTotalInscrits' => $nombreTotalInscrits,
            'nombreInscritsVTT' => $nombreInscritsVTT,
            'nombreInscritsGravel' => $nombreInscritsGravel,
            'nombreInscritsRoute' => $nombreInscritsRoute,
            'nombreInscritsPedestre' => $nombreInscritsPedestre,
            'nombreMaxParticipants' => $nombreMaxParticipants,
        ]);
    }

    public function listeInscritsRandonneesVTT(int $id): void
    {
        session_start();

        $active1 = true;

        // 1. Vérification de l'authentification (Sécurité)
        if (!isset($_SESSION['isUserConnected']) || !$_SESSION['isUserConnected']) {
            // Redirection vers la page de connexion
            // Assurez-vous que votre route de login est correcte
            header('Location: /avva-admin/login');
            exit;
        }

        // 2. Récupération de la Randonnée Cible
        $randonneeCible = $this->entityManager->getRepository(Randonnee::class)->find($id);

        if (!$randonneeCible) {
            // Gérer le cas où la randonnée n'existe pas
            $_SESSION['error_message'] = "La randonnée demandée n'a pas été trouvée.";
            // Redirection vers la liste des randonnées (ou le tableau de bord)
            header('Location: /avva-admin/randonnee');
            exit;
        }

        // 3. Récupération des Inscriptions Pédestres FILTRÉES

        // a) Collecter les IDs des circuits liés à cette randonnée
        $circuitIds = [];
        foreach ($randonneeCible->getCircuits() as $circuit) {
            // Utiliser la méthode getter appropriée pour l'ID du circuit
            $circuitIds[] = $circuit->getId();
        }

        $inscriptionsVTT = [];

        if (!empty($circuitIds)) {
            // b) Utiliser le QueryBuilder de Doctrine pour filtrer les inscriptions
            $qb = $this->entityManager->getRepository(InscriptionVTTRandonnee::class)->createQueryBuilder('i');

            $query = $qb
                ->where($qb->expr()->in('i.circuitRandonnee', ':circuitIds'))
                ->andWhere('i.statutPaiement = :statutPaiement')
                ->orderBy('i.nom', 'ASC')
                ->setParameter('circuitIds', $circuitIds)
                ->setParameter('statutPaiement', 'PAYÉ')
                ->getQuery();

            $inscriptionsVTT = $query->getResult();
        }

        // 4. Autres données pour le template
        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        // 5. Rendu de la vue (Votre fonction maison)
        $this->render('/admin/pages/liste-inscrits-randonnees-vtt', [
            'user' => $_SESSION['user'],
            'active1' => $active1,
            'pages' => $pages,
            'randonneeCible' => $randonneeCible, // Pour le titre de la page
            'inscriptionsVTT' => $inscriptionsVTT // Les données pour le tableau
        ]);

        // Fin de l'exécution après le rendu
        exit;
    }

    public function exportInscritsRandonneesVTT(int $id): void
    {
        session_start();

        // 1. Vérification de l'authentification et des droits
        if (!isset($_SESSION['isUserConnected']) || !$_SESSION['isUserConnected']) {
            header('Location: /avva-admin/login');
            exit;
        }

        // 2. Récupération de la Randonnée Cible
        $randonneeCible = $this->entityManager->getRepository(Randonnee::class)->find($id);

        if (!$randonneeCible) {
            $_SESSION['error_message'] = "Erreur d'export: La randonnée n'a pas été trouvée.";
            header('Location: /avva-admin/accueil');
            exit;
        }

        // 3. Récupération des Inscriptions Pédestres FILTRÉES (Logique réutilisée de la liste)

        $circuitIds = [];
        foreach ($randonneeCible->getCircuits() as $circuit) {
            $circuitIds[] = $circuit->getId();
        }

        $inscriptionsVTT = [];

        if (!empty($circuitIds)) {
            $qb = $this->entityManager->getRepository(InscriptionVTTRandonnee::class)->createQueryBuilder('i');

            $query = $qb
                ->where($qb->expr()->in('i.circuitRandonnee', ':circuitIds'))
                ->orderBy('i.nom', 'ASC')
                ->setParameter('circuitIds', $circuitIds)
                ->getQuery();

            $inscriptionsVTT = $query->getResult();
        }

        // 4. Préparation et Envoi du Fichier CSV

        $filename = 'Inscriptions_VTT_' . $randonneeCible->getSlug() . '_' . date('Ymd_His') . '.csv';

        // En-têtes HTTP pour le téléchargement CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Ouvrir un flux de sortie PHP pour le CSV
        $output = fopen('php://output', 'w');

        // Assurez-vous que l'encodage est UTF-8 pour les accents
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Définition des en-têtes de colonnes
        $headers = [
            'ID',
            'Nom',
            'Prénom',
            'Email',
            'Téléphone',
            'Date Naissance',
            'Sexe',
            'Circuit',
            'Statut Paiement',
            'N° Inscription Groupe',
            'Licence FFVelo Club',
            'N° Licence',
            'Autre Féd Club',
            'Adresse',
            'Code Postal',
            'Ville',
            'Contact Urgence (Nom/Prénom/Tel)',
        ];

        // Écrire l'en-tête
        fputcsv($output, $headers, ';'); // Utiliser le point-virgule comme séparateur pour Excel

        // Écriture des données
        foreach ($inscriptionsVTT as $inscription) {

            $data = [
                $inscription->getId(),
                $inscription->getNom(),
                $inscription->getPrenom(),
                $inscription->getEmail(),
                $inscription->getNumTel(),
                $inscription->getDateNaissance()->format('d/m/Y'),
                $inscription->getSexe(),
                // Assurez-vous que getCircuitRandonnee() et getNomCircuit() existent et retournent une chaîne
                $inscription->getCircuitRandonnee()->getNom(),
                $inscription->getStatutPaiement(),
                $inscription->getNumeroInscription(),
                $inscription->getLicenceFfveloClub(),
                $inscription->getNumLicence(),
                $inscription->getAutreFederationClub(),
                $inscription->getAdresse(),
                $inscription->getCodePostal(),
                $inscription->getVille(),
                $inscription->getNomPrenomTel(),
            ];

            fputcsv($output, $data, ';');
        }

        // Fermer le flux
        fclose($output);

        // Finir l'exécution PHP
        exit;
    }

    public function listeInscritsRandonneesGravel(int $id): void
    {
        session_start();

        $active1 = true;

        // 1. Vérification de l'authentification (Sécurité)
        if (!isset($_SESSION['isUserConnected']) || !$_SESSION['isUserConnected']) {
            // Redirection vers la page de connexion
            // Assurez-vous que votre route de login est correcte
            header('Location: /avva-admin/login');
            exit;
        }

        // 2. Récupération de la Randonnée Cible
        $randonneeCible = $this->entityManager->getRepository(Randonnee::class)->find($id);

        if (!$randonneeCible) {
            // Gérer le cas où la randonnée n'existe pas
            $_SESSION['error_message'] = "La randonnée demandée n'a pas été trouvée.";
            // Redirection vers la liste des randonnées (ou le tableau de bord)
            header('Location: /avva-admin/randonnee');
            exit;
        }

        // 3. Récupération des Inscriptions Pédestres FILTRÉES

        // a) Collecter les IDs des circuits liés à cette randonnée
        $circuitIds = [];
        foreach ($randonneeCible->getCircuits() as $circuit) {
            // Utiliser la méthode getter appropriée pour l'ID du circuit
            $circuitIds[] = $circuit->getId();
        }

        $inscriptionsGravel = [];

        if (!empty($circuitIds)) {
            // b) Utiliser le QueryBuilder de Doctrine pour filtrer les inscriptions
            $qb = $this->entityManager->getRepository(InscriptionGravelRandonnee::class)->createQueryBuilder('i');

            $query = $qb
                ->where($qb->expr()->in('i.circuitRandonnee', ':circuitIds'))
                ->andWhere('i.statutPaiement = :statutPaiement')
                ->orderBy('i.nom', 'ASC')
                ->setParameter('circuitIds', $circuitIds)
                ->setParameter('statutPaiement', 'PAYÉ')
                ->getQuery();

            $inscriptionsGravel = $query->getResult();
        }

        // 4. Autres données pour le template
        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        // 5. Rendu de la vue (Votre fonction maison)
        $this->render('/admin/pages/liste-inscrits-randonnees-gravel', [
            'user' => $_SESSION['user'],
            'active1' => $active1,
            'pages' => $pages,
            'randonneeCible' => $randonneeCible, // Pour le titre de la page
            'inscriptionsGravel' => $inscriptionsGravel // Les données pour le tableau
        ]);

        // Fin de l'exécution après le rendu
        exit;
    }

    public function exportInscritsRandonneesGravel(int $id): void
    {
        session_start();

        // 1. Vérification de l'authentification et des droits
        if (!isset($_SESSION['isUserConnected']) || !$_SESSION['isUserConnected']) {
            header('Location: /avva-admin/login');
            exit;
        }

        // 2. Récupération de la Randonnée Cible
        $randonneeCible = $this->entityManager->getRepository(Randonnee::class)->find($id);

        if (!$randonneeCible) {
            $_SESSION['error_message'] = "Erreur d'export: La randonnée n'a pas été trouvée.";
            header('Location: /avva-admin/accueil');
            exit;
        }

        // 3. Récupération des Inscriptions Pédestres FILTRÉES (Logique réutilisée de la liste)

        $circuitIds = [];
        foreach ($randonneeCible->getCircuits() as $circuit) {
            $circuitIds[] = $circuit->getId();
        }

        $inscriptionsGravel = [];

        if (!empty($circuitIds)) {
            $qb = $this->entityManager->getRepository(InscriptionGravelRandonnee::class)->createQueryBuilder('i');

            $query = $qb
                ->where($qb->expr()->in('i.circuitRandonnee', ':circuitIds'))
                ->orderBy('i.nom', 'ASC')
                ->setParameter('circuitIds', $circuitIds)
                ->getQuery();

            $inscriptionsGravel = $query->getResult();
        }

        // 4. Préparation et Envoi du Fichier CSV

        $filename = 'Inscriptions_Gravel_' . $randonneeCible->getSlug() . '_' . date('Ymd_His') . '.csv';

        // En-têtes HTTP pour le téléchargement CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Ouvrir un flux de sortie PHP pour le CSV
        $output = fopen('php://output', 'w');

        // Assurez-vous que l'encodage est UTF-8 pour les accents
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Définition des en-têtes de colonnes
        $headers = [
            'ID',
            'Nom',
            'Prénom',
            'Email',
            'Téléphone',
            'Date Naissance',
            'Sexe',
            'Circuit',
            'Statut Paiement',
            'N° Inscription Groupe',
            'Licence FFVelo Club',
            'N° Licence',
            'Autre Féd Club',
            'Adresse',
            'Code Postal',
            'Ville',
            'Contact Urgence (Nom/Prénom/Tel)',
        ];

        // Écrire l'en-tête
        fputcsv($output, $headers, ';'); // Utiliser le point-virgule comme séparateur pour Excel

        // Écriture des données
        foreach ($inscriptionsGravel as $inscription) {

            $data = [
                $inscription->getId(),
                $inscription->getNom(),
                $inscription->getPrenom(),
                $inscription->getEmail(),
                $inscription->getNumTel(),
                $inscription->getDateNaissance()->format('d/m/Y'),
                $inscription->getSexe(),
                // Assurez-vous que getCircuitRandonnee() et getNomCircuit() existent et retournent une chaîne
                $inscription->getCircuitRandonnee()->getNom(),
                $inscription->getStatutPaiement(),
                $inscription->getNumeroInscription(),
                $inscription->getLicenceFfveloClub(),
                $inscription->getNumLicence(),
                $inscription->getAutreFederationClub(),
                $inscription->getAdresse(),
                $inscription->getCodePostal(),
                $inscription->getVille(),
                $inscription->getNomPrenomTel(),
            ];

            fputcsv($output, $data, ';');
        }

        // Fermer le flux
        fclose($output);

        // Finir l'exécution PHP
        exit;
    }

    public function listeInscritsRandonneesRoute(int $id): void
    {
        session_start();

        $active1 = true;

        // 1. Vérification de l'authentification (Sécurité)
        if (!isset($_SESSION['isUserConnected']) || !$_SESSION['isUserConnected']) {
            // Redirection vers la page de connexion
            // Assurez-vous que votre route de login est correcte
            header('Location: /avva-admin/login');
            exit;
        }

        // 2. Récupération de la Randonnée Cible
        $randonneeCible = $this->entityManager->getRepository(Randonnee::class)->find($id);

        if (!$randonneeCible) {
            // Gérer le cas où la randonnée n'existe pas
            $_SESSION['error_message'] = "La randonnée demandée n'a pas été trouvée.";
            // Redirection vers la liste des randonnées (ou le tableau de bord)
            header('Location: /avva-admin/randonnee');
            exit;
        }

        // 3. Récupération des Inscriptions Pédestres FILTRÉES

        // a) Collecter les IDs des circuits liés à cette randonnée
        $circuitIds = [];
        foreach ($randonneeCible->getCircuits() as $circuit) {
            // Utiliser la méthode getter appropriée pour l'ID du circuit
            $circuitIds[] = $circuit->getId();
        }

        $inscriptionsRoute = [];

        if (!empty($circuitIds)) {
            // b) Utiliser le QueryBuilder de Doctrine pour filtrer les inscriptions
            $qb = $this->entityManager->getRepository(InscriptionRouteRandonnee::class)->createQueryBuilder('i');

            $query = $qb
                ->where($qb->expr()->in('i.circuitRandonnee', ':circuitIds'))
                ->andWhere('i.statutPaiement = :statutPaiement')
                ->orderBy('i.nom', 'ASC')
                ->setParameter('circuitIds', $circuitIds)
                ->setParameter('statutPaiement', 'PAYÉ')
                ->getQuery();

            $inscriptionsRoute = $query->getResult();
        }

        // 4. Autres données pour le template
        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        // 5. Rendu de la vue (Votre fonction maison)
        $this->render('/admin/pages/liste-inscrits-randonnees-route', [
            'user' => $_SESSION['user'],
            'active1' => $active1,
            'pages' => $pages,
            'randonneeCible' => $randonneeCible, // Pour le titre de la page
            'inscriptionsRoute' => $inscriptionsRoute // Les données pour le tableau
        ]);

        // Fin de l'exécution après le rendu
        exit;
    }

    public function exportInscritsRandonneesRoute(int $id): void
    {
        session_start();

        // 1. Vérification de l'authentification et des droits
        if (!isset($_SESSION['isUserConnected']) || !$_SESSION['isUserConnected']) {
            header('Location: /avva-admin/login');
            exit;
        }

        // 2. Récupération de la Randonnée Cible
        $randonneeCible = $this->entityManager->getRepository(Randonnee::class)->find($id);

        if (!$randonneeCible) {
            $_SESSION['error_message'] = "Erreur d'export: La randonnée n'a pas été trouvée.";
            header('Location: /avva-admin/accueil');
            exit;
        }

        // 3. Récupération des Inscriptions Pédestres FILTRÉES (Logique réutilisée de la liste)

        $circuitIds = [];
        foreach ($randonneeCible->getCircuits() as $circuit) {
            $circuitIds[] = $circuit->getId();
        }

        $inscriptionsRoute = [];

        if (!empty($circuitIds)) {
            $qb = $this->entityManager->getRepository(InscriptionRouteRandonnee::class)->createQueryBuilder('i');

            $query = $qb
                ->where($qb->expr()->in('i.circuitRandonnee', ':circuitIds'))
                ->orderBy('i.nom', 'ASC')
                ->setParameter('circuitIds', $circuitIds)
                ->getQuery();

            $inscriptionsRoute = $query->getResult();
        }

        // 4. Préparation et Envoi du Fichier CSV

        $filename = 'Inscriptions_Route_' . $randonneeCible->getSlug() . '_' . date('Ymd_His') . '.csv';

        // En-têtes HTTP pour le téléchargement CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Ouvrir un flux de sortie PHP pour le CSV
        $output = fopen('php://output', 'w');

        // Assurez-vous que l'encodage est UTF-8 pour les accents
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Définition des en-têtes de colonnes
        $headers = [
            'ID',
            'Nom',
            'Prénom',
            'Email',
            'Téléphone',
            'Date Naissance',
            'Sexe',
            'Circuit',
            'Statut Paiement',
            'N° Inscription Groupe',
            'Licence FFVelo Club',
            'N° Licence',
            'Autre Féd Club',
            'Adresse',
            'Code Postal',
            'Ville',
            'Contact Urgence (Nom/Prénom/Tel)',
        ];

        // Écrire l'en-tête
        fputcsv($output, $headers, ';'); // Utiliser le point-virgule comme séparateur pour Excel

        // Écriture des données
        foreach ($inscriptionsRoute as $inscription) {

            $data = [
                $inscription->getId(),
                $inscription->getNom(),
                $inscription->getPrenom(),
                $inscription->getEmail(),
                $inscription->getNumTel(),
                $inscription->getDateNaissance()->format('d/m/Y'),
                $inscription->getSexe(),
                // Assurez-vous que getCircuitRandonnee() et getNomCircuit() existent et retournent une chaîne
                $inscription->getCircuitRandonnee()->getNom(),
                $inscription->getStatutPaiement(),
                $inscription->getNumeroInscription(),
                $inscription->getLicenceFfveloClub(),
                $inscription->getNumLicence(),
                $inscription->getAutreFederationClub(),
                $inscription->getAdresse(),
                $inscription->getCodePostal(),
                $inscription->getVille(),
                $inscription->getNomPrenomTel(),
            ];

            fputcsv($output, $data, ';');
        }

        // Fermer le flux
        fclose($output);

        // Finir l'exécution PHP
        exit;
    }

    public function listeInscritsRandonneesPedestre(int $id): void
    {
        session_start();

        $active1 = true;

        // 1. Vérification de l'authentification (Sécurité)
        if (!isset($_SESSION['isUserConnected']) || !$_SESSION['isUserConnected']) {
            // Redirection vers la page de connexion
            // Assurez-vous que votre route de login est correcte
            header('Location: /avva-admin/login');
            exit;
        }

        // 2. Récupération de la Randonnée Cible
        $randonneeCible = $this->entityManager->getRepository(Randonnee::class)->find($id);

        if (!$randonneeCible) {
            // Gérer le cas où la randonnée n'existe pas
            $_SESSION['error_message'] = "La randonnée demandée n'a pas été trouvée.";
            // Redirection vers la liste des randonnées (ou le tableau de bord)
            header('Location: /avva-admin/randonnee');
            exit;
        }

        // 3. Récupération des Inscriptions Pédestres FILTRÉES

        // a) Collecter les IDs des circuits liés à cette randonnée
        $circuitIds = [];
        foreach ($randonneeCible->getCircuits() as $circuit) {
            // Utiliser la méthode getter appropriée pour l'ID du circuit
            $circuitIds[] = $circuit->getId();
        }

        $inscriptionsPedestre = [];

        if (!empty($circuitIds)) {
            // b) Utiliser le QueryBuilder de Doctrine pour filtrer les inscriptions
            $qb = $this->entityManager->getRepository(InscriptionPedestreRandonnee::class)->createQueryBuilder('i');

            $query = $qb
                ->where($qb->expr()->in('i.circuitRandonnee', ':circuitIds'))
                ->andWhere('i.statutPaiement = :statutPaiement')
                ->orderBy('i.nom', 'ASC')
                ->setParameter('circuitIds', $circuitIds)
                ->setParameter('statutPaiement', 'PAYÉ')
                ->getQuery();

            $inscriptionsPedestre = $query->getResult();
        }

        // 4. Autres données pour le template
        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        // 5. Rendu de la vue (Votre fonction maison)
        $this->render('/admin/pages/liste-inscrits-randonnees-pedestre', [
            'user' => $_SESSION['user'],
            'active1' => $active1,
            'pages' => $pages,
            'randonneeCible' => $randonneeCible, // Pour le titre de la page
            'inscriptionsPedestre' => $inscriptionsPedestre // Les données pour le tableau
        ]);

        // Fin de l'exécution après le rendu
        exit;
    }

    public function exportInscritsRandonneesPedestre(int $id): void
    {
        session_start();

        // 1. Vérification de l'authentification et des droits
        if (!isset($_SESSION['isUserConnected']) || !$_SESSION['isUserConnected']) {
            header('Location: /avva-admin/login');
            exit;
        }

        // 2. Récupération de la Randonnée Cible
        $randonneeCible = $this->entityManager->getRepository(Randonnee::class)->find($id);

        if (!$randonneeCible) {
            $_SESSION['error_message'] = "Erreur d'export: La randonnée n'a pas été trouvée.";
            header('Location: /avva-admin/accueil');
            exit;
        }

        // 3. Récupération des Inscriptions Pédestres FILTRÉES (Logique réutilisée de la liste)

        $circuitIds = [];
        foreach ($randonneeCible->getCircuits() as $circuit) {
            $circuitIds[] = $circuit->getId();
        }

        $inscriptionsPedestre = [];

        if (!empty($circuitIds)) {
            $qb = $this->entityManager->getRepository(InscriptionPedestreRandonnee::class)->createQueryBuilder('i');

            $query = $qb
                ->where($qb->expr()->in('i.circuitRandonnee', ':circuitIds'))
                ->orderBy('i.nom', 'ASC')
                ->setParameter('circuitIds', $circuitIds)
                ->getQuery();

            $inscriptionsPedestre = $query->getResult();
        }

        // 4. Préparation et Envoi du Fichier CSV

        $filename = 'Inscriptions_Pedestre_' . $randonneeCible->getSlug() . '_' . date('Ymd_His') . '.csv';

        // En-têtes HTTP pour le téléchargement CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Ouvrir un flux de sortie PHP pour le CSV
        $output = fopen('php://output', 'w');

        // Assurez-vous que l'encodage est UTF-8 pour les accents
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Définition des en-têtes de colonnes
        $headers = [
            'ID',
            'Nom',
            'Prénom',
            'Email',
            'Téléphone',
            'Date Naissance',
            'Sexe',
            'Circuit',
            'Statut Paiement',
            'N° Inscription Groupe',
            'Licence FFVelo Club',
            'N° Licence',
            'Autre Féd Club',
            'Adresse',
            'Code Postal',
            'Ville',
            'Contact Urgence (Nom/Prénom/Tel)',
        ];

        // Écrire l'en-tête
        fputcsv($output, $headers, ';'); // Utiliser le point-virgule comme séparateur pour Excel

        // Écriture des données
        foreach ($inscriptionsPedestre as $inscription) {

            $data = [
                $inscription->getId(),
                $inscription->getNom(),
                $inscription->getPrenom(),
                $inscription->getEmail(),
                $inscription->getNumTel(),
                $inscription->getDateNaissance()->format('d/m/Y'),
                $inscription->getSexe(),
                // Assurez-vous que getCircuitRandonnee() et getNomCircuit() existent et retournent une chaîne
                $inscription->getCircuitRandonnee()->getNom(),
                $inscription->getStatutPaiement(),
                $inscription->getNumeroInscription(),
                $inscription->getLicenceFfveloClub(),
                $inscription->getNumLicence(),
                $inscription->getAutreFederationClub(),
                $inscription->getAdresse(),
                $inscription->getCodePostal(),
                $inscription->getVille(),
                $inscription->getNomPrenomTel(),
            ];

            fputcsv($output, $data, ';');
        }

        // Fermer le flux
        fclose($output);

        // Finir l'exécution PHP
        exit;
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