<?php

declare(strict_types=1);

namespace App\Handler;

use Doctrine\ORM\EntityManager;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;

class HomePageHandlerFactory
{
    public function __invoke(ContainerInterface $container): RequestHandlerInterface
    {
        $template = $container->get(TemplateRendererInterface::class);
        assert($template instanceof TemplateRendererInterface);

        $entityManager = $container->get(EntityManager::class);

        return new HomePageHandler($template, $entityManager);
    }
}
