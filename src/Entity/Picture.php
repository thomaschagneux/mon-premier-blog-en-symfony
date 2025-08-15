<?php

namespace App\Entity;

use App\Repository\PictureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PictureRepository::class)]
class Picture extends AbstractEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: Types::INTEGER, nullable: false)]
    protected int $id;

    #[ORM\Column(name: 'file_name', type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 1,
        max: 255,
    )
    ]
    private string $fileName;

    #[ORM\Column(name: 'path_name', type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 1,
        max: 255,
    )
    ]
    private string $pathName;

    #[ORM\Column(name: 'mime_type', type: Types::STRING, length: 255, nullable: true)]
    #[Assert\Length(
        min: 1,
        max: 255,
    )
    ]
    private ?string $mimeType = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    public function getPathName(): string
    {
        return $this->pathName;
    }

    public function setPathName(string $pathName): void
    {
        $this->pathName = $pathName;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }
}
