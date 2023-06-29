<?php

declare(strict_types=1);

namespace App\Handler;

use Doctrine\ORM\EntityManager;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class UploadHandlerFactory
{
    public function __invoke(ContainerInterface $container): UploadHandler
    {
        return new UploadHandler(
            $container->get(EntityManager::class),
            $container->get(LoggerInterface::class),
        );
    }
}
