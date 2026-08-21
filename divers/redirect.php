<?php
session_start();

// 1. Importation de PHPMailer (Ajuste le chemin si tu n'utilises pas Composer)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require_once "config/connexion_bdd.php";

// 2. SÉCURITÉ : Redirection si non connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion/login.php');
    exit();
}

$db = createPdoConnection();
$id_u = $_SESSION['user_id'];
$uploadDir = "uploads/";
$msg_status = "";

/**
 * Fonction d'envoi de mail via PHPMailer
 */
function envoyerMailValidation2($table, $data)
{
    $mail = new PHPMailer(true);
    try {
        // --- Configuration Serveur ---
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'dvmta39@gmail.com';
        $mail->Password = 'pnnikshkztituxfj';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        // --- Destinataires ---
        $mail->setFrom('dvmta39@gmail.com', 'Système de Modération');
        $mail->addAddress('dvmta39@gmail.com');

        // --- Gestion des Pièces Jointes ---
        $uploadDir = "uploads/";

        // 1. Image principale (Affiche une icône dans le mail si présente)
        if (!empty($data['filename']) && file_exists($uploadDir . $data['filename'])) {
            $mail->addAttachment($uploadDir . $data['filename'], 'Image_Principale');
            $imgStatus = "✅ Image jointe";
        } else {
            $imgStatus = "❌ Aucune image";
        }

        // 2. Image de fond
        if (!empty($data['background_img']) && file_exists($uploadDir . $data['background_img'])) {
            $mail->addAttachment($uploadDir . $data['background_img'], 'Image_Fond');
        }

        // 3. Fichier Musique
        if (!empty($data['music_file']) && file_exists($uploadDir . $data['music_file'])) {
            $mail->addAttachment($uploadDir . $data['music_file'], 'Musique_Ambiance');
            $musicStatus = "✅ Musique jointe";
        } else {
            $musicStatus = "❌ Aucune musique";
        }

        // --- Contenu du mail ---
        $mail->isHTML(true);
        $mail->Subject = "🔍 Modération Meteastro : " . $data['title_c'];

        $mail->Body = "
            <div style='font-family: Arial, sans-serif; border: 1px solid #333; padding: 20px; border-radius: 10px; background-color: #f9f9f9;'>
                <h2 style='color: #1d4ed8;'>Nouveau contenu à vérifier (Modification)</h2>
                <p><strong>Section :</strong> " . ucfirst($table) . "</p>
                <p><strong>Titre de l'article :</strong> " . $data['title_c'] . "</p>
                <p style='font-size: 13px; color: #666;'>
                    Fichiers : $imgStatus | $musicStatus
                </p>
                <hr style='border: 0; border-top: 1px solid #ddd;'>
                <p><strong>Aperçu du texte :</strong></p>
                <div style='background: white; padding: 15px; border: 1px solid #ddd; border-radius: 5px; max-height: 400px; overflow-y: auto;'>
                    " . $data['contenu'] . "
                </div>
                <br>
                <div style='text-align: center; margin-top: 20px;'>
                    <a href='https://meteastro.fr/redirect.php?id=" . $data['id'] . "&table=" . $table . "' 
                       style='background-color: #22c55e; color: white; padding: 14px 28px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>
                       ✅ VÉRIFIER ET APPROUVER LA MODIFICATION
                    </a>
                </div>
                <p style='font-size: 11px; color: #999; margin-top: 25px; text-align: center;'>
                    L'approbation rendra le contenu immédiatement public.
                </p>
            </div>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Fonction utilitaire pour gérer les uploads
 */
function handleUpload($fileKey, $oldValue, $dir)
{
    if (!empty($_FILES[$fileKey]['name'])) {
        $newName = time() . '_' . basename($_FILES[$fileKey]['name']);
        if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $dir . $newName)) {
            return $newName;
        }
    }
    return $oldValue;
}

// 3. LOGIQUE DE TRAITEMENT DU FORMULAIRE (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int) $_POST['id'];
    $type = $_POST['type'];
    $title_c = htmlspecialchars($_POST['title_contenu']);
    $contenu = $_POST['contenu'];

    $allowed_tables = ['astronomie', 'meteorologie'];
    if (in_array($type, $allowed_tables)) {

        $check = $db->prepare("SELECT filename, background_img, music_file FROM $type WHERE id = ? AND id_users = ?");
        $check->execute([$id, $id_u]);
        $old = $check->fetch();

        if ($old) {
            $file_c = handleUpload('uploadfile', $old['filename'], $uploadDir);
            $file_bg = handleUpload('background_img', $old['background_img'], $uploadDir);
            $file_music = handleUpload('music_file', $old['music_file'], $uploadDir);

            $sql = "UPDATE $type SET 
                    title_contenu = :t, contenu = :c, filename = :f, 
                    background_img = :bg, music_file = :m, verified = 'n' 
                    WHERE id = :id AND id_users = :uid";

            $stmt = $db->prepare($sql);
            $success = $stmt->execute([
                ':t' => $title_c,
                ':c' => $contenu,
                ':f' => $file_c,
                ':bg' => $file_bg,
                ':m' => $file_music,
                ':id' => $id,
                ':uid' => $id_u
            ]);

            if ($success) {
                // Envoi du mail de validation
                $dataForMail = [
                    'id' => $id,
                    'title_c' => $title_c,
                    'contenu' => $contenu,
                    'filename' => $file_c,
                    'background_img' => $file_bg,
                    'music_file' => $file_music
                ];
                envoyerMailValidation2($type, $dataForMail);

                $msg_status = "updated";
            }
        }
    }
}

// 4. RÉCUPÉRATION DES DONNÉES POUR L'INTERFACE
$sql_list = "
    (SELECT id, title_contenu, contenu, filename, background_img, music_file, gallery_images, verified, 
            DATE_FORMAT(date_astronomie, '%d/%m/%Y') as date_f, 'astronomie' as type, 'fa-user-astronaut' as icon 
     FROM astronomie WHERE id_users = :id1)
    UNION
    (SELECT id, title_contenu, contenu, filename, background_img, music_file, gallery_images, verified, 
            DATE_FORMAT(date_meteorologie, '%d/%m/%Y') as date_f, 'meteorologie' as type, 'fa-cloud-bolt' as icon 
     FROM meteorologie WHERE id_users = :id2)
    ORDER BY id DESC";

$query = $db->prepare($sql_list);
$query->execute([':id1' => $id_u, ':id2' => $id_u]);
$user_list = $query->fetchAll(PDO::FETCH_ASSOC);

$themeChoice = $_COOKIE['meteastro_theme'] ?? 'dark';

// ==========================================================================
// CONTROL_CENTER : TRAITEMENT CENTRALISÉ ET RECHARGEMENT DYNAMIQUE DU HUD
// ==========================================================================

$hud_status_message = "";
$hud_status_type = "success"; // success | error

// 1. DÉTECTION ET SÉCURISATION DE L'ID CIBLE (POST ou GET)
$item_id_to_load = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'])) {
    $item_id_to_load = intval($_POST['item_id']);
} elseif (isset($_GET['id'])) {
    $item_id_to_load = intval($_GET['id']);
}

// 2. EXÉCUTION DE LA MISE À JOUR LORS DE LA SOUMISSION DU FORMULAIRE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_update_hud'])) {

    $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
    $item_type = isset($_POST['item_type']) ? trim(strtolower($_POST['item_type'])) : '';

    // Extraction des états d'interrupteurs (booléens convertis en INT)
    $show_images = isset($_POST['show_images']) ? 1 : 0;
    $enable_music = isset($_POST['enable_music']) ? 1 : 0;

    // Déclaration des variables contextuelles
    $bg_mode = "animated";
    $feed_id = "";

    if (in_array($item_type, ['astronomie', 'astronomy'])) {
        $feed_id = isset($_POST['feed_id']) ? trim($_POST['feed_id']) : '';
        $bg_mode = isset($_POST['bg_mode']) ? trim($_POST['bg_mode']) : 'animated';
    } elseif (in_array($item_type, ['meteorologie', 'météorologie', 'meteorology'])) {
        $meteo_widgets = isset($_POST['meteo_widgets']) ? 1 : 0;
    }

    // Traitement sécurisé du téléversement de fichier
    $final_filename = null;
    if (isset($_FILES['uploadfile']) && $_FILES['uploadfile']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['uploadfile']['tmp_name'];
        $file_name = $_FILES['uploadfile']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($file_ext, $allowed_extensions)) {
            $clean_name = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", pathinfo($file_name, PATHINFO_FILENAME)) . '.' . $file_ext;
            $upload_dir = 'uploads/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (move_uploaded_file($file_tmp, $upload_dir . $clean_name)) {
                $final_filename = $clean_name;
            }
        }
    }

    // Exécution de la persistance en base de données via requêtes préparées PDO
    if ($item_id > 0) {
        try {
            // Détermination de la table cible selon la branche d'activité
            $table_target = in_array($item_type, ['astronomie', 'astronomy']) ? 'astronomie' : 'meteorologie';

            // Initialisation des champs SQL communs
            $fields = [
                "show_images = :show_images",
                "enable_music = :enable_music"
            ];

            // Injection des paramètres spécifiques
            if ($table_target === 'astronomie') {
                $fields[] = "background_mode = :bg_mode";
                $fields[] = "hud_feed_id = :feed_id";
            }

            // Ajout dynamique du nom de fichier si un nouveau média a été envoyé
            if ($final_filename !== null) {
                $fields[] = "filename = :filename";
            }

            // Assemblage final de la requête SQL
            $query = "UPDATE {$table_target} SET " . implode(", ", $fields) . " WHERE id = :id";
            $query_stmt = $db->prepare($query);

            // Bind des paramètres génériques
            $query_stmt->bindValue(':show_images', $show_images, PDO::PARAM_INT);
            $query_stmt->bindValue(':enable_music', $enable_music, PDO::PARAM_INT);
            $query_stmt->bindValue(':id', $item_id, PDO::PARAM_INT);

            if ($final_filename !== null) {
                $query_stmt->bindValue(':filename', $final_filename, PDO::PARAM_STR);
            }

            // Bind des paramètres spécifiques
            if ($table_target === 'astronomie') {
                $query_stmt->bindValue(':bg_mode', $bg_mode, PDO::PARAM_STR);
                $query_stmt->bindValue(':feed_id', $feed_id, PDO::PARAM_STR);
            }

            $query_stmt->execute();
            $hud_status_message = "Configuration [Matrice: " . strtoupper($item_type) . "] appliquée et synchronisée avec succès.";

            // Sécurité : On s'assure que l'ID modifié est bien celui chargé pour le rendu de la page
            $item_id_to_load = $item_id;

        } catch (Exception $e) {
            $hud_status_type = "error";
            $hud_status_message = "Échec de synchronisation de la matrice : " . $e->getMessage();
        }
    }
}

// 3. LOGIQUE DE SYNCHRONISATION FORCEE DE L'ÉLÉMENT COURANT ($current_item)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Récupération sécurisée de l'utilisateur connecté
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// SÉCURITÉ : Si l'utilisateur n'est pas connecté, redirection immédiate
if ($user_id <= 0) {
    header('Location: login.php');
    exit('Accès refusé : session invalide.');
}

$current_item = null;
$item_id_to_load = isset($item_id_to_load) ? intval($item_id_to_load) : 0;

// 3. Tentative de chargement de l'item demandé (uniquement s'il appartient à l'utilisateur)
if ($item_id_to_load > 0) {
    // Test table Astronomie
    $stmt = $db->prepare("SELECT *, 'astronomie' AS type FROM astronomie WHERE id = ?");
    $stmt->execute([$item_id_to_load]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($res && intval($res['id_users']) === $user_id) {
        $current_item = $res;
    } else {
        // Test table Météorologie
        $stmt = $db->prepare("SELECT *, 'meteorologie' AS type FROM meteorologie WHERE id = ?");
        $stmt->execute([$item_id_to_load]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($res && intval($res['id_users']) === $user_id) {
            $current_item = $res;
        }
    }

    // Si un ID précis était demandé mais qu'il n'existe pas ou n'appartient pas à l'utilisateur : Blocage
    if (!$current_item) {
        header('Location: index.php');
        exit();
    }
}

// 4. Système de FALLBACK : Si aucun ID n'était demandé ($item_id_to_load === 0), charger son premier article disponible
if (!$current_item) {
    // Premier item en Astronomie pour CET utilisateur
    $stmt = $db->prepare("SELECT *, 'astronomie' AS type FROM astronomie WHERE id_users = ? ORDER BY id ASC LIMIT 1");
    $stmt->execute([$user_id]);
    $current_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$current_item) {
        // Sinon, premier item en Météorologie pour CET utilisateur
        $stmt = $db->prepare("SELECT *, 'meteorologie' AS type FROM meteorologie WHERE id_users = ? ORDER BY id ASC LIMIT 1");
        $stmt->execute([$user_id]);
        $current_item = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// 5. Initialisation des variables pour l'affichage HTML et les boutons radio
$feed_id = $current_item['hud_feed_id'] ?? '';
$item_type = htmlspecialchars(strtolower(trim($current_item['type'] ?? '')));

// Évaluation des états booléens pour l'interface utilisateur
$is_images_checked = (isset($current_item['show_images']) && (int) $current_item['show_images'] === 1);
$is_music_checked = (isset($current_item['enable_music']) && (int) $current_item['enable_music'] === 1);
$music_file_raw = (isset($current_item['music_file']) && trim($current_item['music_file']) !== '0') ? trim($current_item['music_file']) : '';
$is_audio_preview_visible = ($is_music_checked && !empty($music_file_raw));
?>
<!DOCTYPE html>
<html lang="fr-FR" data-bs-theme="<?= ($themeChoice === 'light') ? 'light' : 'dark'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Espace de création Meteastro - Partagez vos observations astronomiques et météo.">
    <title>Création de Contenu | Meteastro</title>

    <link rel="icon" type="image/png" href="/ressources/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

    <style>
        :root {
            --glass-card: rgba(15, 23, 42, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            background-color: #020617;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .glass-container {
            background: var(--glass-card);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            margin-bottom: 2rem;
        }

        /* Personnalisation de l'éditeur pour l'intégrer au design */
        .note-editor.note-frame {
            border: 1px solid var(--glass-border) !important;
            border-radius: 12px !important;
            overflow: hidden;
            background: white !important;
            /* L'éditeur reste blanc pour la lisibilité */
        }

        .note-toolbar {
            background-color: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }

        /* Style de la liste de contenu */
        .content-list-wrapper {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .content-item {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            transition: all 0.3s ease;
        }

        .content-item:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        /* Bouton Nébuleuse */
        .btn-combined {
            position: relative;
            padding: 3px;
            background: transparent;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 1;
            margin-bottom: 2rem;
        }

        .btn-combined::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg, transparent, #3b82f6, #ef4444, transparent 60%);
            animation: rotateGradient 4s linear infinite;
            z-index: -2;
        }

        .btn-content {
            background: #020617;
            backdrop-filter: blur(10px);
            padding: 15px 35px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #ffffff;
            font-size: 1.1rem;
            font-weight: 700;
        }

        @keyframes rotateGradient {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .rocket-icon {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .edit-full-page {
            position: fixed;
            top: 0;
            left: 100%;
            /* Commence en dehors à droite */
            width: 100%;
            height: 100%;
            background: #020617;
            /* Même couleur que le body */
            z-index: 9999;
            overflow-y: auto;
            transition: left 0.5s cubic-bezier(0.77, 0, 0.175, 1);
        }

        .edit-full-page.active {
            left: 0;
            /* Glisse pour couvrir tout l'écran */
        }

        /* On masque le scroll du body quand le mode édition est actif */
        body.modal-open {
            overflow: hidden;
        }

        /* Style des inputs dans le formulaire d'édition */
        .input-glass {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            color: white;
            padding: 12px;
            border-radius: 12px;
            margin-top: 5px;
        }

        .btn-cosmic-glass {
            position: relative;
            background: rgba(255, 255, 255, 0.05);
            /* Fond verre très subtil */
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 18px;
            border-radius: 15px;
            cursor: pointer;
            overflow: hidden;
            font-weight: 600;
            font-size: 1.1rem;
            backdrop-filter: blur(5px);
            transition: all 0.4s ease;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* L'arrière-plan étoilé animé */
        .btn-cosmic-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 200%;
            height: 100%;
            background-image: url('https://www.transparenttextures.com/patterns/stardust.png');
            /* Texture étoiles subtile */
            background-color: #0f172a;
            opacity: 0;
            transition: opacity 0.4s ease, transform 10s linear;
            z-index: 1;
        }

        /* Effets au survol */
        .btn-cosmic-glass:hover {
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            transform: translateY(-2px);
        }

        .btn-cosmic-glass:hover .btn-cosmic-bg {
            opacity: 1;
            transform: translateX(-50%);
            /* Lent défilement des étoiles */
        }

        .btn-text-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
        }

        /* Changement de couleur d'icône au survol */
        .btn-cosmic-glass:hover .fas {
            color: #3b82f6;
            transition: color 0.3s ease;
        }

        .progress-btn {
            position: relative;
            overflow: hidden;
            /* Pour que le remplissage ne dépasse pas des arrondis */
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 55px;
            /* Ajuste selon tes besoins */
        }

        /* Le fond qui se remplit */
        .progress-fill {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 0%;
            /* Départ à zéro */
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            /* Dégradé cosmique */
            transition: width 0.2s ease-out;
            z-index: 1;
        }

        /* Le texte par-dessus */
        .btn-text-content {
            position: relative;
            z-index: 2;
            /* Force le texte au-dessus de la barre */
            display: flex;
            align-items: center;
            pointer-events: none;
            /* Évite les bugs de clic sur l'icône */
            color: #fff;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        /* État désactivé pendant l'envoi */
        .progress-btn:disabled {
            cursor: not-allowed;
            filter: brightness(0.9);
        }
    </style>
</head>

<body class="<?= ($themeChoice === 'light') ? 'lightmode' : ''; ?>">

    <div class="container py-5">
        <div class="row align-items-start">
            <div class="col-lg-7">
                <div class="glass-container">
                    <header class="mb-4">
                        <h1 class="display-5 fw-bold text-white mb-2">
                            Espace Création <i class="fa-solid fa-rocket rocket-icon text-primary"></i>
                        </h1>
                        <p class="text-secondary">Gérez vos publications astronomiques et météorologiques.</p>
                    </header>

                    <div class="content-form">
                        <button class="btn-combined" id="popup_open">
                            <div class="btn-content">
                                <i class="fas fa-plus-circle"></i>
                                <span>NOUVELLE PUBLICATION</span>
                            </div>
                        </button>
                    </div>

                    <div class="mt-4">
                        <h3 class="h5 text-white mb-4"><i class="fa-solid fa-folder-open me-2 text-info"></i> Vos
                            dernières activités</h3>

                        <div class="content-list-wrapper">
                            <?php if (empty($user_list)): ?>
                                <div class="text-center py-4 text-secondary opacity-50">
                                    <i class="fa-solid fa-ghost fa-3x mb-3"></i>
                                    <p>Vous n'avez pas encore de publications.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($user_list as $item): ?>
                                    <div class="content-item d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-3 text-white">
                                            <div class="icon-circle bg-dark p-2 rounded-circle border border-secondary border-opacity-25 text-center"
                                                style="width: 45px;">
                                                <i class="fa-solid <?= $item['icon'] ?> text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold"><?= $item['title_contenu'] ?></div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <small class="text-secondary small fw-normal text-uppercase"
                                                        style="letter-spacing: 1px; font-size: 0.7rem;">
                                                        <?= $item['type'] ?>
                                                    </small>
                                                    <span class="text-secondary opacity-25">|</span>
                                                    <small class="text-white-50" style="font-size: 0.75rem;">
                                                        <i class="fa-regular fa-calendar-check me-1"></i> <?= $item['date_f'] ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if ($item['verified'] == 'y'): ?>
                                                <span class="status-badge status-verified"><i
                                                        class="fa-solid fa-check-double me-1"></i>Vérifié</span>
                                            <?php else: ?>
                                                <span class="status-badge status-pending"><i class="fa-solid fa-clock me-1"></i>En
                                                    attente</span>
                                            <?php endif; ?>
                                            <div class="dropdown">
                                                <button class="btn btn-link text-secondary p-0" data-bs-toggle="dropdown">
                                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-dark">
                                                    <li>
                                                        <button type="button" class="dropdown-item small btn-edit-trigger"
                                                            data-id="<?= $item['id'] ?>"
                                                            data-type="<?= strtolower($item['type']) ?>"
                                                            data-title-c="<?= $item['title_contenu'] ?>"
                                                            data-contenu="<?= htmlspecialchars($item['contenu'] ?? '') ?>"
                                                            data-filename="<?= htmlspecialchars($item['filename'] ?? '') ?>"
                                                            data-bg="<?= htmlspecialchars($item['background_img'] ?? '') ?>"
                                                            data-music="<?= htmlspecialchars($item['music_file'] ?? '') ?>"
                                                            data-gallery="<?= htmlspecialchars($item['gallery_images'] ?? '') ?>">
                                                            <i class="fa-solid fa-pen me-2 small"></i>Modifier
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <a href="?id=<?= (int) $item['id'] ?>"
                                                            class="dropdown-item small <?= (isset($current_item['id']) && (int) $item['id'] === (int) $current_item['id']) ? 'active' : '' ?>">
                                                            <i class="fa-solid fa-gear me-2 small"
                                                                style="color: var(--color-neon);"></i>Paramètres
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top border-secondary border-opacity-25">
                        <a href="index.php" class="text-decoration-none text-info small">
                            <i class="fa-solid fa-arrow-left me-2"></i> Retour au tableau de bord
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 d-none d-lg-block text-center mt-5">
                <img src="/ressources/contenu.png" alt="Illustration astronomie" class="img-fluid rounded-4 shadow-lg"
                    style="max-height: 450px;">
                <div class="mt-4 text-white-50 fst-italic">
                    "L'astronomie est l'école de la patience."
                </div>
            </div>
        </div>
    </div>

    <div id="edit-overlay-page" class="edit-full-page">
        <div class="container py-5">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <button type="button" class="btn btn-outline-light rounded-pill px-4" id="close-edit">
                    <i class="fa-solid fa-arrow-left me-2"></i> Annuler et retourner
                </button>

                <div class="text-center">
                    <h2 class="text-white fw-bold mb-0">Modification de la publication</h2>
                    <span id="edit-type-badge" class="badge rounded-pill mt-2 px-3 py-2 text-uppercase"
                        style="letter-spacing: 1px;"></span>
                </div>

                <div style="width: 140px;"></div>
            </div>

            <div class="glass-container">
                <form id="form-edit-content" action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="edit-id">
                    <input type="hidden" name="type" id="edit-type">

                    <div class="row">
                        <div class="col-lg-4 border-end border-secondary border-opacity-25">
                            <div class="mb-4">
                                <label class="label-glass mb-2">Couverture actuelle</label>
                                <div id="container-prev-filename" class="mb-2">
                                    <img id="prev-filename" src=""
                                        class="rounded w-100 shadow-sm border border-secondary"
                                        style="display:none; object-fit: cover; max-height: 200px;">
                                </div>
                                <input type="file" name="uploadfile" class="input-glass">
                            </div>

                            <div class="mb-4">
                                <label class="label-glass mb-2">Image de fond</label>
                                <div id="container-prev-bg" class="mb-2">
                                    <img id="prev-bg" src="" class="rounded w-100 shadow-sm border border-secondary"
                                        style="display:none; object-fit: cover; max-height: 120px; opacity: 0.7;">
                                </div>
                                <input type="file" name="background_img" class="input-glass">
                            </div>

                            <div class="mb-4">
                                <label class="label-glass mb-2">Musique</label>
                                <div id="container-prev-music" class="mb-2">
                                </div>
                                <input type="file" name="music_files[]" class="input-glass" multiple>
                            </div>

                            <div class="mb-4">
                                <label class="label-glass mb-2">Galerie photos</label>
                                <div id="prev-gallery" class="d-flex flex-wrap gap-2 mb-2">
                                </div>
                                <input type="file" name="gallery_files[]" class="input-glass" multiple>
                            </div>
                        </div>

                        <div class="col-lg-8 ps-lg-5">

                            <div class="mb-4">
                                <label class="label-glass">Titre de l'article</label>
                                <input type="text" name="title_contenu" id="edit-title-c" class="input-glass" required>
                            </div>

                            <div class="editor-container mb-4">
                                <label class="label-glass">Contenu de l'article</label>
                                <textarea id="edit-summernote" name="contenu"></textarea>
                            </div>

                            <button type="button" class="btn-cosmic-glass w-100 mt-4 progress-btn" id="btn-submit-edit">
                                <div class="progress-fill"></div>

                                <span class="btn-text-content">
                                    <i class="fas fa-satellite-dish me-3"></i>
                                    <span class="btn-label">Transmettre les données</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="settings-overlay" class="edit-full-page">
        <div class="container py-5">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <a href="#" class="btn btn-outline-light rounded-pill px-4">
                    <i class="fa-solid fa-arrow-left me-2"></i> Retour
                </a>
                <h2 class="text-white mb-0">Paramètres du Terminal</h2>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="glass-container text-start">
                        <form action="update_settings.php" method="POST">
                            <div class="mb-4">
                                <label class="text-white-50 small">Nom d'explorateur</label>
                                <input type="text" name="name" class="input-glass"
                                    value="<?= htmlspecialchars($userName) ?>">
                            </div>
                            <div class="mb-4">
                                <label class="text-white-50 small">Adresse de communication (Email)</label>
                                <input type="email" name="email" class="input-glass"
                                    value="<?= htmlspecialchars($userEmail) ?>">
                            </div>
                            <div class="mb-4">
                                <label class="text-white-50 small">URL de l'image de fond (Table)</label>
                                <input type="text" name="bg_url" class="input-glass"
                                    value="<?= htmlspecialchars($userBg) ?>">
                            </div>
                            <div class="mb-4">
                                <label class="text-white-50 small">Nouveau code d'accès (Password)</label>
                                <input type="password" name="password" class="input-glass"
                                    placeholder="Laisser vide pour ne pas changer">
                            </div>
                            <button type="submit" class="btn-cosmic-glass">
                                <i class="fa-solid fa-sync me-2"></i> Synchroniser les données
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel-overlay" id="settings-overlay"></div>

    <div class="side-panel" id="settingsSidebar" data-should-open="<?= isset($_GET['id']) ? 'true' : 'false'; ?>">

        <div class="panel-header">
            <div>
                <span class="panel-subtitle" id="settings-system-mode">
                    <?= ($item_type === 'astronomie') ? '// SYSTEM_MODE_ASTRONOMY_ACTIVE' : '// SYSTEM_MODE_METEOROLOGY_ACTIVE'; ?>
                </span>
                <h2 id="settingsSidebarLabel">
                    <i class="fa-solid fa-gear me-2" style="color: var(--color-neon);"></i>Paramètres
                </h2>
            </div>
            <a href="?" class="close-btn" aria-label="Fermer"></a>
        </div>

        <div class="panel-content">
            <form id="form-page-settings" method="POST" action="?id=<?= (int) ($current_item['id'] ?? 0); ?>"
                enctype="multipart/form-data" class="panel-grid">

                <input type="hidden" name="action_update_hud" value="1">
                <input type="hidden" name="item_id" value="<?= (int) ($current_item['id'] ?? 0); ?>">
                <input type="hidden" name="item_type" value="<?= htmlspecialchars($item_type); ?>">

                <div class="panel-left-col">

                    <div class="mb-4">
                        <?php
                        $has_filename = (isset($current_item['filename']) && trim($current_item['filename']) !== '' && trim($current_item['filename']) !== '0');
                        $img_path = $has_filename ? '/uploads/' . htmlspecialchars($current_item['filename']) : '';
                        ?>
                        <?php if ($has_filename): ?>
                            <div class="hud-media-container" id="container-prev-filename-settings">
                                <img src="<?= $img_path; ?>" alt="Aperçu de la couverture">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="data-card mission-briefing mb-3">
                        <span class="card-label">ÉLÉMENT SÉLECTIONNÉ</span>
                        <div class="card-value">
                            <?= $current_item['title_contenu'] ?? 'Chargement...'; ?>
                        </div>
                    </div>

                    <?php if ($item_type === 'astronomie'): ?>
                        <div class="data-card border border-info mb-3" style="background: rgba(0, 242, 255, 0.03);">
                            <span class="card-label" style="color: var(--neon-blue);"><i
                                    class="fa-solid fa-user-astronaut me-1"></i> ASTRO_METRICS</span>
                            <div class="panel-description small text-white-50 mb-2">Flux de données stellaires et
                                cartographie céleste activés.</div>
                            <div class="d-flex justify-content-between border-top border-secondary pt-2 mt-2">
                                <span class="small text-muted">Focalisation :</span>
                                <span class="small fw-bold text-info">Deep Space Network</span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (in_array($item_type, ['meteorologie', 'météorologie'])): ?>
                        <div class="data-card border border-warning mb-3" style="background: rgba(255, 204, 0, 0.03);">
                            <span class="card-label" style="color: var(--star-gold);"><i
                                    class="fa-solid fa-cloud-sun-rain me-1"></i> METEO_TELEMETRY</span>
                            <div class="panel-description small text-white-50 mb-2">Capteurs troposphériques et analyses
                                barométriques en ligne.</div>
                            <div class="d-flex justify-content-between border-top border-secondary pt-2 mt-2">
                                <span class="small text-muted">Index Alerte :</span>
                                <span class="small fw-bold text-warning">Temps Réel Actif</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="panel-right-col d-flex flex-column gap-3">

                    <div class="data-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="card-label" style="color: var(--neon-blue);">VISUAL_FEED</span>
                                <div class="card-value fs-5">Afficher les images</div>
                                <p class="panel-description mt-2" style="font-size: 0.85rem;">
                                    Active ou désactive le rendu des flux d'images sur l'interface publique.
                                </p>
                            </div>
                            <div class="form-check form-switch custom-hud-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="show_images"
                                    value="1" <?= (isset($current_item['show_images']) && (int) $current_item['show_images'] === 1) ? 'checked' : ''; ?>>
                            </div>
                        </div>
                    </div>

                    <div class="data-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <span class="card-label" style="color: var(--neon-purple);">AUDIO_MODULE</span>
                                <div class="card-value fs-5">Musique de fond</div>
                                <p class="panel-description mt-2" style="font-size: 0.85rem;">
                                    Lance l'ambiance sonore atmosphérique configurée dès l'initialisation de la page.
                                </p>
                            </div>
                            <div class="form-check form-switch custom-hud-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="enable_music"
                                    value="1" <?= (isset($current_item['enable_music']) && (int) $current_item['enable_music'] === 1) ? 'checked' : ''; ?>>
                            </div>
                        </div>

                        <?php
                        $is_music_checked = (isset($current_item['enable_music']) && (int) $current_item['enable_music'] === 1);
                        $music_file_raw = (isset($current_item['music_file']) && trim($current_item['music_file']) !== '0') ? trim($current_item['music_file']) : '';
                        ?>
                        <?php if ($is_music_checked && !empty($music_file_raw)): ?>
                            <div class="hud-audio-container" id="container-prev-music-settings">
                                <div class="mt-2 p-2 rounded-2"
                                    style="background: rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.05);">
                                    <span class="card-label"
                                        style="font-size: 0.65rem; color: var(--color-text-muted); display: block; letter-spacing: 1px;">
                                        // AUDIO_PREVIEW_PLAYER
                                    </span>
                                    <audio controls class="w-100 mt-2"
                                        src="/uploads/<?= htmlspecialchars($music_file_raw); ?>"></audio>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($item_type === 'astronomie'): ?>
                        <?php
                        $bg_mode = $current_item['background_mode'] ?? 'animated';
                        $feed_id = $current_item['hud_feed_id'] ?? '';
                        ?>
                        <div class="data-card">
                            <span class="card-label" style="color: var(--neon-blue);">// METEASTRO_MATRIX_GENERATOR</span>
                            <div class="card-value fs-5 mb-2">Générateur d'Arrière-plans</div>
                            <p class="panel-description mb-4" style="font-size: 0.85rem;">
                                Sélectionnez le flux visuel généré automatiquement par MeteAstro pour habiller
                                l'arrière-plan de votre interface.
                            </p>

                            <div class="hud-tabs-php-wrapper">

                                <input type="radio" id="php-tab-toggle-animated" name="bg_mode" value="animated"
                                    class="tab-switch-radio d-none" <?= ($bg_mode === 'animated') ? 'checked' : ''; ?>>
                                <input type="radio" id="php-tab-toggle-static" name="bg_mode" value="static"
                                    class="tab-switch-radio d-none" <?= ($bg_mode === 'static') ? 'checked' : ''; ?>>

                                <ul class="nav nav-tabs hud-tabs border-secondary mb-3" role="tablist">
                                    <li class="nav-item">
                                        <label for="php-tab-toggle-animated"
                                            class="nav-link text-uppercase tab-trigger-animated" style="cursor: pointer;">//
                                            Flux Animés</label>
                                    </li>
                                    <li class="nav-item">
                                        <label for="php-tab-toggle-static"
                                            class="nav-link text-uppercase tab-trigger-static" style="cursor: pointer;">//
                                            Flux Statiques</label>
                                    </li>
                                </ul>

                                <div class="tab-content-php-custom">

                                    <div class="tab-pane-php-custom pane-animated-content">
                                        <div class="hud-feed-grid">

                                            <label class="hud-feed-card-label">
                                                <input type="radio" name="feed_id" value="astro_nebula_animated"
                                                    class="d-none" <?= ($feed_id === 'astro_nebula_animated') ? 'checked' : ''; ?>>
                                                <div
                                                    class="hud-feed-card <?= ($feed_id === 'astro_nebula_animated') ? 'active' : ''; ?>">
                                                    <div class="feed-preview-wrapper">
                                                        <div class="feed-animated-simulation aurora"></div>
                                                    </div>
                                                    <div class="feed-info"><span class="feed-title">Nébuleuse
                                                            Active</span><span class="feed-tech">// CANVAS_PARTICLES</span>
                                                    </div>
                                                </div>
                                            </label>

                                            <label class="hud-feed-card-label">
                                                <input type="radio" name="feed_id" value="astro_constellations_animated"
                                                    class="d-none" <?= ($feed_id === 'astro_constellations_animated') ? 'checked' : ''; ?>>
                                                <div
                                                    class="hud-feed-card <?= ($feed_id === 'astro_constellations_animated') ? 'active' : ''; ?>">
                                                    <div class="feed-preview-wrapper">
                                                        <div class="feed-animated-simulation matrix-grid"></div>
                                                    </div>
                                                    <div class="feed-info"><span class="feed-title">Réseau
                                                            Céleste</span><span class="feed-tech">// CSS_MATRIX_NODES</span>
                                                    </div>
                                                </div>
                                            </label>

                                            <label class="hud-feed-card-label">
                                                <input type="radio" name="feed_id" value="astro_stars_animated"
                                                    class="d-none" <?= ($feed_id === 'astro_stars_animated') ? 'checked' : ''; ?>>
                                                <div
                                                    class="hud-feed-card <?= ($feed_id === 'astro_stars_animated') ? 'active' : ''; ?>">
                                                    <div class="feed-preview-wrapper">
                                                        <div class="feed-animated-simulation starfield"></div>
                                                    </div>
                                                    <div class="feed-info"><span class="feed-title">Champ
                                                            Hyperespace</span><span class="feed-tech">//
                                                            WEBGL_STARS_FLOW</span></div>
                                                </div>
                                            </label>

                                        </div>
                                    </div>

                                    <div class="tab-pane-php-custom pane-static-content">
                                        <div class="hud-feed-grid">

                                            <label class="hud-feed-card-label">
                                                <input type="radio" name="feed_id" value="astro_deep_space_static"
                                                    class="d-none" <?= ($feed_id === 'astro_deep_space_static') ? 'checked' : ''; ?>>
                                                <div
                                                    class="hud-feed-card <?= ($feed_id === 'astro_deep_space_static') ? 'active' : ''; ?>">
                                                    <div class="feed-preview-wrapper">
                                                        <div class="feed-static-simulation deep-space"></div>
                                                    </div>
                                                    <div class="feed-info"><span class="feed-title">Espace
                                                            Profond</span><span class="feed-tech">// GRADIENT_MATRICE</span>
                                                    </div>
                                                </div>
                                            </label>

                                            <label class="hud-feed-card-label">
                                                <input type="radio" name="feed_id" value="astro_supernova_static"
                                                    class="d-none" <?= ($feed_id === 'astro_supernova_static') ? 'checked' : ''; ?>>
                                                <div
                                                    class="hud-feed-card <?= ($feed_id === 'astro_supernova_static') ? 'active' : ''; ?>">
                                                    <div class="feed-preview-wrapper">
                                                        <div class="feed-static-simulation supernova-static"></div>
                                                    </div>
                                                    <div class="feed-info"><span class="feed-title">Supernova
                                                            Statik</span><span class="feed-tech">// HIGH_CONTRAST_CSS</span>
                                                    </div>
                                                </div>
                                            </label>

                                            <label class="hud-feed-card-label">
                                                <input type="radio" name="feed_id" value="astro_blueprint_static"
                                                    class="d-none" <?= ($feed_id === 'astro_blueprint_static') ? 'checked' : ''; ?>>
                                                <div
                                                    class="hud-feed-card <?= ($feed_id === 'astro_blueprint_static') ? 'active' : ''; ?>">
                                                    <div class="feed-preview-wrapper">
                                                        <div class="feed-static-simulation hud-blueprint"></div>
                                                    </div>
                                                    <div class="feed-info"><span class="feed-title">Matrice Éco</span><span
                                                            class="feed-tech">// HUD_GRID_ONLY</span></div>
                                                </div>
                                            </label>

                                        </div>
                                    </div>

                                </div>
                            </div>

                            <button type="submit" name="force_sync" value="1" id="btn-trigger-generation"
                                class="w-100 btn-hud-action text-uppercase mt-4">
                                <i class="fa-solid fa-wand-magic-sparkles me-2"></i> Forcer la Synchronisation
                            </button>

                            <div class="mt-3">
                                <label class="hud-feed-card-label w-100 m-0">
                                    <input type="radio" name="feed_id" value="" class="d-none" <?= (empty($feed_id)) ? 'checked' : ''; ?>>

                                    <div class="hud-feed-card <?= (empty($feed_id)) ? 'active' : ''; ?>">
                                        <div class="feed-preview-wrapper" style="background: #05070a; height: 50px;">
                                        </div>
                                        <div class="feed-info">
                                            <span class="feed-title">Aucun effet</span>
                                            <span class="feed-tech">// NULL_BACKGROUND</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mt-4">
                        <button type="submit" class="btn-live border-0 text-uppercase w-100">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Sauvegarder les paramètres de la matrice
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
    <style>
        /* ==========================================================================
   THÈME HUD SPATIAL & CYBERPUNK - CONFIGURATION COMPLÈTE
   ========================================================================== */

        :root {
            /* Couleurs de base */
            --deep-space: #05070a;
            --deep-dark: #020305;

            /* Accents Néon */
            --neon-blue: #00f2ff;
            --neon-purple: #bc13fe;
            --nebula-blue: #00d4ff;
            --star-gold: #ffcc00;

            /* Glassmorphism */
            --glass: rgba(10, 15, 25, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
            --glass-hover: rgba(255, 255, 255, 0.15);

            /* Fonts */
            --font-main: 'Inter', sans-serif;
            --font-astro: 'Orbitron', sans-serif;

            /* Configuration Panneau HUD */
            --bg-hud: rgba(10, 12, 22, 0.98);
            /* Légèrement opaque pour le plein écran */
            --bg-card: rgba(20, 26, 42, 0.4);
            --border-hud: rgba(66, 252, 241, 0.12);
            --color-neon: #66fcf1;
            --color-accent: #ff0055;
            --color-text-main: #f0f4f8;
            --color-text-muted: #8a99ad;
            --font-hud: 'Orbitron', 'Inter', sans-serif;

            --radius-main: 24px;
            --radius-sub: 24px;

            --transition-smooth: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
        }

        /* Verrouillage du scroll du site lorsque le panneau est actif */
        body.panel-active {
            overflow: hidden;
        }

        /* ==========================================================================
   STRUCTURE DU PANNEAU LATÉRAL (SIDE PANEL)
   ========================================================================== */

        .side-panel {
            position: fixed;
            top: 0;
            right: 0;
            width: 100vw;
            /* Utilise toute la largeur de l'écran */
            height: 100vh;
            background-color: var(--bg-hud);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            color: var(--color-text-main);
            z-index: 99999;

            /* Animation de glissement (fermé par défaut, poussé à droite) */
            transform: translateX(100%);
            transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);

            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-sizing: border-box;
        }

        /* Classe injectée par JS pour ouvrir le volet */
        .side-panel.open {
            transform: translateX(0);
        }

        /* Overlay d'arrière-plan (Assombrissement du reste du site) */
        .panel-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(4, 5, 10, 0.6);
            backdrop-filter: blur(4px);
            z-index: 99998;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s ease;
        }

        .panel-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }

        /* ==========================================================================
   EN-TÊTE DU PANNEAU (PANEL HEADER)
   ========================================================================== */

        .panel-header {
            padding: 24px 40px;
            border-bottom: 1px solid var(--border-hud);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(11, 14, 26, 0.5);
        }

        .panel-subtitle {
            font-family: var(--font-hud);
            font-size: 0.7rem;
            color: var(--color-neon);
            letter-spacing: 2px;
            display: block;
            margin-bottom: 4px;
            opacity: 0.8;
        }

        .panel-header h2 {
            margin: 0;
            font-family: var(--font-hud);
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Bouton Fermer Minimaliste / Cyberpunk */
        .close-btn {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-hud);
            width: 44px;
            height: 44px;
            border-radius: var(--radius-sub);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-smooth);
            position: relative;
        }

        .close-btn:hover {
            background: rgba(255, 0, 85, 0.1);
            border-color: var(--color-accent);
        }

        .close-btn::before,
        .close-btn::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 2px;
            background-color: var(--color-text-main);
            transition: var(--transition-smooth);
        }

        .close-btn::before {
            transform: rotate(45deg);
        }

        .close-btn::after {
            transform: rotate(-45deg);
        }

        .close-btn:hover::before,
        .close-btn:hover::after {
            background-color: var(--color-accent);
        }

        /* ==========================================================================
   CONTENU & GRILLE ADAPTATIVE (PANEL CONTENT)
   ========================================================================== */

        .panel-content {
            padding: 40px;
            flex: 1;
            overflow-y: auto;
            box-sizing: border-box;
        }

        /* Grille principale limitée à 1400px pour éviter l'éparpillements sur écrans larges */
        .panel-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 32px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        /* Passage en 2 colonnes sur Desktop */
        @media (min-width: 992px) {
            .panel-grid {
                grid-template-columns: 450px 1fr;
                /* Gauche fixe (Média), Droite fluide (Options) */
                align-items: start;
            }
        }

        /* ==========================================================================
   COMPOSANTS VISUELS (MEDIA & DATA CARDS)
   ========================================================================== */

        /* Force le conteneur à contrecarrer tout conflit de hauteur */
        .hud-media-container {
            width: 100%;
            min-height: 150px;
            /* Évite que le conteneur s'écrase à 0px de hauteur */
            background: #000;
            position: relative;
        }

        /* Force l'image à occuper l'espace et à être visible */
        .hud-media-container img {
            display: block !important;
            width: 100% !important;
            height: auto !important;
            max-height: 300px;
            object-fit: cover;
        }

        /* Tag décoratif "LIVE FEED" */
        .hud-media-container::before {
            content: '● LIVE FEED';
            position: absolute;
            top: 16px;
            left: 16px;
            font-family: var(--font-hud);
            font-size: 0.65rem;
            font-weight: bold;
            color: var(--color-accent);
            background: rgba(10, 12, 22, 0.85);
            backdrop-filter: blur(4px);
            padding: 6px 12px;
            border-radius: 20px;
            z-index: 2;
            letter-spacing: 1px;
            border: 1px solid rgba(255, 0, 85, 0.3);
        }

        /* Conteneur pour le lecteur audio */
        .hud-audio-container {
            background: rgba(0, 0, 0, 0.25);
            border: 1px dashed var(--border-hud);
            padding: 12px;
            border-radius: var(--radius-sub);
            margin-top: 15px;
        }

        /* Léger ajustement du lecteur natif pour les navigateurs modernes */
        .hud-audio-container audio {
            height: 32px;
            outline: none;
        }

        /* Force le conteneur HUD à s'ouvrir */
        #container-prev-filename-settings[style*="display: block"] {
            display: block !important;
            height: auto !important;
            overflow: hidden !important;
            border-radius: 24px !important;
        }

        /* Force l'image à se rendre visible et à prendre sa taille */
        #prev-filename-settings {
            display: block !important;
            width: 100% !important;
            height: 100% !important;
            min-height: 200px !important;
            object-fit: cover !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Cartes d'options de Données */
        .data-card {
            background: var(--bg-card);
            border: 1px solid var(--border-hud);
            padding: 24px;
            border-radius: var(--radius-main);
            transition: var(--transition-smooth);
        }

        .data-card:hover {
            background: rgba(20, 26, 42, 0.6);
            border-color: rgba(66, 252, 241, 0.3);
        }

        .card-label {
            display: block;
            font-family: var(--font-hud);
            font-size: 0.65rem;
            color: var(--color-neon);
            letter-spacing: 1px;
            margin-bottom: 8px;
            opacity: 0.8;
        }

        .card-value {
            font-size: 1.2rem;
            font-weight: 500;
            color: var(--color-text-main);
        }

        /* Section d'information latérale style Briefing */
        .mission-briefing {
            border-left: 3px solid var(--color-neon);
            background: rgba(66, 252, 241, 0.02);
        }

        .panel-description {
            line-height: 1.7;
            color: var(--color-text-muted);
            margin: 0;
            font-size: 1rem;
        }

        /* ==========================================================================
   FORMULAIRES & INTEGRATION BOOTSTRAP FORM-SWITCH
   ========================================================================== */

        /* Personnalisation complète des switchs Bootstrap pour coller à l'univers HUD */
        .custom-hud-switch .form-check-input {
            background-color: rgba(255, 255, 255, 0.07);
            border-color: var(--border-hud);
            cursor: pointer;
            width: 3.2em;
            height: 1.6em;
            margin-left: 0;
            transition: var(--transition-smooth);
        }

        /* Aspect au survol du Switch */
        .custom-hud-switch .form-check-input:hover {
            border-color: rgba(66, 252, 241, 0.4);
        }

        /* Changement de couleur d'activation (Néon Blue/Neon) */
        .custom-hud-switch .form-check-input:checked {
            background-color: rgba(66, 252, 241, 0.2);
            border-color: var(--color-neon);
            /* SVG personnalisé pour modifier la couleur du rond interne du switch en noir sombre */
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='%2366fcf1' d='M8 4a4 4 0 100 8 4 4 0 000-8z'/%3e%3c/svg%3e");
            box-shadow: 0 0 15px rgba(66, 252, 241, 0.25);
        }

        .custom-hud-switch .form-check-input:focus {
            border-color: var(--color-neon);
            box-shadow: 0 0 10px rgba(66, 252, 241, 0.3);
        }

        /* ==========================================================================
   BOUTONS D'ACTION (BOUTON LIVE / SUBMIT)
   ========================================================================== */

        .btn-live {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            background: var(--color-accent);
            color: #ffffff;
            font-family: var(--font-hud);
            font-size: 0.85rem;
            padding: 18px;
            text-decoration: none;
            font-weight: bold;
            letter-spacing: 1px;
            border-radius: var(--radius-main);
            transition: var(--transition-smooth);
            box-shadow: 0 4px 15px rgba(255, 0, 85, 0.3);
            cursor: pointer;
        }

        .btn-live:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(255, 0, 85, 0.5);
            filter: brightness(1.1);
        }

        .btn-live:active {
            transform: translateY(0);
        }

        /* Cartes de sélection de mode de génération */
        .bg-dark-hud {
            background: rgba(0, 0, 0, 0.3);
            transition: var(--transition-smooth);
        }

        .generator-mode-card {
            border-radius: 12px;
            cursor: pointer;
            border: 1px solid rgba(255, 255, 255, 0.05) !important;
        }

        .generator-mode-card:hover {
            background: rgba(0, 242, 255, 0.05);
            border-color: rgba(0, 242, 255, 0.2) !important;
        }

        /* État actif de la carte sélectionnée */
        .generator-mode-card.active {
            background: rgba(0, 242, 255, 0.1);
            border-color: var(--neon-blue) !important;
            box-shadow: 0 0 15px rgba(0, 242, 255, 0.15);
        }

        .generator-mode-card.active i {
            color: var(--neon-blue) !important;
            filter: drop-shadow(0 0 5px var(--neon-blue));
        }

        /* Bouton secondaire d'action HUD */
        .btn-hud-action {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-hud);
            color: var(--color-neon);
            font-family: var(--font-hud);
            font-size: 0.8rem;
            padding: 14px;
            border-radius: var(--radius-sub);
            letter-spacing: 1px;
            transition: var(--transition-smooth);
            cursor: pointer;
        }

        .btn-hud-action:hover {
            background: rgba(66, 252, 241, 0.1);
            border-color: var(--color-neon);
            box-shadow: 0 0 15px rgba(66, 252, 241, 0.2);
            transform: translateY(-1px);
        }

        /* Structure de la grille de flux */
        .hud-feed-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-top: 15px;
        }

        @media (max-width: 576px) {
            .hud-feed-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Onglets Custom HUD */
        .hud-tabs .nav-link {
            background: transparent !important;
            color: var(--color-text-muted) !important;
            border: none !important;
            border-bottom: 2px solid transparent !important;
            font-family: var(--font-hud);
            font-size: 0.75rem;
            letter-spacing: 1px;
            padding: 10px 16px;
            transition: var(--transition-smooth);
        }

        .hud-tabs .nav-link.active {
            color: var(--neon-blue) !important;
            border-bottom-color: var(--neon-blue) !important;
            text-shadow: 0 0 8px rgba(0, 242, 255, 0.4);
        }

        /* Cartes des flux */
        .hud-feed-card {
            background: rgba(10, 14, 26, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            transition: var(--transition-smooth);
        }

        .hud-feed-card:hover {
            border-color: rgba(0, 242, 255, 0.3);
            transform: translateY(-2px);
        }

        .hud-feed-card.active {
            border-color: var(--neon-blue);
            box-shadow: 0 0 15px rgba(0, 242, 255, 0.2);
            background: rgba(0, 242, 255, 0.04);
        }

        /* Enveloppe de l'aperçu visuel */
        .feed-preview-wrapper {
            height: 90px;
            background: #000;
            position: relative;
            overflow: hidden;
        }

        .feed-preview-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.7;
            transition: var(--transition-smooth);
        }

        .hud-feed-card.active .feed-preview-wrapper img {
            opacity: 1;
        }

        /* Textes descriptifs des cartes */
        .feed-info {
            padding: 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.03);
        }

        .feed-title {
            display: block;
            font-size: 0.8rem;
            font-weight: bold;
            color: var(--color-text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .feed-tech {
            display: block;
            font-size: 0.55rem;
            color: var(--color-text-muted);
            font-family: var(--font-hud);
            margin-top: 2px;
        }

        /* ==========================================================================
   SIMULATIONS D'ANIMATIONS METEASTRO (MICRO-RENDUS CSS)
   ========================================================================== */

        .feed-animated-simulation {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0.6;
        }

        .hud-feed-card.active .feed-animated-simulation {
            opacity: 1;
        }

        /* Animation 1 : Aurore / Nébuleuse mobile */
        .feed-animated-simulation.aurora {
            background: radial-gradient(circle at 30% 30%, var(--neon-purple), transparent 60%),
                radial-gradient(circle at 70% 70%, var(--neon-blue), transparent 60%);
            background-size: 200% 200%;
            animation: hudAurora 6s ease infinite;
        }

        @keyframes hudAurora {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* Animation 2 : Grille Matrix numérique en mouvement */
        .feed-animated-simulation.matrix-grid {
            background-image: linear-gradient(rgba(0, 242, 255, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 242, 255, 0.1) 1px, transparent 1px);
            background-size: 10px 10px;
            animation: hudMatrixGrid 8s linear infinite;
        }

        @keyframes hudMatrixGrid {
            from {
                background-position: 0 0;
            }

            to {
                background-position: 0 100px;
            }
        }

        /* Animation 3 : Déplacement de poussière d'étoiles */
        .feed-animated-simulation.starfield {
            background: radial-gradient(white, rgba(255, 255, 255, .2) 2px, transparent 40px),
                radial-gradient(white, rgba(255, 255, 255, .15) 1px, transparent 30px);
            background-size: 80px 80px;
            background-position: 0 0, 40px 40px;
            animation: hudStarfield 12s linear infinite;
        }

        @keyframes hudStarfield {
            from {
                background-position: 0 0, 40px 40px;
            }

            to {
                background-position: 80px 80px, 120px 120px;
            }
        }

        /* Base commune pour les rendus statiques autogénérés */
        .feed-static-simulation {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            background: #050811;
        }

        /* Génération Statique 1 : Espace Profond (Nébuleuse figée) */
        .feed-static-simulation.deep-space {
            background: radial-gradient(circle at 80% 20%, rgba(0, 242, 255, 0.15), transparent 50%),
                radial-gradient(circle at 20% 80%, rgba(188, 19, 254, 0.15), transparent 50%),
                #070a13;
        }

        /* Génération Statique 2 : Supernova Étoilée */
        .feed-static-simulation.supernova-static {
            background: radial-gradient(circle at 50% 50%, rgba(255, 204, 0, 0.2), transparent 70%),
                radial-gradient(rgba(255, 255, 255, 0.4) 1px, transparent 1px),
                #050811;
            background-size: 100% 100%, 15px 15px;
        }

        /* Génération Statique 3 : Grille de calibration pure */
        .feed-static-simulation.hud-blueprint {
            background-image: linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 15px 15px;
            background-position: center;
            border: 1px solid rgba(0, 242, 255, 0.1);
        }

        /* --- ENGINE D'ONGLETS SANS JS --- */

        /* Masquage initial des deux volets de contenus */
        .tab-pane-php-custom {
            display: none;
        }

        /* 1. SÉLECTION DE L'ONGLET "ANIMÉ" */
        #php-tab-toggle-animated:checked~.nav-tabs .tab-trigger-animated {
            color: #fff !important;
            background-color: rgba(255, 255, 255, 0.08) !important;
            border-color: var(--bs-border-color) var(--bs-border-color) transparent !important;
        }

        /* Affiche le bloc de flux animés */
        #php-tab-toggle-animated:checked~.tab-content-php-custom .pane-animated-content {
            display: block !important;
        }

        /* 2. SÉLECTION DE L'ONGLET "STATIQUE" */
        #php-tab-toggle-static:checked~.nav-tabs .tab-trigger-static {
            color: #fff !important;
            background-color: rgba(255, 255, 255, 0.08) !important;
            border-color: var(--bs-border-color) var(--bs-border-color) transparent !important;
        }

        /* Affiche le bloc de flux statiques */
        #php-tab-toggle-static:checked~.tab-content-php-custom .pane-static-content {
            display: block !important;
        }

        /* --- SELECTION DES CARTES INTERNES --- */
        .hud-feed-card-label input[type="radio"]:checked+.hud-feed-card {
            border-color: var(--color-neon, #00f2ff) !important;
            box-shadow: 0 0 12px rgba(0, 242, 255, 0.25);
            background: rgba(0, 242, 255, 0.04);
        }

        /* --- Le Panneau en Plein Écran --- */
        .side-panel {
            position: fixed;
            top: 0;
            right: 0;
            width: 100vw;
            /* Prend TOUTE la largeur de la page */
            height: 100vh;
            /* Prend TOUTE la hauteur de la page */
            background-color: #111;
            /* Ton fond de matrice sombre */
            z-index: 1050;
            /* Reste au-dessus de tout le reste */

            /* Structure interne pour bien organiser tes colonnes (gauche / droite) */
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            /* Permet de scroller verticalement si le contenu dépasse */

            /* Animation de glissement depuis la droite */
            transform: translateX(100%);
            /* Caché complètement à droite */
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            /* Transition ultra-fluide */
        }

        /* État ouvert : le panneau occupe tout l'écran */
        .side-panel.open {
            transform: translateX(0);
        }

        /* --- L'overlay (Optionnel en plein écran, mais utile pour le fondu au noir) --- */
        .panel-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: #000000;
            /* Noir total pour la transition */
            z-index: 1040;

            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s ease;
        }

        .panel-overlay.show {
            opacity: 0.7;
            /* Légère transparence pour laisser deviner le fond au début */
            pointer-events: auto;
        }
    </style>

    <?php include "divers/contenu.php"; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/js/popup.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <script>
        $(document).ready(function () {
            // Configuration commune Summernote
            const summernoteConfig = {
                height: 250,
                lang: 'fr-FR',
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            };

            $('#contenu-astronomie').summernote({
                summernoteConfig,
                placeholder: '<b>🔭 Partagez vos découvertes stellaires...</b><br>Détaillez vos observations, matériel utilisé, etc.'
            });

            $('#contenu-meteorologie').summernote({
                summernoteConfig,
                placeholder: '<b>☁️ Rapport météorologique...</b><br>Décrivez les phénomènes observés, températures, pressions...'
            });
        });

        $(document).ready(function () {
            // Initialiser Summernote pour l'édition
            $('#edit-summernote').summernote({
                height: 400,
                lang: 'fr-FR'
            });

            // Quand on clique sur le bouton modifier de la liste
            $('.btn-edit-trigger').on('click', function (e) {
                e.preventDefault();

                // 1. Récupérer les infos (on peut aussi les chercher via AJAX)
                const id = $(this).data('id');
                const type = $(this).data('type');
                const title = $(this).data('title_contenu'); // Sert pour :title et :title_c initialement
                const content = $(this).closest('.content-item').find('.hidden-content').html(); // Si stocké caché

                // 2. Remplir le formulaire
                $('#edit-id').val(id);
                $('#edit-type').val(type);
                $('#edit-category').val(title); // :title
                $('#edit-title-c').val(title_contenu);  // :title_c
                $('#edit-summernote').summernote('code', content); // :contenu

                // 3. Lancer l'animation "Nouvelle Page"
                $('#edit-overlay-page').addClass('active');
                $('body').addClass('modal-open');
            });

            // Retour en arrière
            $('#close-edit').on('click', function () {
                $('#edit-overlay-page').removeClass('active');
                $('body').removeClass('modal-open');
            });
        });

        $('.btn-edit-trigger').on('click', function () {
            const btn = $(this);
            const type = btn.data('type'); // 'astronomie' ou 'météorologie'
            const uploadPath = "/uploads/";

            // 1. Mise à jour visuelle du type
            const badge = $('#edit-type-badge');
            if (type === 'astronomie') {
                badge.text('Secteur Astronomie')
                    .css({ 'background-color': '#3b82f6', 'color': '#fff' }) // Bleu pour l'espace
                    .html('<i class="fa-solid fa-user-astronaut me-2"></i> Astronomie');
            } else {
                badge.text('Secteur Météorologie')
                    .css({ 'background-color': '#ef4444', 'color': '#fff' }) // Rouge/Orange pour la météo
                    .html('<i class="fa-solid fa-cloud-bolt me-2"></i> Météorologie');
            }

            // 2. Remplissage des champs techniques
            $('#edit-id').val(btn.data('id'));
            $('#edit-type').val(type); // Crucial pour le update_logic.php
            $('#edit-title-c').val(btn.data('title-c'));
            $('#edit-summernote').summernote('code', btn.data('contenu'));

            // 3. Gestion des images et médias (Chemin unique /uploads/)
            if (btn.data('filename')) {
                $('#prev-filename').attr('src', uploadPath + btn.data('filename')).show();
            } else { $('#prev-filename').hide(); }

            if (btn.data('bg')) {
                $('#prev-bg').attr('src', uploadPath + btn.data('bg')).show();
            } else { $('#prev-bg').hide(); }

            // Galerie
            $('#prev-gallery').empty();
            if (btn.data('gallery')) {
                const imgs = btn.data('gallery').toString().split(',');
                imgs.forEach(img => {
                    if (img.trim() !== "") {
                        $('#prev-gallery').append(`<img src="${uploadPath}${img}" class="rounded border" style="width:50px; height:50px; object-fit:cover;">`);
                    }
                });
            }

            // Musique
            $('#container-prev-music').empty();
            if (btn.data('music')) {
                $('#container-prev-music').html(`
            <div class="p-2 rounded bg-dark border border-secondary">
                <small class="text-white-50 d-block mb-1">${btn.data('music')}</small>
                <audio controls class="w-100" style="height: 30px;">
                    <source src="${uploadPath}${btn.data('music')}" type="audio/mpeg">
                </audio>
            </div>`);
            }

            // Lancement du slide
            $('#edit-overlay-page').addClass('active');
            $('body').addClass('modal-open');
        });

        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('btn-submit-edit');
            if (!btn) return;

            const form = btn.closest('form');
            const fill = btn.querySelector('.progress-fill');
            const label = btn.querySelector('.btn-label');
            const icon = btn.querySelector('i');

            btn.addEventListener('click', function (e) {
                e.preventDefault(); // Empêche l'envoi classique

                const formData = new FormData(form);
                const xhr = new XMLHttpRequest();

                // 1. Début de l'envoi
                xhr.upload.addEventListener("progress", function (e) {
                    if (e.lengthComputable) {
                        btn.disabled = true; // Désactive le bouton
                        const percent = Math.round((e.loaded / e.total) * 100);

                        // Mise à jour visuelle
                        fill.style.width = percent + "%";
                        label.textContent = `Transmission : ${percent}%`;
                        icon.className = "fas fa-spinner fa-spin me-3"; // Change l'icône
                    }
                });

                // 2. Fin de l'envoi
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        fill.style.background = "#22c55e"; // Passe au vert
                        label.textContent = "Données transmises !";
                        icon.className = "fas fa-check-circle me-3";

                        // Redirection ou rafraîchissement après succès
                        setTimeout(() => {
                            window.location.reload();
                        }, 1200);
                    } else {
                        alert("Échec de la transmission cosmique.");
                        resetBtn();
                    }
                };

                // 3. Gestion d'erreur
                xhr.onerror = function () {
                    alert("Erreur réseau.");
                    resetBtn();
                };

                function resetBtn() {
                    btn.disabled = false;
                    fill.style.width = "0%";
                    label.textContent = "Transmettre les données";
                    icon.className = "fas fa-satellite-dish me-3";
                }

                xhr.open("POST", ""); // Envoie vers la même page (ton PHP de traitement)
                xhr.send(formData);
            });
        });
    </script>
    <script>
        // GESTION DU SLIDE VIA URL
        function handleNavigation() {
            const overlay = document.getElementById('settings-overlay');
            if (window.location.hash === '#parametres') {
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden'; // Bloque le scroll arrière
            } else {
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        window.addEventListener('hashchange', handleNavigation);
        window.addEventListener('load', handleNavigation);

        // Protection Images
        document.addEventListener('contextmenu', e => { if (e.target.tagName === 'IMG') e.preventDefault(); });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- CACHE DU DOM ---
            const DOM = {
                sidebar: document.getElementById('settingsSidebar'),
                overlay: document.getElementById('settings-overlay'),
                closeBtn: document.getElementById('btn-close-settings'),
                btnGenerate: document.getElementById('btn-trigger-generation'),

                // Formulaire & Inputs cachés
                itemId: document.getElementById('settings-item-id'),
                itemType: document.getElementById('settings-item-type'),
                itemTitle: document.getElementById('settings-item-title'),
                inputFeed: document.getElementById('selected-hud-feed'),
                inputModeGlobal: document.getElementById('bg-generation-mode'),

                // Contextes d'affichage
                systemModeText: document.getElementById('settings-system-mode'),
                statusAstro: document.getElementById('hud-status-astro'),
                statusMeteo: document.getElementById('hud-status-meteo'),
                optionsAstro: document.getElementById('hud-options-astro'),
                optionsMeteo: document.getElementById('hud-options-meteo'),

                // Médias (Images & Audio)
                previewImg: document.getElementById('prev-filename-settings'),
                containerImg: document.getElementById('container-prev-filename-settings'),
                previewMusic: document.getElementById('prev-music-settings'),
                containerMusic: document.getElementById('container-prev-music-settings'),

                // Interrupteurs (Switches)
                switchMusic: document.getElementById('switch-enable-music'),
                switchImages: document.getElementById('switch-show-images'),
                switchMeteo: document.getElementById('switch-meteo-widgets')
            };

            // --- ENCAPSULATION DE LA LOGIQUE MÉDIA ---
            const MediaManager = {
                // Nettoyage des valeurs textuelles provenant de la BDD / Attributs data
                cleanPath(value) {
                    if (!value) return null;
                    const cleaned = String(value).trim();
                    const invalid = ['', '0', '/', 'undefined', 'null'];
                    return invalid.includes(cleaned.toLowerCase()) ? null : cleaned;
                },

                // Formate le chemin vers le dossier racine 'uploads/'
                formatUrl(path) {
                    if (!path) return '';
                    return path.startsWith('uploads/') ? `/${path}` : `/uploads/${path}`;
                },

                // Gestion de l'affichage et de la lecture audio
                updateAudio(isEnabled, filename) {
                    if (!DOM.previewMusic || !DOM.containerMusic) return;

                    const cleanFile = this.cleanPath(filename);

                    if (!isEnabled || !cleanFile) {
                        DOM.previewMusic.pause();
                        DOM.previewMusic.removeAttribute('src');
                        DOM.previewMusic.load();
                        DOM.containerMusic.style.display = 'none';
                    } else {
                        DOM.previewMusic.src = this.formatUrl(cleanFile);
                        DOM.previewMusic.load();
                        DOM.containerMusic.style.display = 'block';
                    }
                },

                // Gestion du rendu de la couverture d'image
                updateImage(filename) {
                    if (!DOM.previewImg || !DOM.containerImg) return;

                    const cleanFile = this.cleanPath(filename);

                    if (cleanFile) {
                        DOM.previewImg.src = `${this.formatUrl(cleanFile)}?t=${Date.now()}`;
                        DOM.containerImg.style.display = 'block';
                    } else {
                        DOM.previewImg.removeAttribute('src');
                        DOM.containerImg.style.display = 'none';
                    }
                }
            };

            // --- ACTIONS DE COMMUTATION ET VISIBILITÉ (SIDEBAR) ---
            const Panel = {
                open() {
                    DOM.sidebar?.classList.add('open');
                    DOM.overlay?.classList.add('open');
                    document.body.classList.add('panel-active');
                },
                close() {
                    DOM.sidebar?.classList.remove('open');
                    DOM.overlay?.classList.remove('open');
                    document.body.classList.remove('panel-active');
                    MediaManager.updateAudio(false, null); // Coupe le flux sonore à la fermeture
                }
            };

            // Attribution des écouteurs de fermeture
            DOM.closeBtn?.addEventListener('click', Panel.close);
            DOM.overlay?.addEventListener('click', Panel.close);


            // --- GESTION DES INTEGRATIONS DE FLUX (ONGLETS & GRID CARDS) ---
            document.querySelectorAll('.hud-feed-card').forEach(card => {
                card.addEventListener('click', function () {
                    document.querySelectorAll('.hud-feed-card').forEach(c => c.classList.remove('active'));
                    this.classList.add('active');

                    if (DOM.inputFeed) DOM.inputFeed.value = this.getAttribute('data-feed-id') || '';
                    if (DOM.inputModeGlobal) DOM.inputModeGlobal.value = this.getAttribute('data-mode') || 'animated';
                });
            });

            document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tabBtn => {
                tabBtn.addEventListener('shown.bs.tab', (e) => {
                    if (DOM.inputModeGlobal) {
                        DOM.inputModeGlobal.value = e.target.id === 'tab-static' ? 'static' : 'animated';
                    }
                });
            });


            // --- INTERACTIONS SENSORIELLES EN DIRECT (FORMULAIRE) ---
            DOM.switchMusic?.addEventListener('change', function () {
                const activeFile = this.getAttribute('data-active-file');
                MediaManager.updateAudio(this.checked, activeFile);
            });


            // --- POINT D'ENTRÉE UNIQUE : DECLENCHEMENT ET INJECTION ---
            document.querySelectorAll('.btn-settings-trigger').forEach(button => {
                button.addEventListener('click', function () {

                    // Récupération brute des attributs textuels du DOM
                    const rawShowImages = this.getAttribute('data-show-images');
                    const rawEnableMusic = this.getAttribute('data-enable-music');
                    const rawMeteoWidgets = this.getAttribute('data-meteo-widgets');

                    // 1. CORRECTION IMPÉRATIVE : Évaluation stricte pour obtenir des booléens natifs
                    const data = {
                        id: this.getAttribute('data-id') || '',
                        title: this.getAttribute('data-title-c') || 'Aucun titre',
                        type: (this.getAttribute('data-type') || '').toLowerCase().trim(),
                        showImages: (rawShowImages === '1' || rawShowImages === 'true' || rawShowImages === true),
                        enableMusic: (rawEnableMusic === '1' || rawEnableMusic === 'true' || rawEnableMusic === true),
                        bgMode: this.getAttribute('data-bg-mode') || 'animated',
                        feedId: this.getAttribute('data-feed-id') || '',
                        meteoWidgets: (rawMeteoWidgets === '1' || rawMeteoWidgets === 'true' || rawMeteoWidgets === true),
                        filename: this.getAttribute('data-filename'),
                        musicFile: this.getAttribute('data-music-file')
                    };

                    // Debug de contrôle en console
                    console.log(`[MATRICE HUD] ID: ${data.id} | Branche: ${data.type}`);
                    console.log(`[MATRICE DIAGNOSTIC] Images: ${data.showImages} (Brut: "${rawShowImages}") | Musique: ${data.enableMusic} (Brut: "${rawEnableMusic}")`);

                    // 2. Hydratation des informations système prioritaires
                    if (DOM.itemId) DOM.itemId.value = data.id;
                    if (DOM.itemType) DOM.itemType.value = data.type;
                    if (DOM.itemTitle) DOM.itemTitle.textContent = data.title;

                    // Stockage persistant du fichier sur le noeud du switch musique
                    DOM.switchMusic?.setAttribute('data-active-file', MediaManager.cleanPath(data.musicFile) || '');

                    // 3. Masquage global préventif des layouts conditionnels
                    [DOM.statusAstro, DOM.statusMeteo, DOM.optionsAstro, DOM.optionsMeteo].forEach(el => {
                        if (el) el.style.display = 'none';
                    });

                    // 4. Routage algorithmique par Type de module
                    if (data.type === 'astronomie' || data.type === 'astronomy') {
                        if (DOM.systemModeText) DOM.systemModeText.textContent = '// SYSTEM_MODE_ASTRONOMY_ACTIVE';
                        if (DOM.statusAstro) DOM.statusAstro.style.display = 'block';
                        if (DOM.optionsAstro) DOM.optionsAstro.style.display = 'block';

                        if (DOM.inputModeGlobal) DOM.inputModeGlobal.value = data.bgMode;
                        if (DOM.inputFeed) DOM.inputFeed.value = data.feedId;

                        // Sélection graphique de la carte de flux correspondante
                        document.querySelectorAll('.hud-feed-card').forEach(card => {
                            const cardFeedId = card.getAttribute('data-feed-id');
                            card.classList.toggle('active', cardFeedId === data.feedId && data.feedId !== '');
                        });

                        // Bascule automatique vers l'onglet Bootstrap requis (Statique ou Animé)
                        const targetTabSelector = data.bgMode === 'static' ? '#tab-static' : '#tab-animated';
                        const tabTrigger = document.querySelector(targetTabSelector);
                        if (tabTrigger) {
                            bootstrap.Tab.getOrCreateInstance(tabTrigger).show();
                        }

                    } else if (data.type === 'meteorologie' || data.type === 'météorologie' || data.type === 'meteorology') {
                        if (DOM.systemModeText) DOM.systemModeText.textContent = '// SYSTEM_MODE_METEOROLOGY_ACTIVE';
                        if (DOM.statusMeteo) DOM.statusMeteo.style.display = 'block';
                        if (DOM.optionsMeteo) DOM.optionsMeteo.style.display = 'block';
                        if (DOM.switchMeteo) DOM.switchMeteo.checked = data.meteoWidgets;
                    } else {
                        if (DOM.systemModeText) DOM.systemModeText.textContent = '// SYSTEM_CORE_CONNECTED';
                    }

                    // 5. Initialisation synchrone des commutateurs physiques (DOM)
                    if (DOM.switchImages) DOM.switchImages.checked = data.showImages;
                    if (DOM.switchMusic) DOM.switchMusic.checked = data.enableMusic;

                    MediaManager.updateImage(data.filename);
                    MediaManager.updateAudio(data.enableMusic, data.musicFile);

                    // 6. Déploiement visuel de l'interface utilisateur
                    Panel.open();
                });
            });

            // --- 5. ACTION EXÉCUTIVE : SIMULATION FORCE SYNCHRONISATION ---
            DOM.btnGenerate?.addEventListener('click', function () {
                const initialHTML = this.innerHTML;

                this.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-2"></i> Calcul des coordonnées stellaires...`;
                this.style.opacity = "0.7";
                this.style.pointerEvents = "none";

                setTimeout(() => {
                    this.innerHTML = `<i class="fa-solid fa-check me-2" style="color: var(--color-neon);"></i> Matrice synchronisée !`;
                    this.style.opacity = "1";

                    setTimeout(() => {
                        this.innerHTML = initialHTML;
                        this.style.pointerEvents = "auto";
                    }, 2000);

                    // Rafraîchissement forcé du cache de l'image d'aperçu
                    if (DOM.previewImg && DOM.previewImg.hasAttribute('src')) {
                        const cleanSrc = DOM.previewImg.src.split('?')[0];
                        if (cleanSrc && !cleanSrc.endsWith('/uploads/') && !cleanSrc.endsWith('/0')) {
                            DOM.previewImg.src = `${cleanSrc}?t=${Date.now()}`;
                        }
                    }
                }, 1500);
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('settingsSidebar');
            const overlay = document.getElementById('settings-overlay');
            const closeBtn = document.querySelector('.close-btn');

            // 1. OUVERTURE PLEIN ÉCRAN FLUIDE
            // On vérifie l'attribut de données au lieu de manipuler une classe existante
            if (sidebar && sidebar.getAttribute('data-should-open') === 'true') {
                // Un seul requestAnimationFrame suffit puisque le panneau n'était pas encore affiché
                requestAnimationFrame(() => {
                    sidebar.classList.add('open');
                    if (overlay) overlay.classList.add('show');
                    document.body.style.overflow = 'hidden'; // Bloque le scroll
                });
            }

            // 2. FERMETURE FLUIDE
            function closePanel(e) {
                e.preventDefault();

                sidebar.classList.remove('open');
                if (overlay) overlay.classList.remove('show');
                document.body.style.overflow = ''; // Libère le scroll

                // Attend la fin du glissement (400ms) avant de changer d'URL
                setTimeout(() => {
                    window.location.href = closeBtn.getAttribute('href');
                }, 400);
            }

            if (closeBtn) closeBtn.addEventListener('click', closePanel);
            if (overlay) overlay.addEventListener('click', closePanel);
        });
    </script>
</body>

</html>