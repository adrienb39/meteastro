<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManager;

class NewsletterController extends AbstractController
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function welcome(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;

        if ($userId) {
            /** @var User|null $user */
            $user = $this->entityManager->getRepository(User::class)->find($userId);
            $newsletterStatus = $user ? $user->getNewsletter() : null;

            // Si déjà défini à 0 ou 1, pas besoin d'être ici -> redirection
            if ($newsletterStatus === 0 || $newsletterStatus === 1) {
                $this->redirect('/');
                exit();
            }
        }

        if ($userId && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $newsletterChoice = (isset($_POST['accept_newsletter']) && $_POST['accept_newsletter'] === '1') ? 1 : 0;

            /** @var User|null $user */
            $user = $this->entityManager->getRepository(User::class)->find($userId);
            if ($user) {
                $user->setNewsletter($newsletterChoice);
                $this->entityManager->flush();
            }

            $_SESSION['newsletter'] = $newsletterChoice;

            $this->redirect('/');
            exit();
        }

        $this->render('newsletter/welcome-newsletter', [
            'hideSiteHeader' => true,
            'newsletter' => true,
        ]);
    }
}