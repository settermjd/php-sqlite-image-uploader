<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ORM\Table(name: 'image')]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id', type: Types::INTEGER, unique: true, nullable: false)]
    protected int|null $id = null;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 200, unique: true, nullable: false)]
    private ?string $name;

    #[ORM\Column(name: 'data', type: Types::BLOB, unique: true, nullable: false)]
    private ?string $data;

    public function __construct(
        ?string $name,
        ?string $data,
    ) {
        $this->name = $name;
        $this->data = $data;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

}
