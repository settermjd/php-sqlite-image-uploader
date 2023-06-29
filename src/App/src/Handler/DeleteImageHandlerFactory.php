<?php

declare(strict_types=1);

namespace App\Handler;

use Doctrine\ORM\EntityManager;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class DeleteImageHandlerFactory
{
    public function __invoke(ContainerInterface $container): DeleteImageHandler
    {
        return new DeleteImageHandler(
            $container->get(TemplateRendererInterface::class),
            $container->get(EntityManager::class),
            $container->get(LoggerInterface::class),
        );
    }
}
