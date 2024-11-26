<?php

namespace App\RickAndMortyApi\Repository;

use App\RickAndMortyApi\ApiClient;
use App\RickAndMortyApi\Resource\Character;
use App\RickAndMortyApi\Resource\Location;

class CharacterRepository
{

    public function __construct(private readonly ApiClient $client)
    {
    }

    /**
     * @return array|Character[]
     */
    public function fetchAll(): array
    {
        $characters = $this->client->fetchAllResources('character', Character::class);
        array_walk($characters, fn(Character $character) => $this->hydrateCharacter($character));
        usort($characters, fn(Character $a, Character $b) => $a->getName() <=> $b->getName());

        return $characters;
    }

    public function fetchByLocation(Location $location): array
    {
        $results = [];

        foreach ($location->getResidents() as $resident) {
            $results[] = $this->hydrateCharacter($this->client->fetchSingleResourceByUrl($resident, Character::class));
        }

        usort($results, fn(Character $a, Character $b) => $a->getName() <=> $b->getName());

        return $results;
    }

    public function fetchByDimension(string $dimension): array
    {
        $results = array_filter(
            $this->fetchAll(),
            fn($character) => $character->getLocation()->getDimension() === $dimension
        );

        usort($results, fn(Character $a, Character $b) => $a->getName() <=> $b->getName());

        return $results;
    }

    private function hydrateCharacter(Character $character): Character
    {
        $character->setOrigin($this->client->fetchSingleResourceByUrl($character->getOriginUrl(), Location::class));
        $character->setLocation($this->client->fetchSingleResourceByUrl($character->getLocationUrl(), Location::class));

        return $character;
    }
}
