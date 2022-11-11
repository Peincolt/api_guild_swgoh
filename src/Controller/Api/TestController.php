<?php

namespace App\Controller\Api;

use App\Repository\SquadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test/fill-uniq-identifier', name: 'test_identifier', methods: ['GET'])]
    public function getSquad(SquadRepository $squadRepository): JsonResponse
    {
        //phpinfo();
        $squads = $squadRepository->findAll();
        foreach ($squads as $squad) {
            $true = true;
            $bytes = \openssl_random_pseudo_bytes(20, $true);
            $squad->setUniqueIdentifier(bin2hex($bytes));
            $squadRepository->save($squad, true);
        }
        //$this->getDoctrine()->getManager()->flush();
        die('lel');
        //$squadRepository->flush();*/
    }
}