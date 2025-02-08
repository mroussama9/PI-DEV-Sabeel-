<?php
// src/Entity/Parcelle.php
namespace App\Entity;

use App\Repository\ParcelleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParcelleRepository::class)]
class Parcelle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Terre::class, inversedBy: 'parcelles')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Terre $terre = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: 'float')]
    private ?float $superficie = null;

    public function getId(): ?int { return $this->id; }

    public function getTerre(): ?Terre { return $this->terre; }
    public function setTerre(?Terre $terre): self { $this->terre = $terre; return $this; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getSuperficie(): ?float { return $this->superficie; }
    public function setSuperficie(float $superficie): self { $this->superficie = $superficie; return $this; }
}
