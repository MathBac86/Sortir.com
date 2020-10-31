<?php

namespace App\Entity;

use App\Repository\InscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InscriptionRepository::class)
 */
class Inscription
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Sortie::class, inversedBy="inscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Sortie;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Participant::class, inversedBy="inscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Participant;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateInscription;

    public function getSortie(): ?Sortie
    {
        return $this->Sortie;
    }

    public function setSortie(?Sortie $Sortie): self
    {
        $this->Sortie = $Sortie;

        return $this;
    }

    public function getParticipant(): ?Participant
    {
        return $this->Participant;
    }

    public function setParticipant(?Participant $Participant): self
    {
        $this->Participant = $Participant;

        return $this;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeInterface $dateInscription): self
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }
}
