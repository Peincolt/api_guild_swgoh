<?php

namespace App\Command;

use App\Utils\Manager\Guild as GuildManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'synchro-guild', 
    description: 'Commande qui permet de récupérer les informations d\'une guilde grâce à l\'api de swgoh.gg', 
    hidden: false
)]
class GuildCommand extends Command
{
    public function __construct(private GuildManager $guildManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'id',
            InputArgument::REQUIRED,
            'Id de la guilde que vous souhaitez synchroniser'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(
            [
                '<fg=yellow>Début de la commande',
                '===========================',
                'Début de la synchronisation des informations de la guilde</>',
            ]
        );

        $result = $this->guildManager
            ->updateGuild((string)$input->getArgument('id'), $output);

        if (is_array($result)) {
            if (
                isset($result['error_message']) &&
                is_string($result['error_message'])
            ) {
                $output->writeln(
                    [
                        '<fg=red>Erreur lors de la synchronisation',
                        '===========================',
                        'Voilà le message d\'erreur :',
                        $result['error_message'].'</>'
                    ]
                );
                return Command::FAILURE;
            }

            if (
                isset($result['error_messages']) &&
                is_array($result['error_messages'])
            ) {
                $output->writeln(
                    [
                        '<fg=red>Une erreur est survenue lors de la synchronisation des joueurs suivants :</>',
                        '===========================',
                    ]
                );
                foreach($result['error_messages'] as $key) {
                    $output->writeln($key.'</>');
                }
                return Command::SUCCESS;
            }
        }

        $output->writeln(
            [
                '<fg=green>Fin de la synchronisation',
                '===========================',
                'Fin de la commande</>'
            ]
        );
        return Command::SUCCESS;
    }
}