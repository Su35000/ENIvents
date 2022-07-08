<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $libelle_role;

    #[ORM\OneToMany(mappedBy: 'role', targetEntity: Participant::class, orphanRemoval: true)]
    private $participant;

    public function __construct()
    {
        $this->participant = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleRole(): ?string
    {
        return $this->libelle_role;
    }

    public function setLibelleRole(string $libelle_role): self
    {
        $this->libelle_role = $libelle_role;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipant(): Collection
    {
        return $this->participant;
    }

    public function addParticipant(Participant $participant): self
    {
        if (!$this->participant->contains($participant)) {
            $this->participant[] = $participant;
            $participant->setRole($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        if ($this->participant->removeElement($participant)) {
            // set the owning side to null (unless already changed)
            if ($participant->getRole() === $this) {
                $participant->setRole(null);
            }
        }

        return $this;
    }
}
