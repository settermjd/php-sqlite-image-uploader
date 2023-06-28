<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Image;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

class ImageRepositoryFactory
{
    public function __invoke(ContainerInterface $container): ImageRepository
    {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);

        return $entityManager->getRepository(Image::class);
    }
}
