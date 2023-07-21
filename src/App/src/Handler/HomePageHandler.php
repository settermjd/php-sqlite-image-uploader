<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Image;
use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HomePageHandler implements RequestHandlerInterface
{
    public function __construct(
        private TemplateRendererInterface $template,
        private EntityManager $entityManager,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = [];

        $images = $this->entityManager
            ->getRepository(Image::class)
            ->findAll();
        $data['images'] = $images;

        return new HtmlResponse(
            $this->template->render('app::home-page', $data)
        );
    }
}
