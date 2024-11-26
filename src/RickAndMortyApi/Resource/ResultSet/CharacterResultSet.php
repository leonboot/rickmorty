<?php

namespace App\RickAndMortyApi\Resource\ResultSet;

use App\RickAndMortyApi\Resource\Character;

class CharacterResultSet
{

    private array $results = [];

    /**
     * @return array
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @param array $results
     */
    public function setResults(array $results): void
    {
        foreach ($results as $result) {
            $this->addResult($result);
        }
    }

    public function addResult(Character $character): void
    {
        $this->results[] = $character;
    }

    public function deleteResult(Character $character): void
    {
        $key = array_search($character, $this->results, true);
        if ($key !== false) {
            unset($this->results[$key]);
            $this->results = array_values($this->results);
        }
    }
}
