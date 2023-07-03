<?php

declare(strict_types=1);

namespace App\Handler;

use Doctrine\ORM\EntityManager;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class ViewImageHandlerFactory
{
    public function __invoke(ContainerInterface $container): ViewImageHandler
    {
        return new ViewImageHandler(
            $container->get(TemplateRendererInterface::class),
            $container->get(EntityManager::class),
            $container->get(LoggerInterface::class),
        );
    }
}
