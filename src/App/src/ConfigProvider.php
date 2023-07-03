<?php

declare(strict_types=1);

namespace App;

use App\Repository\ImageRepository;
use App\Repository\ImageRepositoryFactory;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.laminas.dev/laminas-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [
                Handler\PingHandler::class => Handler\PingHandler::class,
            ],
            'factories'  => [
                Handler\DeleteImageHandler::class     => Handler\DeleteImageHandlerFactory::class,
                Handler\HomePageHandler::class        => Handler\HomePageHandlerFactory::class,
                Handler\UploadHandler::class          => Handler\UploadHandlerFactory::class,
                Handler\ViewImageHandler::class       => Handler\ViewImageHandlerFactory::class,
                Handler\UploadImageFormHandler::class => Handler\UploadImageFormHandlerFactory::class,
                ImageRepository::class                => ImageRepositoryFactory::class,
                LoggerInterface::class                => function (): LoggerInterface {
                    $log = new Logger('name');
                    $log->pushHandler(new StreamHandler(__DIR__ . '/../../../data/log/app.log', Level::Debug));
                    return $log;
                },
            ],
        ];
    }

    /**
     * Returns the templates configuration
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                'app'    => [__DIR__ . '/../templates/app'],
                'error'  => [__DIR__ . '/../templates/error'],
                'layout' => [__DIR__ . '/../templates/layout'],
            ],
        ];
    }
}
