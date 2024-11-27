<?php

namespace App\Twig\Extension;

use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ActiveLinkExtension extends AbstractExtension
{

    public function __construct(private readonly RouterInterface $router)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('activeLink', $this->isActiveLink(...)),
        ];
    }

    public function isActiveLink(string $route): bool
    {
        if (!str_starts_with($route, '/')) {
            $route = '/'.$route;
        }

        $currentRoute = $this->router->getContext()->getPathInfo();

        return $currentRoute === $route || str_starts_with($currentRoute, $route.'/');
    }
}
