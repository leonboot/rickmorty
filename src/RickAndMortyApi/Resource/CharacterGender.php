<?php

namespace App\RickAndMortyApi\Resource;

enum CharacterGender: string
{

    case Female = 'Female';
    case Male = 'Male';
    case Genderless = 'Genderless';
    case Unknown = 'unknown';
}
