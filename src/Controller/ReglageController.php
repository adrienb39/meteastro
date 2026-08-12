<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Reglage;
use Doctrine\ORM\EntityManager;

class ReglageController extends AbstractController
{
    private EntityManager $entityManager;
    // Définition du répertoire de destination pour le logo. 
    // Assurez-vous que le chemin est correct par rapport à votre structure d'application.
    private const UPLOAD_DIR = '/uploads/logo/';
    private const UPLOAD_DIR_2 = '/uploads/image-fond/';

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        // Créer le répertoire de téléversement s'il n'existe pas
        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_DIR)) {
            // Tente de créer le répertoire de manière récursive avec des permissions 0777
            if (!mkdir($_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_DIR, 0777, true) && !is_dir($_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_DIR)) {
                // En cas d'échec de la création, vous devriez logger cette erreur
                error_log("Failed to create upload directory: " . $_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_DIR);
            }
        }

        // Créer le répertoire de téléversement s'il n'existe pas
        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_DIR_2)) {
            // Tente de créer le répertoire de manière récursive avec des permissions 0777
            if (!mkdir($_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_DIR_2, 0777, true) && !is_dir($_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_DIR_2)) {
                // En cas d'échec de la création, vous devriez logger cette erreur
                error_log("Failed to create upload directory: " . $_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_DIR_2);
            }
        }
    }

    /**
     * Charge l'unique enregistrement des paramètres du site (ID=1).
     * Si l'enregistrement n'existe pas, crée une nouvelle entité avec des valeurs par défaut.
     * @return Reglage
     */
    private function loadSettingsEntity(): Reglage
    {
        // Tente de récupérer l'entité unique par son ID fixe (1)
        $settings = $this->entityManager->getRepository(Reglage::class)->find(1);

        if (!$settings) {
            // Crée l'entité par défaut si elle n'existe pas (première exécution)
            $settings = new Reglage();

            // --- VALEURS PAR DÉFAUT MISES À JOUR ---
            $settings->setSiteName('Mon Site AVVA');
            $settings->setPresidentNom('Bruyère Maxime');
            $settings->setPresidentAdresseRue('22, rue du Clou');
            $settings->setPresidentAdresseCpVille('39330 Pagnoz');
            $settings->setContactEmail('avva39@outlook.fr');
            $settings->setContactPhone('0611417939');
            // Partenaire 1 (Jura Cycles)
            $settings->setPartenaire1Nom('Jura Cycles');
            $settings->setPartenaire1Url('https://jura-cycles.fr');
            $settings->setPartenaire1AdresseRue('32 Avenue Aristide Briand');
            $settings->setPartenaire1AdresseCpVille('39110 Salins Les Bains');
            $settings->setPartenaire1Tel('0384730577');
            // Réseaux Sociaux
            $settings->setSocialFacebookUrl('https://facebook.com/mon-club-avva');
            $settings->setSocialYoutubeUrl('https://youtube.com/mon-club-avva');
            // Fédérations
            $settings->setFfveloUrl('https://ffvelo.fr');
            $settings->setCodep39Url('https://codep39jura.fr');
            // Couleurs
            $settings->setThemeTextColor('#007bff');
            $settings->setThemeFondColor('#6c757d');
            // Fond Pages
            $settings->setPageFondColor('#ffffff');
            $settings->setPageFondTransparent(1);
            // Nouveau champ logo
            $settings->setLogoFilename(null);
            // Image fond
            $settings->setImageFondFilename(null);
        }

        return $settings;
    }

    /**
     * Affiche le formulaire de gestion des paramètres du site.
     * URL: /avva-admin/settings
     */
    public function settings(): void
    {
        session_start();

        // Vérification de l'authentification et du rôle Administrateur (rôle 1)
        if (!$this->isUserLoggedIn() || ($_SESSION['user']['idRole'] ?? 0) != 1) {
            header("Location: /avva-admin/login");
            exit;
        }

        $active14 = true;
        $pages = $this->entityManager->getRepository(className: Page::class)->findAll();
        $settingsEntity = $this->loadSettingsEntity();

        // --- CONVERSION EN TABLEAU AVEC TOUS LES CHAMPS (incluant le logo) ---
        $settingsArray = [
            'site_name' => $settingsEntity->getSiteName(),
            'logo_filename' => $settingsEntity->getLogoFilename(),
            'image_fond_filename' => $settingsEntity->getImageFondFilename(),
            
            // Coordonnées du Président
            'president_nom' => $settingsEntity->getPresidentNom(),
            'president_adresse_rue' => $settingsEntity->getPresidentAdresseRue(),
            'president_adresse_cp_ville' => $settingsEntity->getPresidentAdresseCpVille(),
            'contact_email' => $settingsEntity->getContactEmail(),
            'contact_phone' => $settingsEntity->getContactPhone(),

            // Partenaire 1
            'partenaire_1_nom' => $settingsEntity->getPartenaire1Nom(),
            'partenaire_1_url' => $settingsEntity->getPartenaire1Url(),
            'partenaire_1_adresse_rue' => $settingsEntity->getPartenaire1AdresseRue(),
            'partenaire_1_adresse_cp_ville' => $settingsEntity->getPartenaire1AdresseCpVille(),
            'partenaire_1_tel' => $settingsEntity->getPartenaire1Tel(),

            // Réseaux Sociaux
            'social_facebook_url' => $settingsEntity->getSocialFacebookUrl(),
            'social_youtube_url' => $settingsEntity->getSocialYoutubeUrl(),

            // Fédérations
            'ffvelo_url' => $settingsEntity->getFfveloUrl(),
            'codep39_url' => $settingsEntity->getCodep39Url(),

            // Couleurs
            'theme_text_color' => $settingsEntity->getThemeTextColor(),
            'theme_fond_color' => $settingsEntity->getThemeFondColor(),

            // Fond Pages
            'page_fond_color' => $settingsEntity->getPageFondColor(),
            'page_fond_transparent' => $settingsEntity->getPageFondTransparent()
        ];

        $this->render('admin/settings', [
            'user' => $_SESSION['user'],
            'active14' => $active14,
            'pages' => $pages,
            'settings' => $settingsArray
        ]);
    }

    /**
     * Gère la logique de téléversement du fichier logo.
     * @param array $fileData Tableau issu de $_FILES['logo_file']
     * @return string|null Nom du nouveau fichier si succès, ou message d'erreur (string) si échec, ou null si aucun fichier n'est fourni.
     */
    private function handleLogoUpload(array $fileData): string|null
    {
        // Si le fichier n'est pas fourni dans la soumission
        if ($fileData['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($fileData['error'] !== UPLOAD_ERR_OK) {
            return "Erreur lors du téléversement du fichier (code: " . $fileData['error'] . ").";
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/svg+xml'];
        $maxSize = 5 * 1024 * 1024; // 5 Mo

        // 1. Vérification de la taille
        if ($fileData['size'] > $maxSize) {
            return "Le fichier est trop volumineux (max 5 Mo).";
        }

        // 2. Vérification du type (via mime_content_type pour plus de sécurité)
        $mimeType = mime_content_type($fileData['tmp_name']);
        if (!in_array($mimeType, $allowedTypes)) {
            return "Type de fichier non autorisé. Seuls JPG, PNG et SVG sont permis.";
        }

        // 3. Génération du nom de fichier unique
        $extension = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));
        // Ajustement pour SVG si nécessaire
        if ($mimeType === 'image/svg+xml' && $extension !== 'svg') {
            $extension = 'svg';
        }

        $fileName = uniqid('logo_', true) . '.' . $extension;
        $targetPath = $_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_DIR . $fileName;

        // 4. Déplacement du fichier
        if (move_uploaded_file($fileData['tmp_name'], $targetPath)) {
            return $fileName; // Succès: retourne le nouveau nom de fichier
        } else {
            return "Erreur inconnue lors du déplacement du fichier.";
        }
    }

    /**
     * Gère la logique de téléversement du fichier logo.
     * @param array $fileData Tableau issu de $_FILES['logo_file']
     * @return string|null Nom du nouveau fichier si succès, ou message d'erreur (string) si échec, ou null si aucun fichier n'est fourni.
     */
    private function handleImageFondUpload(array $fileData): string|null
    {
        // Si le fichier n'est pas fourni dans la soumission
        if ($fileData['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($fileData['error'] !== UPLOAD_ERR_OK) {
            return "Erreur lors du téléversement du fichier (code: " . $fileData['error'] . ").";
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/svg+xml'];
        $maxSize = 5 * 1024 * 1024; // 5 Mo

        // 1. Vérification de la taille
        if ($fileData['size'] > $maxSize) {
            return "Le fichier est trop volumineux (max 5 Mo).";
        }

        // 2. Vérification du type (via mime_content_type pour plus de sécurité)
        $mimeType = mime_content_type($fileData['tmp_name']);
        if (!in_array($mimeType, $allowedTypes)) {
            return "Type de fichier non autorisé. Seuls JPG, PNG et SVG sont permis.";
        }

        // 3. Génération du nom de fichier unique
        $extension = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));
        // Ajustement pour SVG si nécessaire
        if ($mimeType === 'image/svg+xml' && $extension !== 'svg') {
            $extension = 'svg';
        }

        $fileName = uniqid('image_fond_', true) . '.' . $extension;
        $targetPath = $_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_DIR_2 . $fileName;

        // 4. Déplacement du fichier
        if (move_uploaded_file($fileData['tmp_name'], $targetPath)) {
            return $fileName; // Succès: retourne le nouveau nom de fichier
        } else {
            return "Erreur inconnue lors du déplacement du fichier.";
        }
    }

    /**
     * Traite la soumission du formulaire et enregistre les paramètres.
     * URL: /avva-admin/settings/save (méthode POST)
     */
    public function save(): void
    {
        session_start();

        if (!$this->isUserLoggedIn()) {
            $this->redirect('/avva-admin/login');
            return;
        }

        $settingsEntity = $this->loadSettingsEntity();
        $currentLogoFilename = $settingsEntity->getLogoFilename(); // Ancien nom de fichier
        $currentImageFondFilename = $settingsEntity->getImageFondFilename(); // Ancien nom de fichier

        // 1. Récupération et nettoyage de TOUTES les données POST
        $data = [
            'site_name' => filter_input(INPUT_POST, 'site_name'),
            'president_nom' => filter_input(INPUT_POST, 'president_nom'),
            'president_adresse_rue' => filter_input(INPUT_POST, 'president_adresse_rue'),
            'president_adresse_cp_ville' => filter_input(INPUT_POST, 'president_adresse_cp_ville'),
            // Utiliser FILTER_VALIDATE_EMAIL pour la validation
            'contact_email' => filter_input(INPUT_POST, 'contact_email', FILTER_VALIDATE_EMAIL),
            'contact_phone' => filter_input(INPUT_POST, 'contact_phone'),

            // Partenaire 1
            'partenaire_1_nom' => filter_input(INPUT_POST, 'partenaire_1_nom'),
            'partenaire_1_url' => filter_input(INPUT_POST, 'partenaire_1_url'),
            'partenaire_1_adresse_rue' => filter_input(INPUT_POST, 'partenaire_1_adresse_rue'),
            'partenaire_1_adresse_cp_ville' => filter_input(INPUT_POST, 'partenaire_1_adresse_cp_ville'),
            'partenaire_1_tel' => filter_input(INPUT_POST, 'partenaire_1_tel'),

            // Réseaux Sociaux (URL)
            'social_facebook_url' => filter_input(INPUT_POST, 'social_facebook_url'),
            'social_youtube_url' => filter_input(INPUT_POST, 'social_youtube_url'),

            // Fédérations (URL)
            'ffvelo_url' => filter_input(INPUT_POST, 'ffvelo_url'),
            'codep39_url' => filter_input(INPUT_POST, 'codep39_url'),

            // Couleurs
            'theme_text_color' => filter_input(INPUT_POST, 'theme_text_color'),
            'theme_fond_color' => filter_input(INPUT_POST, 'theme_fond_color'),

            // Fond Pages - Gestion du checkbox
            'page_fond_color' => filter_input(INPUT_POST, 'page_fond_color'),
            'page_fond_transparent' => isset($_POST['page_fond_transparent']) ? 1 : 0
        ];

        // 2. Traitement du Logo (Fichier)
        $newLogoFilename = $currentLogoFilename; // Par défaut, on garde l'ancien

        if (isset($_FILES['logo_file'])) {
            $uploadResult = $this->handleLogoUpload($_FILES['logo_file']);

            if ($uploadResult === null) {
                // Aucun nouveau fichier fourni, on garde l'ancien nom ($newLogoFilename reste $currentLogoFilename)
            } elseif (is_string($uploadResult) && strpos($uploadResult, '.') !== false) {
                // Téléversement réussi, $uploadResult est le nouveau nom de fichier
                $newLogoFilename = $uploadResult;
                
                // Si un nouveau fichier est téléchargé et qu'il y avait un ancien, on le supprime
                if ($currentLogoFilename) {
                    $oldPath = $_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_DIR . $currentLogoFilename;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
            } else {
                // Échec du téléversement ($uploadResult est le message d'erreur)
                $_SESSION['error_message'] = "Erreur de téléversement du logo : " . $uploadResult;
                $this->redirect('/avva-admin/settings');
                return;
            }
        }

        $newImageFondFilename = $currentImageFondFilename; // Par défaut, on garde l'ancien

        if (isset($_FILES['image_fond_file'])) {
            $uploadResult = $this->handleImageFondUpload($_FILES['image_fond_file']);

            if ($uploadResult === null) {
                // Aucun nouveau fichier fourni, on garde l'ancien nom ($newLogoFilename reste $currentLogoFilename)
            } elseif (is_string($uploadResult) && strpos($uploadResult, '.') !== false) {
                // Téléversement réussi, $uploadResult est le nouveau nom de fichier
                $newImageFondFilename = $uploadResult;
                
                // Si un nouveau fichier est téléchargé et qu'il y avait un ancien, on le supprime
                if ($currentImageFondFilename) {
                    $oldPath = $_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_DIR_2 . $currentImageFondFilename;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
            } else {
                // Échec du téléversement ($uploadResult est le message d'erreur)
                $_SESSION['error_message'] = "Erreur de téléversement de l'image de fond : " . $uploadResult;
                $this->redirect('/avva-admin/settings');
                return;
            }
        }

        // 3. Validation des données de base (post-upload)
        if (
            empty($data['site_name']) ||
            !$data['contact_email'] || 
            empty($data['president_nom'])
        ) {
            $_SESSION['error_message'] = "Erreur de validation. Le Nom du Site, l'Email de Contact et le Nom du Président sont obligatoires.";
            $this->redirect('/avva-admin/settings');
            return;
        }

        try {
            // 4. Mise à jour de l'Entité avec tous les Setters
            $settingsEntity->setSiteName($data['site_name']);
            $settingsEntity->setLogoFilename($newLogoFilename); // Enregistrement du nouveau ou de l'ancien nom de fichier
            $settingsEntity->setImageFondFilename($newImageFondFilename); // Enregistrement du nouveau ou de l'ancien nom de fichier

            // Coordonnées Président
            $settingsEntity->setPresidentNom($data['president_nom']);
            $settingsEntity->setPresidentAdresseRue($data['president_adresse_rue']);
            $settingsEntity->setPresidentAdresseCpVille($data['president_adresse_cp_ville']);
            $settingsEntity->setContactEmail($data['contact_email']);
            $settingsEntity->setContactPhone($data['contact_phone']);
            // Partenaire 1
            $settingsEntity->setPartenaire1Nom($data['partenaire_1_nom']);
            $settingsEntity->setPartenaire1Url($data['partenaire_1_url']);
            $settingsEntity->setPartenaire1AdresseRue($data['partenaire_1_adresse_rue']);
            $settingsEntity->setPartenaire1AdresseCpVille($data['partenaire_1_adresse_cp_ville']);
            $settingsEntity->setPartenaire1Tel($data['partenaire_1_tel']);
            // Réseaux Sociaux
            $settingsEntity->setSocialFacebookUrl($data['social_facebook_url']);
            $settingsEntity->setSocialYoutubeUrl($data['social_youtube_url']);
            // Fédérations
            $settingsEntity->setFfveloUrl($data['ffvelo_url']);
            $settingsEntity->setCodep39Url($data['codep39_url']);
            // Couleurs
            $settingsEntity->setThemeTextColor($data['theme_text_color']);
            $settingsEntity->setThemeFondColor($data['theme_fond_color']);
            // Fond Pages
            $settingsEntity->setPageFondColor($data['page_fond_color']);
            $settingsEntity->setPageFondTransparent($data['page_fond_transparent']);

            // 5. Persistance et Enregistrement dans la DB (Flush)
            $this->entityManager->persist($settingsEntity);
            $this->entityManager->flush();

            $_SESSION['success_message'] = "Les paramètres du site ont été enregistrés avec succès !";

        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Erreur Doctrine : Impossible d'enregistrer les paramètres. " . $e->getMessage();
            // Log de l'erreur
        }

        $this->redirect('/avva-admin/settings');
    }

    private function isUserLoggedIn(): bool
    {
        // Assure que la session est démarrée avant de vérifier $_SESSION
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user']);
    }
}