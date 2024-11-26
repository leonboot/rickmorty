<?php

namespace App\RickAndMortyApi\Repository;

use App\RickAndMortyApi\ApiClient;
use App\RickAndMortyApi\Resource\Character;
use App\RickAndMortyApi\Resource\Location;

class LocationRepository
{

    public function __construct(private readonly ApiClient $client)
    {
    }

    public function fetchById(int $id): Location
    {
        return $this->hydrateLocation($this->client->fetchSingleResourceByUrl("location/$id", Location::class));
    }

    public function fetchAll(): array
    {
        $result = $this->client->fetchAllResources('location', Location::class);
        array_walk($result, fn(Location $location) => $this->hydrateLocation($location));

        return $result;
    }

    public function fetchByDimension(string $dimension): array
    {
        $results = array_filter(
            $this->fetchAll(),
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

        $results = array_filter($results, fn($dimension) => !in_array($dimension, ['', 'unknown']));
        sort($results);

        return $results;
    }

    private function hydrateLocation(Location $location): Location
    {
        $residents = array_map(
            fn($residentUrl) => $this->client->fetchSingleResourceByUrl($residentUrl, Character::class),
            $location->getResidentUrls()
        );

        usort($residents, fn(Character $a, Character $b) => $a->getName() <=> $b->getName());
        $location->setResidents($residents);

        return $location;
    }
}
