<?php

namespace App\RickAndMortyApi;

use App\RickAndMortyApi\Resource\ApiResource;
use InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiClient
{

    private CacheInterface $cache;

    private HttpClientInterface $client;

    private const API_URL = 'https://rickandmortyapi.com/api/';

    public function __construct(private readonly SerializerInterface $serializer)
    {
        $this->cache = new FilesystemAdapter('rickandmortyapi', 1800);
        $this->client = HttpClient::create(['headers' => ['Accept' => 'application/json']]);
    }

    public function fetchSingleResourceByUrl(string $url, string $resourceClass): ApiResource
    {
        if (!is_subclass_of($resourceClass, ApiResource::class)) {
            throw new InvalidArgumentException('Resource class must be a subclass of '.ApiResource::class);
        }

        $url = $this->getAbsoluteUrl($url);

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
        $next = $this->getAbsoluteUrl($type);
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

    public function fetchMultipleResources(string $type, array $ids, string $resourceClass): array
    {
        if (!is_subclass_of($resourceClass, ApiResource::class)) {
            throw new InvalidArgumentException('Resource class must be a subclass of '.ApiResource::class);
        }

        $resources = [];
        $uncachedIds = [];

        foreach ($ids as $id) {
            if ($this->cache->hasItem($this->getAbsoluteUrl(sprintf('%s/%s', $type, $id)))) {
                $resources[] = $this->fetchSingleResourceByUrl(sprintf('%s/%s', $type, $id), $resourceClass);
            } else {
                $uncachedIds[] = $id;
            }
        }

        while (count($uncachedIds) > 0) {
            $chunk = array_splice($uncachedIds, 0, 20);
            $response = $this->client->request('GET', $this->getAbsoluteUrl(sprintf('%s/%s', $type, implode(',', $chunk))));
            $data = json_decode($response->getContent(), true);
            if (is_array($data)) {
                foreach ($data as $resourceData) {
                    if (isset($resourceData['id']))  {
                        $json = $this->cache->get($this->getAbsoluteUrl(sprintf('%s/%s', $type, $resourceData['id'])), function () use ($resourceData) {
                            return json_encode($resourceData);
                        });
                        $resources[] = $this->serializer->deserialize($json, $resourceClass, 'json');
                    }
                }
            }

        }

        return $resources;
    }

    private function getAbsoluteUrl(string $url): string
    {
        if (preg_match('/^https?:\/\//', $url)) {
            return $url;
        }

        if (str_starts_with($url, '/')) {
            return self::API_URL.substr($url, 1);
        }

        return self::API_URL.$url;
    }
}
