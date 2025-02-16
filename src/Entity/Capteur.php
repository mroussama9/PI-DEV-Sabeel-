<?php
// src/Entity/Capteur.php
namespace App\Entity;

use App\Repository\CapteurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CapteurRepository::class)]
class Capteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom du capteur est requis.")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le type de capteur est requis.")]
    private ?string $type = null;

    #[ORM\Column(type: "float", nullable: true)]
    #[Assert\Type(type: "float", message: "La valeur doit être un nombre.")]
    private ?float $valeur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $topic = null;

    #[ORM\ManyToOne(targetEntity: Parcelle::class, inversedBy: 'capteurs')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Parcelle $parcelle = null;

    // Getters et setters
    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getType(): ?string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }

    public function getValeur(): ?float { return $this->valeur; }
    public function setValeur(?float $valeur): self { $this->valeur = $valeur; return $this; }

    public function getTopic(): ?string { return $this->topic; }
    public function setTopic(?string $topic): self { $this->topic = $topic; return $this; }

    public function getParcelle(): ?Parcelle { return $this->parcelle; }
    public function setParcelle(?Parcelle $parcelle): self { $this->parcelle = $parcelle; return $this; }
}
