<?php

namespace App\Entity;

use App\Repository\NonReservationEncaisseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NonReservationEncaisseRepository::class)]
class NonReservationEncaisse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private array $article = [];

    #[ORM\Column]
    private ?int $couverts = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $total = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $marge = null;

    #[ORM\Column]
    private ?bool $midi = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getArticle(): array
    {
        return $this->article;
    }

    public function setArticle(array $article): static
    {
        $this->article = $article;

        return $this;
    }

    public function getCouverts(): ?int
    {
        return $this->couverts;
    }

    public function setCouverts(int $couverts): static
    {
        $this->couverts = $couverts;

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getMarge(): ?string
    {
        return $this->marge;
    }

    public function setMarge(string $marge): static
    {
        $this->marge = $marge;

        return $this;
    }

    public function isMidi(): ?bool
    {
        return $this->midi;
    }

    public function setMidi(bool $midi): static
    {
        $this->midi = $midi;

        return $this;
    }
}
