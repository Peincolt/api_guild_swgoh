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
        
        // Si on a error_message_api_swgoh, c'est que l'on a eu une erreur lors de la récupération des infos directement via l'API
        if (isset($result['error_message_api_swgoh']) && is_string($result['error_message'])) {
            // On print le message
            $output->writeln(
                [
                    '<fg=red>Erreur lors de la synchronisation',
                    '===========================',
                    'Voilà le message d\'erreur :',
                    $result['error_message'].'</>'
                ]
            );
        }
        
        if (isset($result['error_messages']) && is_array($result['error_messages'])) {
            $output->writeln(
                [
                    '<fg=red>Erreur lors de la synchronisation',
                    '===========================',
                    'Voilà les messages d\'erreur :</>'
                ]
            );
            foreach($result['error_messages'] as $errorMessage) {
                $output->writeln($errorMessage.'</>');
            }
            $output->writeln(
                [
                    '===========================',
                    'Fin de la synchronisation'
                ]
            );
        }
        return Command::FAILURE;
    }
}