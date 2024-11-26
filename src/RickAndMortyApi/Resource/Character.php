<?php

namespace App\RickAndMortyApi\Resource;

use Symfony\Component\Serializer\Attribute\SerializedPath;

class Character extends ApiResource
{

    private ?CharacterStatus $status = null;

    private string $species;

    private string $type;

    private ?CharacterGender $gender = null;

    #[SerializedPath('[origin][url]')]
    private string $originUrl;

    private Location $origin;

    #[SerializedPath('[location][url]')]
    private string $locationUrl;

    private Location $location;

    private string $image;

    private array $episodes;

    public function getStatus(): ?CharacterStatus
    {
        return $this->status;
    }

    public function setStatus(?CharacterStatus $status): void
    {
        $this->status = $status;
    }

    public function getSpecies(): string
    {
        return $this->species;
    }

    public function setSpecies(string $species): void
    {
        $this->species = $species;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getGender(): ?CharacterGender
    {
        return $this->gender;
    }

    public function setGender(?CharacterGender $gender): void
    {
        $this->gender = $gender;
    }

    public function getOrigin(): Location
    {
        return $this->origin;
    }

    public function setOrigin(Location $origin): void
    {
        $this->origin = $origin;
    }

    public function getOriginUrl(): string
    {
        return $this->originUrl;
    }

    public function setOriginUrl(string $originUrl): void
    {
        $this->originUrl = $originUrl;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function setLocation(Location $location): void
    {
        $this->location = $location;
    }

    public function getLocationUrl(): string
    {
        return $this->locationUrl;
    }

    public function setLocationUrl(string $locationUrl): void
    {
        $this->locationUrl = $locationUrl;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function getEpisodes(): array
    {
        return $this->episodes;
    }

    public function setEpisodes(array $episodes): void
    {
        $this->episodes = $episodes;
    }
}
