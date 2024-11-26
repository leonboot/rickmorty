<?php

namespace App\RickAndMortyApi;

use App\RickAndMortyApi\Resource\ApiResource;
use InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiClient
{

    private CacheInterface $cache;

    public function __construct(
        #[Autowire(service: 'rickandmorty.client')] protected readonly HttpClientInterface $client, private readonly SerializerInterface $serializer
    ) {
        $this->cache = new FilesystemAdapter('rickandmortyapi', 1800);
    }

    public function fetchSingleResourceByUrl(string $url, string $resourceClass): ApiResource
    {
        if (!is_subclass_of($resourceClass, ApiResource::class)) {
            throw new InvalidArgumentException('Resource class must be a subclass of '.ApiResource::class);
        }

        $json = $this->cache->get($url, function () use ($url) {
            $response = $this->client->request('GET', $url);
            return $response->getContent();
        });

        return $this->serializer->deserialize($json, $resourceClass, 'json');
    }

    public function fetchAllResources(string $type, string $resourceClass): array
    {
        if (!is_subclass_of($resourceClass, ApiResource::class)) {
            throw new InvalidArgumentException('Resource class must be a subclass of '.ApiResource::class);
        }

        $resources = [];
        $next = $type;
        while ($next !== null) {
            $json = $this->cache->get($next, function () use ($next) {
                $response = $this->client->request('GET', $next);
                return $response->getContent();
            });

            $data = json_decode($json, true);
            if (isset($data['results']) && is_array($data['results'])) {
                $resources = array_merge(
                    $resources,
                    $this->serializer->deserialize(json_encode($data['results']), $resourceClass.'[]', 'json')
                );
            }

            if (isset($data['info']['next'])) {
                $next = $data['info']['next'];
            } else {
                $next = null;
            }
        }

        return $resources;
    }
}
