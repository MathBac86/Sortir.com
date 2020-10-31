<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VilleRepository::class)
 */
class Ville
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $nomVille;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $codePostal;

    /**
     * @ORM\OneToMany(targetEntity=Lieux::class, mappedBy="ville")
     */
    private $lieuxes;

    public function __construct()
    {
        $this->lieuxes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomVille(): ?string
    {
        return $this->nomVille;
    }

    public function setNomVille(string $nomVille): self
    {
        $this->nomVille = $nomVille;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * @return Collection|Lieux[]
     */
    public function getLieuxes(): Collection
    {
        return $this->lieuxes;
    }

    public function addLieux(Lieux $lieux): self
    {
        if (!$this->lieuxes->contains($lieux)) {
            $this->lieuxes[] = $lieux;
            $lieux->setVille($this);
        }

        return $this;
    }

    public function removeLieux(Lieux $lieux): self
    {
        if ($this->lieuxes->contains($lieux)) {
            $this->lieuxes->removeElement($lieux);
            // set the owning side to null (unless already changed)
            if ($lieux->getVille() === $this) {
                $lieux->setVille(null);
            }
        }

        return $this;
    }
}
