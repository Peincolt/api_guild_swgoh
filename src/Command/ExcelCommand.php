<?php

namespace App\Command;

use App\Entity\Guild;
use App\Repository\GuildRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\phpspreadsheet\GenerateExcel;
use App\Utils\Service\Extract\ExcelSquad;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'generate-excel', description: 'Cette commande permet de générer un fichier Excel avec les escouades des guildes')]
class ExcelCommand extends Command
{
    public function __construct(
        private ExcelSquad $excelSquad,
        private GuildRepository $guildRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('id', InputArgument::REQUIRED, 'Id de la guilde a utilisé afin de générer les exports CSV')
            ->addOption('type', 'ty', InputOption::VALUE_OPTIONAL, 'Souhaitez les teams de défenses ou les teams d\'attaque ? (d : défense ,a : attaque, an: analyse, tb: tb, t : tout)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(
            [
                '<fg=yellow>Début de la commande',
                '==========================='
            ]
        );

        $guild = $this->guildRepository
            ->findOneBy(
                [
                    'id_swgoh' => $input->getArgument('id')
                ]
            );

        if (empty($guild)) {
            $output->writeln(
                [
                    '<fg=red>Erreur : Impossible de trouver la guilde dans la base de données',
                    '===========================',
                    'Fin de la commande</>'
                ]
            );
            return Command::FAILURE;
        }

        if (!empty($guild->getName())) {
            $output->writeln(
                [
                    'Vous souhaitez générer un fichier Excel à partir des informations de la guilde '.
                    $guild->getName(),
                    '===========================',
                ]
            );
        }

        $type = $input->getOption('type');
        if (!empty($type) && is_string($type)) {
            $type = $input->getOption('type');
            switch ($type) {
                case "a":
                    $output->writeln('Vous avez décidé de récupérer les teams utilisées pour l\'attaque');
                    break;
                case "d":
                    $output->writeln('Vous avez décidé de récupérer les teams utilisées pour la défense');
                    break;
                case "an":
                    $output->writeln('Vous avez décidé de récupérer les teams pour analyse de roster');
                    break;
                case "tb":
                    $output->writeln('Vous avez décidé de récupérer les teams pour la TB');
                    break;
                default:
                    $output->writeln('Vous avez décidé de récupérer toutes les teams (défense + attaque)');
                    $type = "t";
                    break;
            }
        } else {
            $type = "t";
        }

        $output->writeln('Début de la génération de la matrice Excel...');

        $result = $this->excelSquad->constructSpreadShitViaCommand(
            $guild,
            $type
        );

        if (
            is_array($result) &&
            isset($result['error_message']) &&
            is_string($result['error_message'])
        ) {
            $output->writeln(
                [
                    '<fg=red>Erreur lors de la génération du fichier Excel',
                    '===========================',
                    'Voilà le message d\'erreur :',
                    strval($result['error_message']).'</>'
                ]
            );
            return Command::FAILURE;
        }

        $output->writeln(
            [
                '<fg=green>Fin de la génération de la matrice Excel',
                '===========================',
                'Fin de la commande</>'
            ]
        );
        return Command::SUCCESS;
    }
}