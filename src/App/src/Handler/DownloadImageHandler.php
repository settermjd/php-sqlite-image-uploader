<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Image;
use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\StreamFactory;
use Laminas\Filter\Digits;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

use function is_array;
use function sprintf;

class DownloadImageHandler implements RequestHandlerInterface
{
    private InputFilter $inputFilter;

    public function __construct(
        private EntityManager $entityManager,
        private LoggerInterface $logger,
    ) {
        $fileId = new Input('id');
        $fileId
            ->getFilterChain()
            ->attach(new Digits());

        $this->inputFilter = new InputFilter();
        $this->inputFilter->add($fileId);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $result = $this->getImage($request);

        if ($result instanceof Image) {

            $this->logger->debug(sprintf(
                'Retrieved image with length: %d and format: %s',
                $result->getSize(),
                $result->getFormat(),
            ));

            $response = (new Response())
                ->withHeader('Content-Length', $result->getSize())
                ->withHeader('Content-Type', "image/{$result->getFormat()}");
            $response->getBody()->write(base64_decode($result->getData()));

            return $response;
        }

        if (is_array($result)) {
            return new JsonResponse($result, 400);
        }

        return new JsonResponse("Image could not be retrieved", 404);
    }

    private function getImage(ServerRequestInterface $request): Image|null|array
    {
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

            $this->logger->error(sprintf('Retrieved image with id %s.', $fileId));

            return $image;
        }

        return $this->inputFilter->getMessages();
    }
}
