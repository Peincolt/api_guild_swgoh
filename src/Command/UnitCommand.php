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
                'Quel type d\'unité souhaitez vous mettre à jour ? Héros (heros), vaisseaux (ships), les deux (all)',
                'all'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output) :int
    {
        $typeUnit = $input->getOption('type');

        if (
            empty($typeUnit) ||
            !is_string($typeUnit) ||
            !in_array($typeUnit, ['heros', 'ships', 'all'], true)
        ) {
            $typeUnit = 'all';
        }

        $types = ($typeUnit === 'all') ? ['heros', 'ships'] : [$typeUnit];
        $output->writeln(
            [
                '<fg=yellow>Début de la commande',
                '==========================='
            ]
        );

        foreach($types as $type) {
            if ($type === 'heros') {
                $output->writeln('Début de la synchronisation des héros ...</>');
                $result = $this->unitManager->updateUnit('Hero');
            } elseif($type === 'ships') {
                $output->writeln('Début de la synchronisation des vaisseaux ...</>');
                $result = $this->unitManager->updateUnit('Ship');
            }
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
            } else {
                $output->writeln('<fg=green>Fin de la synchronisation des '.($type === 'heros' ? 'héros' : 'vaisseaux').'.</>');
            }
        }

        $output->writeln(
            [
                '<fg=green>===========================',
                'Fin de la commande</>'
            ]
        );
        return Command::SUCCESS;
    }
}