<?php

namespace App\Command;

use App\Utils\Manager\Ability as AbilityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'synchro-abilities', description: 'Cette commande permet de récupérer les compétences des héros du jeu')]
class AbilitiesCommand extends Command
{
    public function __construct(private AbilityManager $abilityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output) :int
    {
        $output->writeln(
            [
                '<fg=yellow>Début de la commande',
                '===========================',
                'Début de la synchronisation des compétences</>'
            ]
        );

        $result = $this->abilityManager->updateAbilities();

        if (!is_array($result)) {
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