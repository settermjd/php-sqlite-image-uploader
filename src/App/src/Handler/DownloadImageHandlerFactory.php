<?php

declare(strict_types=1);

namespace App\Handler;

use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class DownloadImageHandlerFactory
{
    public function __invoke(ContainerInterface $container): DownloadImageHandler
    {
        return new DownloadImageHandler(
            $container->get(EntityManager::class),
            $container->get(LoggerInterface::class),
        );
    }
}
