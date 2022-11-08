<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column()]
    private array $roles = [];

    #[ORM\Column(length: 100)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Recruiter::class)]
    private Collection $Recruiters;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Candidate::class)]
    private Collection $Candidates;

    public function __construct()
    {
        $this->Recruiters = new ArrayCollection();
        $this->Candidates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, Recruiter>
     */
    public function getRecruiters(): Collection
    {
        return $this->Recruiters;
    }

    public function addRecruiter(Recruiter $recruiter): self
    {
        if (!$this->Recruiters->contains($recruiter)) {
            $this->Recruiters->add($recruiter);
            $recruiter->setUser($this);
        }

        return $this;
    }

    public function removeRecruiter(Recruiter $recruiter): self
    {
        if ($this->Recruiters->removeElement($recruiter)) {
            // set the owning side to null (unless already changed)
            if ($recruiter->getUser() === $this) {
                $recruiter->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Candidate>
     */
    public function getCandidates(): Collection
    {
        return $this->Candidates;
    }

    public function addCandidates(Candidate $candidate): self
    {
        if (!$this->Candidates->contains($candidate)) {
            $this->Candidates->add($candidate);
            $candidate->setUser($this);
        }

        return $this;
    }

    public function removeCandidates(Candidate $candidate): self
    {
        if ($this->Candidates->removeElement($candidate)) {
            // set the owning side to null (unless already changed)
            if ($candidate->getUser() === $this) {
                $candidate->setUser(null);
            }
        }

        return $this;
    }
}