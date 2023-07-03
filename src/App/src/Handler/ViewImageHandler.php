<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Image;
use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Filter\Digits;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

use function sprintf;

class ViewImageHandler implements RequestHandlerInterface
{
    private InputFilter $inputFilter;

    public function __construct(
        private TemplateRendererInterface $renderer,
        private EntityManager $entityManager,
        private LoggerInterface $logger,
    ) {
        $fileId = new Input('id');
        $fileId->getFilterChain()
            ->attach(new Digits());

        $this->inputFilter = new InputFilter();
        $this->inputFilter->add($fileId);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->inputFilter->setData($request->getAttributes());
        if ($this->inputFilter->isValid()) {
            $fileId = $this->inputFilter->getValue('id');
            $this->logger->debug(sprintf(
                'Looking for file with ID: %s.',
                $request->getAttribute('id')
            ));
            $image = $this->entityManager
                ->getRepository(Image::class)
                ->findOneBy(['id' => $fileId]);

            if (! $image instanceof Image) {
                $this->logger->error(sprintf(
                    'Could not retrieve file with ID: %s.',
                    $request->getAttribute('id')
                ));
                return new RedirectResponse('/');
            }

            $this->logger->error(sprintf(
                'Retrieved image with id %s.',
                $this->inputFilter->getValue('id')
            ));

            return new HtmlResponse(
                $this->renderer->render(
                    'app::view-image',
                    [
                        'image' => $image,
                    ]
                )
            );
        }

        return new RedirectResponse('/');
    }
}
