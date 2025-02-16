<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: "parcelle")]
class Parcelle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de la parcelle est requis.")]
    private ?string $nom = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    #[Assert\NotBlank(message: "La superficie est requise.")]
    #[Assert\Positive(message: "La superficie doit être un nombre positif.")]
    private ?float $superficie = null;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private ?bool $plantee = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $typeCulture = null;

    #[ORM\ManyToOne(targetEntity: Terre::class, inversedBy: 'parcelles')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Terre $terre = null;

    #[ORM\OneToMany(targetEntity: Capteur::class, mappedBy: 'parcelle', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $capteurs;

    #[ORM\Column(type: "integer", options: ["default" => 0])]
    #[Assert\PositiveOrZero(message: "Le nombre de capteurs doit être un nombre positif.")]
    private int $nombreCapteurs = 0;

    public function __construct()
    {
        $this->capteurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getSuperficie(): ?float
    {
        return $this->superficie;
    }

    public function setSuperficie(float $superficie): self
    {
        $this->superficie = round($superficie, 2);
        return $this;
    }

    public function isPlantee(): ?bool
    {
        return $this->plantee;
    }

    public function setPlantee(bool $plantee): self
    {
        $this->plantee = $plantee;
        return $this;
    }

    public function getTypeCulture(): ?string
    {
        return $this->typeCulture;
    }

    public function setTypeCulture(?string $typeCulture): self
    {
        $this->typeCulture = $typeCulture;
        return $this;
    }

    public function getTerre(): ?Terre
    {
        return $this->terre;
    }

    public function setTerre(?Terre $terre): self
    {
        $this->terre = $terre;
        return $this;
    }

    /**
     * @return Collection<int, Capteur>
     */
    public function getCapteurs(): Collection
    {
        return $this->capteurs;
    }

    public function addCapteur(Capteur $capteur): self
    {
        if (!$this->capteurs->contains($capteur)) {
            $this->capteurs->add($capteur);
            $capteur->setParcelle($this);
        }
        return $this;
    }

    public function removeCapteur(Capteur $capteur): self
    {
        if ($this->capteurs->removeElement($capteur)) {
            if ($capteur->getParcelle() === $this) {
                $capteur->setParcelle(null);
            }
        }
        return $this;
    }

    public function getNombreCapteurs(): int
    {
        return $this->nombreCapteurs;
    }

    public function setNombreCapteurs(int $nombreCapteurs): self
    {
        $this->nombreCapteurs = $nombreCapteurs;
        return $this;
    }

    /**
     * Retourne la somme des superficies des parcelles associées à cette Terre.
     */
    public function getSuperficieTotal(): float
    {
        $somme = 0;
        foreach ($this->terre->getParcelles() as $parcelle) {
            $somme += $parcelle->getSuperficie();
        }
        return round($somme, 2);
    }
}
