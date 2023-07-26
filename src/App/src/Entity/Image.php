<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Imagick;

use function base64_encode;
use function implode;
use function json_decode;
use function sprintf;
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
    /** @var resource Stores the image's data */
    private $data;

    #[ORM\Column(name: 'width', type: Types::INTEGER, unique: false, nullable: true)]
    private ?int $width;

    #[ORM\Column(name: 'height', type: Types::INTEGER, unique: false, nullable: true)]
    private ?int $height;

    #[ORM\Column(name: 'density', type: Types::JSON, unique: false, nullable: true)]
    private ?string $density;

    #[ORM\Column(name: 'orientation', type: Types::INTEGER, unique: false, nullable: true)]
    private ?int $orientation;

    #[ORM\Column(name: 'format', type: Types::STRING, unique: false, nullable: true)]
    private ?string $format;

    #[ORM\Column(name: 'depth', type: Types::STRING, unique: false, nullable: true)]
    private ?int $depth;

    #[ORM\Column(name: 'colour_space', type: Types::STRING, unique: false, nullable: true)]
    private ?int $colourSpace;

    #[ORM\Column(name: 'size', type: Types::INTEGER, unique: false, nullable: true)]
    private ?int $size;

    /**
     * Add the following information
     *
     * - Image size, width and height, density, properties, orientation, length, depth,
     * format, colour model, & ColorSync Profile.
     */
    public function __construct(
        ?string $name,
        ?string $data,
        ?int $height = null,
        ?int $width = null,
        ?string $density = null,
        ?string $format = null,
        ?int $depth = null,
        ?int $colourSpace = null,
        ?int $size = null,
        ?int $id = null,
    ) {
        $this->id          = $id;
        $this->name        = $name;
        $this->data        = $data;
        $this->height      = $height;
        $this->width       = $width;
        $this->density     = $density;
        $this->format      = $format;
        $this->depth       = $depth;
        $this->colourSpace = $colourSpace;
        $this->size        = $size;
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

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function getDimensions(): string
    {
        return sprintf(
            '%d x %d',
            $this->getWidth() ?? 0,
            $this->getHeight() ?? 0,
        );
    }

    public function getDensity(): ?string
    {
        return implode('x', json_decode($this->density));
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function getDepth(): ?int
    {
        return $this->depth;
    }

    public function getColourSpace(): string
    {
        return match ($this->colourSpace) {
            Imagick::COLORSPACE_CMY => 'CMY',
            Imagick::COLORSPACE_CMYK => 'CMYK',
            Imagick::COLORSPACE_GRAY => 'Gray',
            Imagick::COLORSPACE_HSB => 'HSB',
            Imagick::COLORSPACE_HSL => 'HSL',
            Imagick::COLORSPACE_HWB => 'HWB',
            Imagick::COLORSPACE_OHTA => 'OHTA',
            Imagick::COLORSPACE_RGB => 'RGB',
            Imagick::COLORSPACE_SRGB => 'SRGB',
            Imagick::COLORSPACE_TRANSPARENT => 'Transparent',
            Imagick::COLORSPACE_XYZ => 'XYZ',
            Imagick::COLORSPACE_YCBCR => 'YCBCR',
            Imagick::COLORSPACE_YCC => 'YCC',
            Imagick::COLORSPACE_YIQ => 'YIQ',
            Imagick::COLORSPACE_YPBPR => 'YPBPR',
            Imagick::COLORSPACE_YUV => 'YUV',
            default => 'Unknown',
        };
    }

    public function __toArray(): array
    {
        return [
            'colorSpace' => $this->getColourSpace(),
            'data'       => $this->getData(),
            'depth'      => $this->getDepth(),
            'format'     => $this->getFormat(),
            'height'     => $this->getHeight(),
            'id'         => $this->getId(),
            'name'       => $this->getName(),
            'size'       => $this->getSize(),
            'width'      => $this->getWidth(),
        ];
    }
}
