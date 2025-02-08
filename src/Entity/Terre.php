<?php
// src/Entity/Terre.php
namespace App\Entity;

use App\Repository\TerreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TerreRepository::class)]
class Terre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $gouvernorat = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7)]
    private ?float $latitude = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7)]
    private ?float $longitude = null;

    #[ORM\Column(type: 'float')]
    private ?float $superficie = null;

    #[ORM\OneToMany(mappedBy: 'terre', targetEntity: Parcelle::class, orphanRemoval: true)]
    private Collection $parcelles;

    public function __construct()
    {
        $this->parcelles = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getGouvernorat(): ?string { return $this->gouvernorat; }
    public function setGouvernorat(string $gouvernorat): self { $this->gouvernorat = $gouvernorat; return $this; }

    public function getLatitude(): ?float { return $this->latitude; }
    public function setLatitude(float $latitude): self { $this->latitude = $latitude; return $this; }

    public function getLongitude(): ?float { return $this->longitude; }
    public function setLongitude(float $longitude): self { $this->longitude = $longitude; return $this; }

    public function getSuperficie(): ?float { return $this->superficie; }
    public function setSuperficie(float $superficie): self { $this->superficie = $superficie; return $this; }

    public function getParcelles(): Collection { return $this->parcelles; }

    public function addParcelle(Parcelle $parcelle): self
    {
        if (!$this->parcelles->contains($parcelle)) {
            $this->parcelles->add($parcelle);
            $parcelle->setTerre($this);
        }
        return $this;
    }

    public function removeParcelle(Parcelle $parcelle): self
    {
        if ($this->parcelles->removeElement($parcelle)) {
            if ($parcelle->getTerre() === $this) {
                $parcelle->setTerre(null);
            }
        }
        return $this;
    }
}
