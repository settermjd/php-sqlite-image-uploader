<?php

declare(strict_types=1);

namespace App\Handler;

use Imagick;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_intersect;

class UploadImageFormHandler implements RequestHandlerInterface
{
    private TemplateRendererInterface $renderer;

    public const SUPPORTED_FORMATS = [
        'AVIF',
        'GIF',
        'JPEG',
        'PNG',
        'SVG',
        'WEBP',
    ];

    public function __construct(TemplateRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse($this->renderer->render(
            'app::upload-image-form',
            [
                'formats' => array_intersect(
                    self::SUPPORTED_FORMATS,
                    Imagick::queryFormats()
                ),
            ]
        ));
    }
}
