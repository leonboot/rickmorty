<?php

namespace App\RickAndMortyApi\Resource\ResultSet;

abstract class AbstractResultSet
{

    private PaginationInfo $info;

    public function getInfo(): PaginationInfo
    {
        return $this->info;
    }

    public function setInfo(PaginationInfo $info): void
    {
        $this->info = $info;
    }
}
