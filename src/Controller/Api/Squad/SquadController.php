<?php

namespace App\Controller\Api\Squad;

use App\Entity\Squad;
use App\Form\SquadType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use App\Utils\Manager\Squad as SquadManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SquadController extends AbstractController
{
    public function __construct(
        private SquadManager $squadManager
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
            $this->squadManager->getSquadUnitsData($squad)
        );
    }

    #[Route('/squad/create', name: 'api_squad_create', methods: ['POST'])]
    public function createSquad(Request $request): JsonResponse
    {
        $squad = new Squad();
        $form = $this->createForm(SquadType::class, $squad);
        $form->submit($request->request->all());
        if ($form->isSubmitted() && $form->isValid()) {
            $units = $request->get('units');
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
        if ($request->isMethod('PUT')) {
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
        return new JsonResponse(['error_message' => 'Mauvaise méthode'], 405);
    }

    #[Route('/squad/{unique_identifier}/delete', name: 'api_squad_delete', methods: ['DELETE'])]
    public function deleteSquad(Request $request, Squad $squad): JsonResponse
    {
        if ($request->isMethod('DELETE')) {
            $this->squadManager->getRepository()->remove($squad, true);
            return $this->json(
                ['result' => ['message' => 'L\'escouade a bien été supprimée']]
            );
        }
        return new JsonResponse(['error_message' => 'Mauvaise méthode'], 405);
    }

    /**
     * @return array<string, array<array<string>>>
     */
    private function generateErrorResponse(FormInterface $form): array
    {
        $arrayReturn = [];
        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                foreach ($child->getErrors(true, true) as $error) {
                    $arrayReturn['errors'][$child->getName()][] = $error->getMessage();
                }
            }
        }
        return $arrayReturn;
    }
}