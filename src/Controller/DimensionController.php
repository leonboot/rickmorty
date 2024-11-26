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
        return $this->render('dimensions/list.html.twig', [
            'dimensions' => $repository->fetchAllDimensions(),
        ]);
    }

    #[Route('/{dimension}', name: 'details')]
    public function details(string $dimension, LocationRepository $repository, CharacterRepository $characterRepository): Response
    {
        $dimension = rawurldecode($dimension);

        return $this->render('dimensions/details.html.twig', [
            'dimension' => $dimension,
            'locations' => $repository->fetchByDimension($dimension),
            'residents' => array_filter(
                $characterRepository->fetchAll(),
                fn($character) => $character->getLocation()->getDimension() === $dimension
            )
        ]);
    }
}
