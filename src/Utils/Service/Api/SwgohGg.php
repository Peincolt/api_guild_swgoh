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
            if (empty($guild['data'])) {
                return array(
                    'error_message' => 'La guilde que vous essayez de synchroniser n\existe pas'
                );
            }
            return $guild;
        } catch (Exception $e) {
            return array(
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage()
            );
        }
    }

    public function fetchPlayer(string $allyCode)
    {
        try {
            $response = $this->client->request(
                "GET",
                $this->baseUrl . "player/" . $allyCode
            );
            if ($response->getStatusCode() == 404) {
                throw new Exception(
                    'Le joueur que vous essayez de synchroniser n\'existe pas',
                    404
                );
            }
            return $response->toArray();
        } catch (Exception $e) {
            return array(
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage()
            );
        }
        
    }

    public function fetchHeroOrShip($type)
    {
        try {
            return $this->client->request(
                "GET",
                $this->baseUrl.$this->getRouteByEntityName($type)
            )->toArray();
        } catch (Exception $e) {
            return array(
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage()
            );
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
            return $this->client->request(
                "GET",
                $this->baseUrl .'abilities'
            )->toArray();
        } catch (Exception $e) {
            return array(
                'error_code' => $e->getCode(),
                'error_message_api_swgoh' => $e->getMessage()
            );
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