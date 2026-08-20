<?php

namespace App\Controller;

use App\Entity\Meteorologie;
use App\Entity\MenuConnect;
use App\Entity\MenuPrincipal;
use Doctrine\ORM\EntityManager;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class MeteorologieController extends AbstractController
{
    public function __construct(private EntityManager $entityManager)
    {
    }

    public function index(): void
    {
        $this->startSession();
        $isConnected = isset($_SESSION['email'], $_SESSION['password']);
        $menuItems = $this->entityManager
            ->getRepository($isConnected ? MenuConnect::class : MenuPrincipal::class)
            ->findBy([], ['parent' => 'ASC', 'id' => 'ASC']);

        ob_start();
        $this->renderBootstrapMenu($menuItems);
        $menuHtml = ob_get_clean();

        $articles = $this->entityManager->getRepository(Meteorologie::class)->findBy(
            ['verified' => true],
            ['dateMeteorologie' => 'DESC']
        );

        $this->addUniqueIP();
        $nombreVisite = $this->getUniqueVisitor();
        $this->addUniqueIPMonthly();

        $this->render('meteorologie/index', [
            'nombreVisite' => $nombreVisite,
            'menuHtml' => $menuHtml,
            'isConnected' => $isConnected,
            'userName' => $isConnected ? $_SESSION['name'] : '',
            'userEmail' => $isConnected ? $_SESSION['email'] : '',
            'articlesMeteorologie' => $articles,
            'posts' => array_map([$this, 'toArray'], $articles),
            'jsonPlaylist' => json_encode($this->playlist($articles), JSON_UNESCAPED_SLASHES)
        ]);
    }

    public function show(?int $id = null): void
    {
        $this->startSession();
        $id ??= filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $article = $id ? $this->entityManager->getRepository(Meteorologie::class)->find($id) : null;

        if (!$article || !$article->getVerified()) {
            $this->renderError(404);
        }

        $this->addUniqueIP();
        $nombreVisite = $this->getUniqueVisitor();
        $this->addUniqueIPMonthly();

        $isConnected = isset($_SESSION['email'], $_SESSION['password']);
        $menuItems = $this->entityManager
            ->getRepository($isConnected ? MenuConnect::class : MenuPrincipal::class)
            ->findBy([], ['parent' => 'ASC', 'id' => 'ASC']);

        ob_start();
        $this->renderBootstrapMenu($menuItems);
        $menuHtml = ob_get_clean();

        $this->render('meteorologie/contenu', [
            'nombreVisite' => $nombreVisite,
            'menuHtml' => $menuHtml,
            'isConnected' => $isConnected,
            'userName' => $isConnected ? $_SESSION['name'] : '',
            'userEmail' => $isConnected ? $_SESSION['email'] : '',
            'article' => $this->toArray($article),
            'jsonPlaylist' => json_encode($this->playlist([$article]), JSON_UNESCAPED_SLASHES)
        ]);
    }

    private function toArray(Meteorologie $article): array
    {
        return [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'title_contenu' => $article->getTitleContenu(),
            'contenu' => $article->getContenu(),
            'filename' => $article->getFilename(),
            'background_img' => $article->getBackgroundImg(),
            'gallery_images' => $article->getGalleryImages(),
            'show_images' => $article->getShowImages(),
            'date_meteorologie' => $article->getDateMeteorologie(),
            'name' => $article->getUser()->getName()
        ];
    }

    private function playlist(array $articles): array
    {
        $playlist = [];
        foreach ($articles as $article) {
            foreach (explode(',', (string) $article->getMusicFile()) as $file) {
                $file = trim($file);
                if ($file !== '' && !in_array($file, $playlist, true)) {
                    $playlist[] = '/uploads/' . $file;
                }
            }
        }
        return $playlist;
    }

    private function startSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function renderBootstrapMenu(array $items, int $parentId = 0): void
    {
        foreach ($items as $item) {
            if ($item->getParent() != $parentId) {
                continue;
            }

            $hasChildren = false;
            foreach ($items as $child) {
                if ($child->getParent() == $item->getId()) {
                    $hasChildren = true;
                    break;
                }
            }

            $class = $item->getClass() ?? '';
            $name = htmlspecialchars(ucfirst($item->getMenuName()), ENT_QUOTES, 'UTF-8');
            $url = htmlspecialchars($item->getUrl(), ENT_QUOTES, 'UTF-8');

            if ($hasChildren) {
                echo '<li class="nav-item dropdown">';
                echo '<a class="nav-link dropdown-toggle ' . $class . '" href="' . $url . '" role="button" data-bs-toggle="dropdown" aria-expanded="false">' . $name . '</a>';
                echo '<ul class="dropdown-menu shadow border-0 animate slideIn">';
                $this->renderBootstrapMenu($items, $item->getId());
                echo '</ul></li>';
            } else {
                $linkClass = $parentId === 0 ? 'nav-link' : 'dropdown-item';
                echo '<li><a class="' . $linkClass . ' ' . $class . '" href="' . $url . '">' . $name . '</a></li>';
            }
        }
    }

    public function contactProcess(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendJsonResponse('error', 'Méthode non autorisée.');
        }

        $pseudo = filter_input(INPUT_POST, 'pseudo', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);

        if (!$pseudo || !$email || !$message) {
            $this->sendJsonResponse('error', 'Données invalides ou email mal formé.');
        }

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'dvmta39@gmail.com';
            $mail->Password = $_ENV['SMTP_PASS'] ?? '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';
            $mail->setFrom($mail->Username, 'Meteastro - Station de Contrôle');
            $mail->addAddress($mail->Username);
            $mail->addReplyTo($email, $pseudo);
            $mail->isHTML(true);
            $mail->Subject = '[METEASTRO] Transmission de ' . $pseudo;
            $mail->Body = '<h2>Signal capté de : ' . htmlspecialchars($pseudo, ENT_QUOTES, 'UTF-8') . '</h2><p>' . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . '</p><p>Source : ' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '</p>';
            $mail->AltBody = "Message de {$pseudo} ({$email}) :\n\n{$message}";
            $mail->send();
            $this->sendJsonResponse('success', 'Signal propulsé avec succès vers Meteastro !');
        } catch (Exception $exception) {
            error_log('PHPMailer Error: ' . $mail->ErrorInfo);
            $this->sendJsonResponse('error', 'Le signal a été dévié par une anomalie.');
        }
    }

    private function sendJsonResponse(string $status, string $message): void
    {
        if (ob_get_length()) {
            ob_clean();
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => $status,
            'message' => $message,
            'timestamp' => time()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function addUniqueIP(?string $ip = null): void
    {
        $ip = trim($ip ?? $this->getIP() ?? '');
        if ($ip === '') {
            return;
        }

        $file = __DIR__ . '/../../iplist.txt';
        $content = is_file($file) ? (string) file_get_contents($file) : '';
        $ips = array_filter(array_map('trim', explode(',', $content)));
        if (!in_array($ip, $ips, true)) {
            file_put_contents($file, ($content === '' ? '' : ',') . $ip, FILE_APPEND | LOCK_EX);
        }
    }

    private function getUniqueVisitor(): int
    {
        $file = __DIR__ . '/../../iplist.txt';
        if (!is_file($file)) {
            return 0;
        }

        return count(array_filter(array_map('trim', explode(',', (string) file_get_contents($file)))));
    }

    private function getIP(): ?string
    {
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
            $value = $_SERVER[$key] ?? '';
            $ip = trim(explode(',', $value)[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
        return null;
    }

    private function addUniqueIPMonthly(?string $ip = null): void
    {
        $ip = trim($ip ?? $this->getIP() ?? '');
        if ($ip === '') {
            return;
        }

        $file = __DIR__ . '/../../iplist_date.txt';
        $content = is_file($file) ? (string) file_get_contents($file) : '';
        $record = date('Y-m') . '-' . $ip;
        if (!in_array($record, array_filter(array_map('trim', explode("\n", $content))), true)) {
            file_put_contents($file, $record . "\n", FILE_APPEND | LOCK_EX);
        }
    }

    private function getUniqueVisitorMonthly(): int
    {
        $file = __DIR__ . '/../../iplist_date.txt';
        if (!is_file($file)) {
            return 0;
        }

        $prefix = date('Y-m');
        $lines = array_map('trim', explode("\n", (string) file_get_contents($file)));
        return count(array_filter($lines, static fn(string $line): bool => str_starts_with($line, $prefix)));
    }
}
