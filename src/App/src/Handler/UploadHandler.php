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
use Laminas\Validator\File\FilesSize;
use Laminas\Validator\File\IsImage;
use Laminas\Validator\File\MimeType;
use Laminas\Validator\File\UploadFile;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

use function array_merge_recursive;
use function unlink;

class UploadHandler implements RequestHandlerInterface
{
    private InputFilter $inputFilter;

    public function __construct(
        private EntityManager $entityManager,
        private LoggerInterface $logger
    ) {
        $file = new FileInput('file');

        // Add validators
        $file
            ->getValidatorChain()
            // Ensure that the file is an uploaded file
            ->attach(new UploadFile())
            // Ensure that the uploaded file is an image
            ->attach(new IsImage())
            // Limit the file to a maximum size of 5MB
            ->attach(new FilesSize([
                'max' => '5MB',
            ]))
            // Restrict the allowed file types
            ->attach(new MimeType([
                'image/avif',
                'image/gif',
                'image/jpeg',
                'image/png',
                'image/webp',
            ]));

        // Add filters
        $file
            ->getFilterChain()
            // Move and rename the uploaded file after it is uploaded
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
        $this->logger->debug("POST data", $post);

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

        $this->logger->error(
            "There were POST validation issues.",
            $this->inputFilter->getMessages()
        );

        return new EmptyResponse(500);
    }
}
