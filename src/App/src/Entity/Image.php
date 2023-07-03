<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use function base64_encode;
use function stream_get_contents;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ORM\Table(name: 'image')]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id', type: Types::INTEGER, unique: true, nullable: false)]
    protected int|null $id;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 200, unique: true, nullable: false)]
    private ?string $name;

    #[ORM\Column(name: 'data', type: Types::BLOB, unique: true, nullable: false)]
    /** @var string Stores the image's data */
    private $data;

    public function __construct(
        ?string $name,
        ?string $data,
        ?int $id = null,
    ) {
        $this->id   = $id;
        $this->name = $name;
        $this->data = $data;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getData(): ?string
    {
        if ($this->data !== null) {
            return base64_encode(stream_get_contents($this->data));
        }

        return $this->data;
    }
}
