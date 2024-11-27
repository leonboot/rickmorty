<?php

namespace App\RickAndMortyApi\Repository;

use App\RickAndMortyApi\ApiClient;
use App\RickAndMortyApi\Resource\Character;
use App\RickAndMortyApi\Resource\Location;

class LocationRepository
{

    public const HYDRATE_RESIDENTS = 1;

    public function __construct(private readonly ApiClient $client)
    {
    }

    public function fetchById(int $id, ?int $hydrate = null): Location
    {
        return $this->hydrateLocation($this->client->fetchSingleResourceByUrl("location/$id", Location::class), $hydrate);
    }

    public function fetchAll(?int $hydrate = null): array
    {
        $result = $this->client->fetchAllResources('location', Location::class);
        array_walk($result, fn(Location $location) => $this->hydrateLocation($location, $hydrate));

        return $result;
    }

    public function fetchByDimension(string $dimension, ?int $hydrate = null): array
    {
        $results = array_filter(
            $this->fetchAll($hydrate),
            fn($location) => $location->getDimension() === $dimension
        );

        usort($results, fn(Location $a, Location $b) => $a->getName() <=> $b->getName());

        return $results;
    }

    public function fetchAllDimensions(): array
    {
        $results = array_unique(array_map(
            fn($location) => $location->getDimension(),
            $this->fetchAll()
        ));

        $results = array_filter($results, fn($dimension) => !in_array($dimension, ['']));
        sort($results);

        return $results;
    }

    private function hydrateLocation(Location $location, ?int $mode = null): Location
    {
        if ($mode & self::HYDRATE_RESIDENTS) {
            $residentIds = array_map(
                fn($residentUrl) => (int)basename($residentUrl),
                $location->getResidentUrls()
            );

            $residents = $this->client->fetchMultipleResources('character', $residentIds, Character::class);
            array_walk($residents, fn(Character $character) => $character->setLocation($location));

            usort($residents, fn(Character $a, Character $b) => $a->getName() <=> $b->getName());
            $location->setResidents($residents);
        }

        return $location;
    }
}
