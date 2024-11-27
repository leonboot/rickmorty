<?php

namespace App\RickAndMortyApi\Repository;

use App\RickAndMortyApi\ApiClient;
use App\RickAndMortyApi\Resource\Character;
use App\RickAndMortyApi\Resource\Location;

class CharacterRepository
{

    public const HYDRATE_ORIGIN = 1;
    public const HYDRATE_LOCATION = 2;

    public function __construct(private readonly ApiClient $client)
    {
    }

    /**
     * @return array|Character[]
     */
    public function fetchAll(?int $hydrate = null): array
    {
        $characters = $this->client->fetchAllResources('character', Character::class);
        array_walk($characters, fn(Character $character) => $this->hydrateCharacter($character, $hydrate));
        usort($characters, fn(Character $a, Character $b) => $a->getName() <=> $b->getName());

        return $characters;
    }

    public function fetchByLocation(Location $location, ?int $hydrate = null): array
    {
        $results = [];

        foreach ($location->getResidents() as $resident) {
            $results[] = $this->hydrateCharacter(
                $this->client->fetchSingleResourceByUrl($resident, Character::class),
                $hydrate
            );
        }

        usort($results, fn(Character $a, Character $b) => $a->getName() <=> $b->getName());

        return $results;
    }

    public function fetchByDimension(string $dimension, ?int $hydrate = null): array
    {
        $results = array_filter(
            $this->fetchAll($hydrate | self::HYDRATE_LOCATION),
            fn($character) => $character->getLocation()->getDimension() === $dimension
        );

        usort($results, fn(Character $a, Character $b) => $a->getName() <=> $b->getName());

        return $results;
    }

    private function hydrateCharacter(Character $character, ?int $mode = null): Character
    {
        if ($mode & self::HYDRATE_ORIGIN) {
            $character->setOrigin($this->client->fetchSingleResourceByUrl($character->getOriginUrl(), Location::class));
        }

        if ($mode & self::HYDRATE_LOCATION) {
            $character->setLocation($this->client->fetchSingleResourceByUrl($character->getLocationUrl(), Location::class));
        }

        return $character;
    }
}
