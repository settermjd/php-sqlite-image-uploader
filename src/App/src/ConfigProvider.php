<?php

declare(strict_types=1);

namespace App;

use App\Handler\UploadHandlerFactory;
use App\Repository\ImageRepository;
use App\Repository\ImageRepositoryFactory;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
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
            'invokables' => [],
            'factories'  => [
                Handler\DeleteImageHandler::class   => ReflectionBasedAbstractFactory::class,
                Handler\HomePageHandler::class      => ReflectionBasedAbstractFactory::class,
                Handler\UploadHandler::class        => UploadHandlerFactory::class,
                Handler\DownloadImageHandler::class => ReflectionBasedAbstractFactory::class,
                ImageRepository::class              => ImageRepositoryFactory::class,
                LoggerInterface::class              => function (): LoggerInterface {
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
