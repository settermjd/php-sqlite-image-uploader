<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Image;
use Doctrine\ORM\EntityManager;
use Imagick;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\UploadedFileFactory;
use Laminas\Filter\File\RenameUpload;
use Laminas\InputFilter\FileInput;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\File\UploadFile;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

use function array_merge_recursive;
use function unlink;

class UploadHandler implements RequestHandlerInterface
{
    public function __construct(
        private EntityManager $entityManager,
        private LoggerInterface $logger
    ) {
        $file = new FileInput('file');
        $file
            ->getValidatorChain()
            ->attach(new UploadFile());
        $file
            ->getFilterChain()
            ->attach(new RenameUpload([
                'overwrite'            => true,
                'randomize'            => true,
                'stream_factory'       => new StreamFactory(),
                'target'               => __DIR__ . '/../../../../data/uploads/',
                'upload_file_factory'  => new UploadedFileFactory(),
                'use_upload_extension' => true,
                'use_upload_name'      => true,
            ]));

        $this->inputFilter = new InputFilter();
        $this->inputFilter->add($file);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $post = array_merge_recursive(
            $request->getParsedBody(),
            $request->getUploadedFiles()
        );
        $this->inputFilter->setData($post);

        if ($this->inputFilter->isValid()) {
            /** @var UploadedFileInterface $uploadedImage */
            $uploadedImage = $this->inputFilter->getValue('file');
            $fileName      = $uploadedImage->getStream()->getMetadata("uri");
            $this->logger->debug('Uploaded Image', [$fileName]);
            $imagick = new Imagick($fileName);
            $image   = new Image(
                name: $uploadedImage->getClientFilename(),
                data: $imagick->getImageBlob(),
            );
            $this->entityManager->persist($image);
            $this->entityManager->flush();
            unlink($fileName);

            return new RedirectResponse('/');
        }

        return new EmptyResponse(500);
    }
}
