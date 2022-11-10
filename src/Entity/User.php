<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Un compte a déjà été créé avec cet email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $active = false;
    
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function addCandidate(Candidate $candidate): self
    {
        if (!$this->Candidates->contains($candidate)) {
            $this->Candidates->add($candidate);
            $candidate->setUser($this);
        }

        return $this;
    }

    public function removeCandidate(Candidate $candidate): self
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