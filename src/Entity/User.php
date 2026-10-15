<?php

namespace App\Entity;

use App\Enum\UserRole;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\UnicodeString;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'users.validation.email.unique')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: Types::INTEGER, nullable: false)]
    protected int $id;

    /** @var string The user's email */
    #[ORM\Column(name: 'email', type: Types::STRING, length: 180, unique: true, nullable: false)]
    #[Assert\NotBlank(message: 'users.validation.email.not_blank')]
    #[Assert\Email(message: 'users.validation.email.invalid')]
    #[Assert\Length(
        max: 180,
        maxMessage: 'users.validation.email.max_length',
        min: 5,
        minMessage: 'users.validation.email.min_length',
    )]
    private string $email;
    #[ORM\Column(name: 'role', type: Types::STRING, length: 20, nullable: false, enumType: UserRole::class)]
    private UserRole $role = UserRole::ROLE_USER;

    /**
     * @var string The hashed password
     */
    #[ORM\Column(name: 'password', type: Types::STRING, nullable: false)]
    #[Assert\NotBlank(message: 'users.validation.password.not_blank')]
    #[Assert\PasswordStrength(
        minScore: 1,
        message: 'users.validation.password.complexity',
    )]
    private string $password;

    #[ORM\Column(name: 'first_name', type: Types::STRING, length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'users.validation.first_name.not_blank')]
    #[Assert\Length(
        min: 3,
        minMessage: 'users.validation.first_name.min_length',
        max: 255,
        maxMessage: 'users.validation.first_name.max_length',
    )]
    private ?string $firstName = null;

    #[ORM\Column(name: 'last_name', type: Types::STRING, length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'users.validation.last_name.not_blank')]
    #[Assert\Length(
        min: 3,
        minMessage: 'users.validation.last_name.min_length',
        max: 255,
        maxMessage: 'users.validation.last_name.max_length',
    )]
    private ?string $lastName = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Picture $picture = null;

    public function __construct()
    {
        $this->role = UserRole::ROLE_USER;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = trim($email);

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        if ('' === $this->email) {
            throw new \LogicException('The user email must not be empty.');
        }

        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        // Symfony expects an array of strings; expose the enum value.
        return [$this->role->value];
    }

    public function getRole(): UserRole
    {
        return $this->role;
    }

    public function setRole(?UserRole $role): static
    {
        $this->role = $role ?? UserRole::ROLE_USER;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPicture(): ?Picture
    {
        return $this->picture;
    }

    public function setPicture(?Picture $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getFullName(): ?string
    {
        $firstName = (new UnicodeString($this->getFirstName() ?? ''))->title()->toString();
        $lastName = (new UnicodeString($this->getLastName() ?? ''))->title()->toString();

        return trim(sprintf('%s %s', $firstName, $lastName)) ?: null;
    }
}
