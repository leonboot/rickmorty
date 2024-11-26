<?php

namespace App\RickAndMortyApi\Resource;

use Symfony\Component\Serializer\Attribute\SerializedPath;

class Location extends ApiResource
{

    private string $type;

    private ?string $dimension = null;

    #[SerializedPath("[residents]")]
    private array $residentUrls = [];

    private array $residents = [];

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getDimension(): ?string
    {
        return $this->dimension;
    }

    public function setDimension(?string $dimension): void
    {
        $this->dimension = $dimension;
    }

    public function getResidents(): array
    {
        return $this->residents;
    }

    public function setResidents(array $residents): void
    {
        $this->residents = $residents;
    }

    public function getResidentUrls(): array
    {
        return $this->residentUrls;
    }

    public function setResidentUrls(array $residentUrls): void
    {
        $this->residentUrls = $residentUrls;
    }
}
