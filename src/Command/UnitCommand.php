<?php

namespace App\Command;

use App\Utils\Manager\Unit as UnitManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'synchro-unit',
    description: 'Cette commande permet de récupérer les unités (héros et vaisseaux) du jeu',
    hidden: false
)]

class UnitCommand extends Command 
{
    public function __construct(private UnitManager $unitManager)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addOption('heros', 'r', InputOption::VALUE_NONE, 'Récupération des héros')
            ->addOption('ships', 's', InputOption::VALUE_NONE, 'Récupération des vaisseaux')
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Récupération des héros et des vaisseaux');
    }

    protected function execute(InputInterface $input, OutputInterface $output) :int
    {
        $userOptions = $input->getOptions(
            [
                'heros',
                'ships',
                'all'
            ]
        );

        if (
            empty($userOptions['ships']) && 
            empty($userOptions['heros']) &&
            empty($userOptions['all'])
        ) {
            $userOptions['all'] = true;
        }

        $output->writeln(
            [
                '<fg=yellow>Début de la commande',
                '==========================='
            ]
        );

        if ($userOptions['all'] || $userOptions['hero']) {
            $output->writeln(
                [
                    'Vous avez choisi de synchroniser les héros'.(!empty($userOptions['all']) ? 'et les vaisseaux':''),
                    '===========================',
                    'Début de la synchronisation des héros ...</>',
                ]
            );

            $result = $this->unitManager->updateUnit('Hero');
        }

        if ($userOptions['all'] || $userOptions['ship']) {
            if (!empty($result) && is_array($result)) {
                $output->writeln(
                    [
                        '<bg=red;fg=white>Erreur lors de la synchronisation</fg>',
                        '<bg=red;fg=white>===========================</fg>',
                        '<bg=red;fg=white>Voilà le message d\'erreur :</fg>',
                        '<bg=red;fg=white>'.$result['error_message'].'</fg>'
                    ]
                );
                return Command::FAILURE;
            }
            $output->writeln(
                [
                    '<fg=yellow>Vous avez choisi de synchroniser les vaisseaux',
                    '===========================',
                    'Début de la synchronisation des vaisseaux ...</>',
                ]
            );
            $result = $this->unitManager->updateUnit('Ship');
        }

        if (!empty($result) && !is_array(($result))) {
            $output->writeln(
                [
                    '<fg=green>Fin de la synchronisation',
                    '===========================',
                    'Fin de la commande</>'
                ]
            );
            return Command::SUCCESS;
        } else {
            $output->writeln(
                [
                    '<bg=red;fg=white>Erreur lors de la synchronisation</fg>',
                    '<bg=red;fg=white>===========================</fg>',
                    '<bg=red;fg=white>Voilà le message d\'erreur :</fg>',
                    '<bg=red;fg=white>'.$result['error_message'].'</fg>'
                ]
            );
            return Command::FAILURE;
        }
    }
}