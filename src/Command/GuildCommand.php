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

    protected function configure()
    {
        $this->addArgument(
            'id',
            InputArgument::REQUIRED,
            'Id de la guilde que vous souhaitez synchroniser'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            [
                '<fg=yellow>Début de la commande',
                '===========================',
                'Début de la synchronisation des informations de la guilde</>',
            ]
        );

        // TESTER RETOUR CODE QUAND MET RIEN
        /*if (emptyu($input->getArgument('id'))) {
            $output->writeln(
                [
                    '<fg=red>Début de la commande',
                    '===========================</fg>',
                ]
            );
        }*/

        $result = $this->guildManager
            ->updateGuild($input->getArgument('id'), $output);

        if (is_array($result)) {
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