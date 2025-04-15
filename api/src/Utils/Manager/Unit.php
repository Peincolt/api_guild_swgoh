<?php

namespace App\Utils\Manager;

use App\Utils\Factory\Unit as UnitFactory;
use App\Utils\Service\Api\SwgohGg;
use Doctrine\ORM\EntityManagerInterface;

class Unit
{
    private $entityManagerInterface;

    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        private SwgohGg $swgohGg,
        private UnitFactory $unitFactory
    )
    {
        $this->entityManagerInterface = $entityManagerInterface;
    }

    public function updateUnit(string $type) :bool|array
    {
        $dataUnits = $this->swgohGg->fetchHeroOrShip($type);
        if (isset($dataUnits['error_message_api_swgoh'])) {
            return $dataUnits;
        }
        $this->entityManagerInterface->beginTransaction();
        try {
            foreach ($dataUnits as $key => $dataUnit) {
                $unit = $this->unitFactory->getEntityByApiResponse($dataUnit, $type, $this->entityManagerInterface);
                if (!is_array($unit)) {
                    $this->entityManagerInterface->persist($unit);
                } else {
                    throw new \Exception($unit['error_message']);
                }
            }
            $this->entityManagerInterface->flush();
            $this->entityManagerInterface->commit();
            return true;
        } catch(\Exception $e) {
            $this->entityManagerInterface->rollback();
            return [
                'error_message' => $e->getMessage()
            ];
        }
        return $dataUnits;
    }
}