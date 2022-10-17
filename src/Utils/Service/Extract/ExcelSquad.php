<?php

namespace App\Utils\Service\Extract;

use App\Entity\Guild;
use App\Entity\Squad;
use App\Repository\GuildRepository;
use App\Repository\SquadRepository;
use App\Repository\SquadUnitRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use ReflectionClass;

class ExcelSquad
{
    //private $squadService;

    public function __construct(
        private SquadRepository $squadRepository, 
        //SquadService $squadService, 
        private GuildRepository $guildRepository,
        private SquadUnitRepository $squadUnitRepository
    )
    {}

    public function constructSpreadShitViaCommand(Guild $guild, string $option)
    {
        $squads = $this->squadRepository->findBy(
            [
                'guild' => $guild,
                'used_for' => $this->translateType($option)
            ]
        );

        return $this->constructSpreadShit($guild, $squads);
    }

    public function constructSpreadShit(Guild $guild, $squads)
    {
        $spreadSheet = new Spreadsheet();
        $arrayInformationHero = array ("Etoile Gear Relic (Speed)");
        $arrayInformationShip = array ("Protection/Vie (Speed)");
        $numberPlayers = $guild->getNumberPlayers();
        $spreadSheet->removeSheetByIndex(0);
        foreach ($squads as $squad) {
            $compteur = 0;
            $startData = 4;
            $numberLineStatUnit = $startData + $numberPlayers;
            $startLetter = "B";
            if ($squad->getType() == "hero") {
                $arrayInformations = $arrayInformationHero;
            } else {
                $arrayInformations = $arrayInformationShip;
            }

            $sheet = $spreadSheet->createSheet();
            $sheet->setTitle($squad->getName());
            $sheet->setCellValue('A1', 'Joueur');
            $sheet->setCellValue('B1', 'Unités');
            $sheet->mergeCells('B1:U1');
            $sheet->mergeCells('A2:A3');

            // Affichage du nom des unités, des stats gear et de la première colonne du tableau (Protection/Vie (Speed))
            foreach ($squad->getUnits() as $squadUnit) {
                $reflection = new ReflectionClass($squadUnit->getUnit());
                $sheet->setCellValue(
                    $startLetter."2",
                    $squadUnit->getUnit()->getName()
                );
                if ($reflection->getShortName() == "Hero") {
                    $sheet->setCellValue(
                        $startLetter.($numberLineStatUnit),
                        "=COUNTIF(".
                        $startLetter.
                        "4:".
                        $startLetter.
                        ($numberLineStatUnit-1).",\"*G13*\")"
                    );
                }
                $sheet->getCell($startLetter.($numberLineStatUnit))
                    ->getStyle()->setQuotePrefix(true);
                $sheet->fromArray($arrayInformations, null, $startLetter."3");
                $compteur++;
                $startLetter++;
            }

            $startLetter++;

            // Affichage du tableau stats des joueurs
            /*$sheet->setCellValue($startLetter."2", "Stats des persos du joueur");
            $sheet->setCellValue($startLetter."3", "Nombre de gear 13");
            $startLetter++;
            $sheet->setCellValue($startLetter."3", "Nombre de gear 12");
            $startLetter++;
            $sheet->setCellValue($startLetter."3", "Nombre de gear <= 11");

            $sheet->setCellValue($startLetter[$compteur].($NbSiFormulaStart),"=COUNTIF(B4:".$startLetter.($NbSiFormulaStart-1).",\"*G13*\")");
            $sheet->setCellValue($startLetter[$compteur].($NbSiFormulaStart),"=COUNTIF(".$startLetter."4:".$startLetter.($NbSiFormulaStart-1).",\"*G13*\")");*/

            if ($squad->getType() == "hero") {
                $sheet->setCellValue('A'.$numberLineStatUnit, 'Nombre de G13 :');
            }
            
            $squadData = $this->squadService
                ->getPlayerSquadInformation($squad, $guild);
            foreach ($squadData as $player => $data) {
                $sheet->setCellValue('A'.$startData, $player);
                $startLetter = "B";
                $sheet->setCellValue($this->incrementLetter($startLetter,count($squad->getSquadUnits()) + 1).($startData),"=COUNTIF(".$startLetter.$startData.":".$this->incrementLetter($startLetter,count($squad->getSquadUnits())).($startData).",\"*G13*\")");
                $sheet->setCellValue($this->incrementLetter($startLetter,count($squad->getSquadUnits()) + 2).($startData),"=COUNTIF(".$startLetter.$startData.":".$this->incrementLetter($startLetter,count($squad->getSquadUnits())).($startData).",\"*G12*\")");
                $sheet->setCellValue($this->incrementLetter($startLetter,count($squad->getSquadUnits()) + 3).($startData),"=".count($squad->getSquadUnits())."-".$this->incrementLetter($startLetter,count($squad->getSquadUnits()) + 2).($startData)."-".$this->incrementLetter($startLetter,count($squad->getSquadUnits()) + 1).($startData));
                foreach ($data as $arrayValueUnit) {
                    if ($squad->getType() == "hero") {
                        $chain = $arrayValueUnit['rarity'].'* G'.$arrayValueUnit['gear_level'].' R'.$arrayValueUnit['relic_level'].' ('.$arrayValueUnit['speed'].')';
                        if (!empty($arrayValueUnit['omicrons'])) {
                            $chain.=' omicron(s): ';
                            for ($i = 0; $i < count($arrayValueUnit['omicrons']); $i++) {
                                if ($i == count($arrayValueUnit['omicrons']) - 1) {
                                    $chain.=$arrayValueUnit['omicrons'][$i];
                                } else {
                                    $chain.=$arrayValueUnit['omicrons'][$i].',';
                                }
                            }
                        }
                        $sheet->setCellValue($startLetter.$startData, $chain);
                        $sheet->getStyle($startLetter.$startData)->applyFromArray($this->getStyleByGear($arrayValueUnit['gear_level']));
                        $startLetter++;
                    } else {
                        $sheet->setCellValue($startLetter.$startData,$arrayValueUnit['protection'].'/'.$arrayValueUnit['life'].' ('.$arrayValueUnit['speed'].')');
                        $startLetter++;
                    }
                }
                $startData++;
                
            }
        }
        $writer = new Xlsx($spreadSheet);
        $writer->save($folder.'/'.$guild->getName().' - '.$type.'.xlsx');
    }

    public function getStyleByGear(String $gearLevel)
    {
        switch ($gearLevel) {
            case 13:
                $color = 'FF0000';
                break;

            case 12:
                $color = 'FFC90E';
                break;

            default:
                $color = '800080';
                break;
        }

        return array(
            'font' => [
                'bold' => true,
                'color' => array('rgb' => $color)
            ]
        );
    }

    private function translateType(string $type)
    {
        switch ($type) {
            case "a":
                return "attack";
                break;
            case "d":
                return "defense";
                break;
            default:
                return "all";
                break;
        }
    }

    /**
     * Fonction qui permet d'incrémenter les lettres pour les tableaux Excel
     * 
     * @param string $letter Lettre à incrémenter
     * @param int    $number Nombre qui va servir dans la boucle for
     * 
     * @return $letter
     */
    public function incrementLetter(string $letter, int $number)
    {
        for ($i = 1; $i <= $number; $i++) {
            $letter++;
        }
        return $letter;
    }
}