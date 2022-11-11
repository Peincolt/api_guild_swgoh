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

    #[Route('/squad/{unique_identifier}', name: 'api_squad', methods: ['GET'])]
    public function getSquad(Squad $squad): JsonResponse
    {
        return $this->json(
            $this->squadManager->getSquadData($squad)
        );
    }

    #[Route('/squad/{unique_identifier}/units', name: 'api_squad_units', methods: ['GET'])]
    public function getSquadUnits(Squad $squad): JsonResponse
    {
        return $this->json(
            $this->squadManager->getSquadUnitsData($squad, true)
        );
    }

    #[Route('/squad/create', name: 'api_squad_create', methods: ['POST'])]
    public function createSquad(Request $request): JsonResponse
    {
        $squad = new Squad();
        $form = $this->createForm(SquadType::class, $squad);
        $form->submit($request->request->all());
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->json(
                $this->squadManager->fillSquadByForm($squad, $form)
            );
        } else {
            return $this->json($this->generateErrorResponse($form));
        }
    }

    #[Route('/squad/{unique_identifier}/update', name: 'api_squad_update', methods: ['PUT'])]
    public function updateSquad(Squad $squad, Request $request): JsonResponse
    {
        $form = $this->createForm(SquadType::class, $squad);
        $form->submit($request->request->all());
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->json(
                $this->squadManager->fillSquadByForm($squad, $form)
            );
        } else {
            foreach($form->getErrors() as $error) {
                var_dump($error->getMessage());
            }
            return $this->json($this->generateErrorResponse($form));
        }
    }

    #[Route('/squad/{unique_identifier}/delete', name: 'api_squad_delete', methods: ['DELETE'])]
    public function deleteSquad(Squad $squad): JsonResponse
    {
        $this->squadManager->getRepository()->remove($squad, true);
        return $this->json(
            ['result' => ['message' => 'L\'escouade a bien été supprimée']]
        );
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