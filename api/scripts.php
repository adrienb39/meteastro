<?php
session_start();

// Récupération du token envoyé (en POST ou en GET)
$tokenRecu = $_POST['token'] ?? $_GET['token'] ?? null;

// Vérification de la validité du token
if (!$tokenRecu || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $tokenRecu)) {
    http_response_code(403);
    die('Accès refusé : token invalide ou expiré.');
}

// Token valide pour l'accès à la page : on en régénère un nouveau,
// dédié aux appels AJAX qui vont suivre depuis cette page (au lieu
// de le détruire, ce qui bloquait tout clic sur les boutons).
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$ajaxToken = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centre de Contrôle - MétéAstro</title>

    <!-- Bootstrap 5 CSS & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 1.5rem;
        }

        .glass-card {
            position: relative;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 28px;
            padding: 2.5rem;
            max-width: 520px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .back-link {
            position: absolute;
            top: 1.5rem;
            left: 1.5rem;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #cbd5e1;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .back-link:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            transform: translateX(-2px);
        }

        .icon-box {
            width: 75px;
            height: 75px;
            margin: 0 auto 1.5rem;
            background: rgba(13, 202, 240, 0.1);
            border: 1px solid rgba(13, 202, 240, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0dcaf0;
            font-size: 2rem;
        }

        .btn-script {
            border: none;
            color: #ffffff;
            font-weight: 600;
            padding: 0.9rem 1.4rem;
            border-radius: 14px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .btn-script:hover:not(:disabled) {
            transform: translateY(-2px);
            color: #fff;
        }

        .btn-script:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-cyan {
            background: linear-gradient(135deg, #0dcaf0 0%, #0d6efd 100%);
            box-shadow: 0 4px 15px rgba(13, 202, 240, 0.25);
        }

        .btn-cyan:hover:not(:disabled) {
            box-shadow: 0 8px 25px rgba(13, 202, 240, 0.45);
        }

        .btn-purple {
            background: linear-gradient(135deg, #6f42c1 0%, #0d6efd 100%);
            box-shadow: 0 4px 15px rgba(111, 66, 193, 0.25);
        }

        .btn-purple:hover:not(:disabled) {
            box-shadow: 0 8px 25px rgba(111, 66, 193, 0.45);
        }

        .btn-emerald {
            background: linear-gradient(135deg, #198754 0%, #20c997 100%);
            box-shadow: 0 4px 15px rgba(32, 201, 151, 0.25);
        }

        .btn-emerald:hover:not(:disabled) {
            box-shadow: 0 8px 25px rgba(32, 201, 151, 0.45);
        }

        .output-box {
            display: none;
            margin-top: 1.5rem;
            padding: 1rem 1.25rem;
            border-radius: 14px;
            font-size: 0.9rem;
            text-align: left;
            word-break: break-word;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .output-success {
            background: rgba(25, 135, 84, 0.15);
            border: 1px solid rgba(25, 135, 84, 0.4);
            color: #2ecc71;
        }

        .output-error {
            background: rgba(220, 53, 69, 0.15);
            border: 1px solid rgba(220, 53, 69, 0.4);
            color: #ff6b6b;
        }

        .log-box {
            display: none;
            margin-top: 1.5rem;
            padding: 1rem 1.25rem;
            border-radius: 14px;
            font-size: 0.78rem;
            text-align: left;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
            max-height: 320px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-word;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>

<body>

    <div class="glass-card">
        <a href="#" id="backLink" class="back-link" title="Retour" aria-label="Retour">
            <i class="fa-solid fa-arrow-left"></i>
        </a>

        <div class="icon-box">
            <i class="fa-solid fa-sliders"></i>
        </div>

        <h3 class="text-white fw-bold mb-2">Centre d'Exécution</h3>
        <p class="text-white-50 small mb-4">Sélectionnez une action à exécuter en arrière-plan.</p>

        <div class="d-grid gap-3">

            <!-- Bouton 1 -->
            <button class="btn btn-script btn-cyan" data-script="send-announcement-v250.php" data-icon="fa-bullhorn">
                <span class="btn-text">Envoyer les annonces (v2.5)</span>
                <i class="fa-solid fa-bullhorn btn-icon"></i>
            </button>

            <!-- Bouton 2 -->
            <button class="btn btn-script btn-purple" data-script="get-latest-version.php" data-icon="fa-cloud-moon">
                <span class="btn-text">Vérifier la dernière version en envoyant la version</span>
                <i class="fa-solid fa-cloud-moon btn-icon"></i>
            </button>

            <!-- Bouton 3 -->
            <button class="btn btn-script btn-emerald" data-script="view-logs.php" data-icon="fa-file-lines"
                data-mode="logs">
                <span class="btn-text">Afficher les logs</span>
                <i class="fa-solid fa-file-lines btn-icon"></i>
            </button>

        </div>

        <div id="outputBox" class="output-box"></div>
    </div>

    <script>
        // Retour à la page précédente ; si aucun historique (arrivée directe sur
        // cette page), on retombe sur une page d'accueil par défaut.
        document.getElementById('backLink').addEventListener('click', function (e) {
            e.preventDefault();
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = '/';
            }
        });

        // Token dédié aux appels AJAX de cette page (régénéré à chaque chargement).
        const csrfToken = <?= json_encode($ajaxToken) ?>;

        document.querySelectorAll('.btn-script').forEach(button => {
            button.addEventListener('click', async function () {
                const scriptUrl = this.getAttribute('data-script');
                const originalIcon = this.getAttribute('data-icon');
                const mode = this.getAttribute('data-mode'); // 'logs' ou null
                const btnText = this.querySelector('.btn-text');
                const btnIcon = this.querySelector('.btn-icon');
                const outputBox = document.getElementById('outputBox');
                const allButtons = document.querySelectorAll('.btn-script');

                allButtons.forEach(btn => btn.disabled = true);

                const originalText = btnText.textContent;
                btnText.textContent = "Exécution en cours...";
                btnIcon.className = "fa-solid fa-circle-notch fa-spin btn-icon";
                outputBox.style.display = "none";

                try {
                    const response = await fetch(scriptUrl, {
                        headers: { 'X-CSRF-Token': csrfToken }
                    });

                    if (!response.ok) {
                        throw new Error(`Erreur HTTP : ${response.status}`);
                    }

                    if (mode === 'logs') {
                        const data = await response.json();

                        outputBox.className = "log-box";
                        if (!data.exists || data.lines.length === 0) {
                            outputBox.textContent = data.message || 'Aucun log disponible.';
                        } else {
                            outputBox.textContent = data.lines.join('\n');
                            outputBox.scrollTop = outputBox.scrollHeight;
                        }
                        outputBox.style.display = "block";

                    } else {
                        const data = await response.text();
                        outputBox.className = "output-box output-success";
                        outputBox.innerHTML = `<i class="fa-solid fa-circle-check me-2"></i> <strong>[${scriptUrl}]</strong> : ${data || 'Exécuté avec succès.'}`;
                        outputBox.style.display = "block";
                    }

                } catch (error) {
                    outputBox.className = "output-box output-error";
                    outputBox.innerHTML = `<i class="fa-solid fa-triangle-exclamation me-2"></i> <strong>[${scriptUrl}]</strong> : ${error.message}`;
                    outputBox.style.display = "block";

                } finally {
                    allButtons.forEach(btn => btn.disabled = false);
                    btnText.textContent = originalText;
                    btnIcon.className = `fa-solid ${originalIcon} btn-icon`;
                }
            });
        });
    </script>

</body>

</html>