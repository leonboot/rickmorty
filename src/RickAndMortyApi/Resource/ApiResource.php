<?php

namespace App\RickAndMortyApi\Resource;

use DateTime;

abstract class ApiResource
{

    protected int $id;

    protected string $name;

    protected string $url;

    protected DateTime $created;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function setCreated(DateTime|string $created): void
    {
        if (is_string($created)) {
            $created = new DateTime($created);
        }

        $this->created = $created;
    }
}
