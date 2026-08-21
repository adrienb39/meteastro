<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: 'id_users', type: "integer")]
    private int $id;

    #[ORM\Column(name: 'name', type: "string", length: 255)]
    private string $name;

    #[ORM\Column(name: 'email', type: "string", length: 255)]
    private string $email;

    #[ORM\Column(name: 'password', type: "string", length: 255)]
    private string $password;

    #[ORM\Column(name: 'code', type: "string", length: 50)]
    private string $code;

    #[ORM\Column(name: 'status', type: "string")]
    private string $status;

    #[ORM\Column(name: 'newsletter', type: "integer", nullable: true)]
    private ?int $newsletter = null;

    #[ORM\Column(name: 'last_version', type: "string", length: 10, nullable: true)]
    private ?string $lastVersion = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getNewsletter(): ?int
    {
        return $this->newsletter;
    }

    public function setNewsletter(?int $newsletter): void
    {
        $this->newsletter = $newsletter;
    }

    public function getLastVersion(): ?string
    {
        return $this->lastVersion;
    }

    public function setLastVersion(?string $lastVersion): void
    {
        $this->lastVersion = $lastVersion;
    }
}