<?php

namespace App\Utils\Service\Api;

use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class SwgohGg
{
    private $client;
    private $baseUrl;

    public function __construct(
        $baseUrl,
        HttpClientInterface $httpClientInterface,
    ) {
        $this->client = $httpClientInterface;
        $this->baseUrl = $baseUrl;
    }

    public function fetchGuild(string $id)
    {
        try {
            $guild = $this->client->request(
                "GET",
                $this->baseUrl . "guild-profile/" . $id
            )->toArray();
            return $guild;
        } catch (Exception $e) {
            $arrayReturn['error_code'] = $e->getCode();
            $arrayReturn['error_message'] = $e->getMessage();
            return $arrayReturn;
        }
    }

    public function fetchPlayer(string $allyCode)
    {
        try {
            return $this->client->request(
                "GET",
                $this->baseUrl . "player/" . $allyCode
            )->toArray();
        } catch (Exception $e) {
            return false;
        }
        
    }

    /**
     * Rest texte
     * var $type
     */
    public function fetchHeroOrShip($type)
    {
        try {
            return $this->client->request(
                "GET",
                $this->baseUrl.$this->getRouteByEntityName($type)
            )->toArray();
        } catch (Exception $e) {
            $arrayReturn['error_code'] = $e->getCode();
            $arrayReturn['error_message'] = $e->getMessage();
            return $arrayReturn;
        }
        
    }

    public function fetchHeroes($listId)
    {
        return $this->fetchHeroOrShip('characters', $listId);
    }

    public function fetchShips($listId)
    {
        return $this->fetchHeroOrShip('ships', $listId);
    }

    public function fetchAbilities() :array
    {
        try {
            return $this->client->request("GET", $this->baseUrl .'abilities')->toArray();
        } catch (Exception $e) {
            $arrayReturn['error_code'] = $e->getCode();
            $arrayReturn['error_message'] = $e->getMessage();
            return $arrayReturn;
        }
    }

    public function getRouteByEntityName(string $entityName) :string
    {
        switch ($entityName) {
            case 'Hero':
                return 'characters';
            break;
            case 'Ship':
                return 'ships';
            break;
        }
    }
}