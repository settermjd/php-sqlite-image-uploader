<?php

declare(strict_types=1);

namespace App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class UploadImageFormHandlerFactory
{
    public function __invoke(ContainerInterface $container) : UploadImageFormHandler
    {
        return new UploadImageFormHandler($container->get(TemplateRendererInterface::class));
    }
}
