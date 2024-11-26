<?php

namespace App\RickAndMortyApi\Resource;

enum CharacterStatus: string
{

    case Alive = 'Alive';
    case Dead = 'Dead';
    case Unknown = 'unknown';
}
