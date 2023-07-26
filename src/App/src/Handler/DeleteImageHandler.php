<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Image;
use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Filter\Digits;
use Laminas\Filter\StringToLower;
use Laminas\Filter\StripNewlines;
use Laminas\Filter\StripTags;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Mezzio\Flash\FlashMessageMiddleware;
use Mezzio\Flash\FlashMessagesInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

use function sprintf;

class DeleteImageHandler implements RequestHandlerInterface
{
    private InputFilter $inputFilter;

    public function __construct(
        private EntityManager $entityManager,
        private LoggerInterface $logger,
    ) {
        $fileId = new Input('id');
        $fileId->getFilterChain()
            ->attach(new StringToLower())
            ->attach(new StripNewlines())
            ->attach(new StripTags())
            ->attach(new Digits());

        $this->inputFilter = new InputFilter();
        $this->inputFilter->add($fileId);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->logger->debug('Request attributes.', $request->getAttributes());

        $this->inputFilter->setData($request->getAttributes());
        if ($this->inputFilter->isValid()) {
            $fileId = $this->inputFilter->getValue('id');
            $this->logger->debug(sprintf(
                'Looking for file with ID: %s.',
                $fileId
            ));

            $image = $this->entityManager
                ->getRepository(Image::class)
                ->findOneBy(['id' => $fileId]);

            if (! $image instanceof Image) {
                $message = sprintf('Could not retrieve file with ID: %s.', $request->getAttribute('id'));
                $this->logger->error($message);
                return new JsonResponse($message);
            }

            $this->entityManager->remove($image);
            $this->entityManager->flush();

            $this->logger->error(sprintf('Delete image with id %s.', $fileId));

            return new JsonResponse('Image was deleted.');
        }

        $this->logger->error('Could not delete the image.', $this->inputFilter->getMessages());

        return new JsonResponse('Could not delete the image.');
    }
}
