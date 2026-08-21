<?php

namespace App\Command;

use App\Controller\NewsletterCronController;
use App\Service\NewsletterMailerService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:send-newsletter',
    description: 'Envoie la newsletter de version aux abonnés.'
)]
class SendNewsletterCommand extends Command
{
    private NewsletterMailerService $newsletterService;

    public function __construct(NewsletterMailerService $newsletterService)
    {
        // On passe le nom au constructeur parent
        parent::__construct('app:send-newsletter');
        $this->newsletterService = $newsletterService;
    }

    protected function configure(): void
    {
        $this
            ->setName('app:send-newsletter')
            ->setDescription('Envoie la newsletter pour la version du site.')
            ->addArgument('version', InputArgument::OPTIONAL, 'La version à diffuser', NewsletterCronController::LATEST_VERSION);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        /** @var string $latestVersion */
        $latestVersion = $input->getArgument('version') ?? NewsletterCronController::LATEST_VERSION;

        $io->title("Exécution du cron newsletter — Version $latestVersion");

        try {
            $stats = $this->newsletterService->sendReleaseNewsletter($latestVersion);

            if ($stats['sent_count'] > 0) {
                $io->success("{$stats['sent_count']} e-mail(s) envoyé(s) sur ce lot pour la v{$latestVersion}.");
            } else {
                $io->info("Aucun e-mail à envoyer pour la version {$latestVersion}.");
            }

            if (!empty($stats['errors'])) {
                $io->warning("Erreurs rencontrées :");
                $io->listing($stats['errors']);
            }

            return Command::SUCCESS;

        } catch (\Throwable $e) {
            $io->error("Erreur fatale : " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}