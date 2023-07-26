<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Image;
use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_filter;
use function array_walk;

use const ARRAY_FILTER_USE_KEY;

class HomePageHandler implements RequestHandlerInterface
{
    public function __construct(private EntityManager $entityManager)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->entityManager
            ->getRepository(Image::class)
            ->findAll();

        if (! empty($response)) {
            $data = [];
            foreach ($response as $item) {
                $data[] = array_filter(
                    $item->__toArray(),
                    fn (string $key) => $key !== 'data',
                    ARRAY_FILTER_USE_KEY
                );
            }

            return new JsonResponse($data);
        }

        return new EmptyResponse();
    }
}
