<?php

namespace App\Controller\Api\Squad;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Squad;
use App\Form\SquadType;
use App\Utils\Manager\Squad as SquadManager;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class SquadController extends AbstractController
{
    public function __construct(
        private SquadManager $squadManager,
        private SerializerInterface $serializer
    ) {
        
    }

    #[Route('/squad/{name}', name: 'api_squad', methods: ['GET'])]
    public function getSquad(Squad $squad): JsonResponse
    {
        return $this->json(
            $this->squadManager->getSquadData($squad)
        );
    }

    #[Route('/squad/{name}/units', name: 'api_squad_units', methods: ['GET'])]
    public function getSquadUnits(Squad $squad): JsonResponse
    {
        return $this->json(
            $this->squadManager->getSquadUnitsData($squad, true)
        );
    }

    #[Route('/squad/create', name: 'api_squad_create', methods: ['POST'])]
    public function createSquad(Request $request/*, FormError $form*/): JsonResponse
    {
        $squad = new Squad();
        $form = $this->createForm(SquadType::class, $squad);
        if ($request->isMethod('POST')) {
            $form->submit($request->request->all());
            if ($form->isSubmitted() && $form->isValid()) {
                return $this->json(
                    $this->squadManager->fillSquadByForm($squad, $form)
                );
            } else {
                return $this->json($this->generateErrorResponse($form));
            }
        }
    }

    private function generateErrorResponse(Form $form)
    {
        $arrayReturn = array();
        foreach($form->all() as $child) {
            if (!$child->isValid()) {
                foreach($child->getErrors(true, true) as $error) {
                    $arrayReturn['errors'][$child->getName()][] = $error->getMessage();
                }
            }
        }
        return $arrayReturn;
    }
}