<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class ImageRepository extends EntityRepository
{
    public function getImages(): ArrayCollection
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $result       = $queryBuilder
            ->select('u')
            ->from(Image::class, 'u')
            ->getQuery()
            ->getResult();

        return new ArrayCollection($result);
    }
}
