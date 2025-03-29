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

    protected function configure(): void
    {
        $this
            ->addOption(
                'type',
                null,
                InputOption::VALUE_REQUIRED,
                'Quel type d\'unité souhaitez vous mettre à jour ? Héros (hero), vaisseaux (ships), les deux (all)',
                'all'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output) :int
    {
        $userOptions = $input->getOptions();

        if (empty($userOptions['ships'])
            && empty($userOptions['heros'])
            && empty($userOptions['all'])
        ) {
            $userOptions['all'] = true;
        }

        $output->writeln(
            [
                '<fg=yellow>Début de la commande',
                '==========================='
            ]
        );

        if ($userOptions['hero']) {

        }

        if ($userOptions['vaisseau']) {

        }

        if ($userOptions['all']) {

        }

        if ($userOptions['all'] || $userOptions['hero']) {
            $output->writeln(
                [
                    'Vous avez choisi de synchroniser les héros'.
                    (!empty($userOptions['all']) ? 'et les vaisseaux':''),
                    '===========================',
                    'Début de la synchronisation des héros ...</>',
                ]
            );

            $result = $this->unitManager->updateUnit('Hero');
        }

        if ($userOptions['all'] || $userOptions['ship']) {
            if (
                !empty($result) &&
                is_array($result) &&
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
            $output->writeln(
                [
                    '<fg=yellow>Vous avez choisi de synchroniser les vaisseaux',
                    '===========================',
                    'Début de la synchronisation des vaisseaux ...</>',
                ]
            );
            $result = $this->unitManager->updateUnit('Ship');
        }

        if (!empty($result)) {

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
        }
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
}