<?php

namespace App\RickAndMortyApi\Resource\ResultSet;

class PaginationInfo
{

    private int $count;

    private int $pages;

    private ?string $next = null;

    private ?string $prev = null;

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function getPages(): int
    {
        return $this->pages;
    }

    public function setPages(int $pages): void
    {
        $this->pages = $pages;
    }

    public function getNext(): ?string
    {
        return $this->next;
    }

    public function setNext(?string $next): void
    {
        $this->next = $next;
    }

    public function getPrev(): ?string
    {
        return $this->prev;
    }

    public function setPrev(?string $prev): void
    {
        $this->prev = $prev;
    }
}
