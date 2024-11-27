<?php

namespace App\Controller;

use App\RickAndMortyApi\Repository\LocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/locations', name: 'locations_')]
class LocationsController extends AbstractController
{

    #[Route('/', name: 'list')]
    public function list(LocationRepository $repository): Response
    {
        $dimensions = $repository->fetchAllDimensions();

        $locationsByDimension = [];
        foreach ($dimensions as $dimension) {
            $locationsByDimension[$dimension] = $repository->fetchByDimension($dimension);
        }

        return $this->render('locations/list.html.twig', [
            'locationsByDimension' => $locationsByDimension,
        ]);
    }

    #[Route('/{id}', name: 'details')]
    public function details(string $id, LocationRepository $repository): Response
    {
        return $this->render('locations/details.html.twig', [
            'location' => $repository->fetchById($id, LocationRepository::HYDRATE_RESIDENTS),
        ]);
    }
}
