<?php

namespace App\Entity;

use App\Entity\Enum\UserRole;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity('email', message: 'error.email.unique')]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $email;

    /**
     * @var string[] $roles
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\OneToMany(mappedBy: 'attendee', targetEntity: Meeting::class)]
    #[ORM\OrderBy(['timeSlot' => 'DESC'])]
    private Collection $meetings;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $firstname;

    #[ORM\Column(type: 'string', nullable: true)]
    private Uuid|string|null $bbbMeetingId;

    public function __construct()
    {
        $this->meetings = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->firstname;
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
        $roles[] = UserRole::ROLE_USER->name;

        return array_unique($roles);
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(UserRole $role): self
    {
        $this->roles[] = $role->name;

        return $this;
    }

    public function isAdmin(): bool
    {
        $roles = $this->getRoles();
        return in_array(UserRole::ROLE_ADMIN->name, $roles);
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getMeetings(): Collection
    {
        return $this->meetings;
    }

    public function addMeeting(Meeting $meeting): self
    {
        if (!$this->meetings->contains($meeting)) {
            $this->meetings[] = $meeting;
            $meeting->setAttendee($this);
        }

        return $this;
    }

    public function removeMeeting(Meeting $meeting): self
    {
        if ($this->meetings->removeElement($meeting)) {
            // set the owning side to null (unless already changed)
            if ($meeting->getAttendee() === $this) {
                $meeting->setAttendee(null);
            }
        }

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getBbbMeetingId(): Uuid|string|null
    {
        try {
            return Uuid::fromString($this->bbbMeetingId);
        } catch (InvalidArgumentException $e) {
            return $this->bbbMeetingId;
        }
    }

    public function setBbbMeetingId(Uuid|string|null $bbbMeetingId): self
    {
        $this->bbbMeetingId = $bbbMeetingId;

        return $this;
    }
}
