<?php

namespace App\Controller;

use App\RickAndMortyApi\Repository\CharacterRepository;
use App\RickAndMortyApi\Repository\LocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dimensions', name: 'dimensions_')]
class DimensionController extends AbstractController
{

    #[Route('/', name: 'list')]
    public function list(LocationRepository $repository): Response
    {
        $dimensions = $repository->fetchAllDimensions();

        $locationsByDimension = [];
        foreach ($dimensions as $dimension) {
            $locationsByDimension[$dimension] = $repository->fetchByDimension($dimension);
        }

        return $this->render('dimensions/list.html.twig', [
            'locationsByDimensions' => $locationsByDimension,
        ]);
    }

    #[Route('/{dimension}', name: 'details')]
    public function details(string $dimension, LocationRepository $repository, CharacterRepository $characterRepository): Response
    {
        $dimension = rawurldecode($dimension);
        $locations = $repository->fetchByDimension($dimension, LocationRepository::HYDRATE_RESIDENTS);
        $residents = array_merge(
            ...array_map(
                fn($location) => $location->getResidents(),
                $locations
            )
        );
        return $this->render('dimensions/details.html.twig', [
            'dimension' => $dimension,
            'locations' => $locations,
            'residents' => $residents,
        ]);
    }
}
