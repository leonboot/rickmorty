<?php

namespace App\RickAndMortyApi\Resource\ResultSet;

use App\RickAndMortyApi\Resource\Location;

class LocationResultSet extends AbstractResultSet
{

    private array $results = [];

    /**
     * @return array
     */
    public function getResults(): array
    {
        return $this->results;
    }

    public function addResult(Location $location): void
    {
        $this->results[] = $location;
    }

    public function removeResult(Location $location): void
    {
        $key = array_search($location, $this->results, true);
        if ($key !== false) {
            unset($this->results[$key]);
            $this->results = array_values($this->results);
        }
    }
}
