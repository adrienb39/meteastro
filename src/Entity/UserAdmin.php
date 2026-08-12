<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users_admin')]
class UserAdmin
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_user', type: 'integer')]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(name: 'nom_user', type: 'string', length: 50)]
    private string $nom;

    #[ORM\Column(name: 'prenom_user', type: 'string', length: 50)]
    private string $prenom;

    #[ORM\Column(name: 'email_user', type: 'string', length: 100, unique: true)]
    private string $email;

    #[ORM\Column(name: 'password_user', type: 'string', length: 200)]
    private ?string $password = null;

    #[ORM\Column(name: 'admin', type: 'boolean')]
    private ?bool $admin = null;

    // #[ORM\Column(name: 'droits_pages_contenu', type: 'boolean')]
    // private ?bool $droitsPagesContenu = null;

    // #[ORM\Column(name: 'droits_codep_jura', type: 'boolean')]
    // private bool $droitsCodepJura;

    // #[ORM\Column(name: 'droits_cyclo_club_des_monts_de_plasne', type: 'boolean')]
    // private bool $droitsCycloClubDesMontsDePlasne;

    #[ORM\Column(name: 'temporary_code', type: 'string', length: 20, nullable: true)]
    private ?string $temporaryCode = null;

    #[ORM\Column(name: 'code_expiration', type: 'datetime', nullable: true)]
    private ?\DateTime $codeExpiration = null;

    #[ORM\Column(name: 'is_active', type: 'boolean')]
    private bool $isActive = false;  // Indiquer si l'utilisateur est actif ou non

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(name: 'role_id', referencedColumnName: 'id_role')]
    private Role $role;

    public function __construct()
    {
        $this->password = '';
    }

    // Getters and Setters

    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getAdmin(): ?bool
    {
        return $this->admin;
    }

    // public function getDroitsPagesContenu(): ?bool {
    //     return $this->droitsPagesContenu;
    // }

    // public function getDroitsCodepJura(): bool {
    //     return $this->droitsCodepJura;
    // }

    // public function setDroitsCodepJura(bool $droitsCodepJura): void {
    //     $this->droitsCodepJura = $droitsCodepJura;
    // }

    // public function getDroitsCycloClubDesMontsDePlasne(): bool {
    //     return $this->droitsCycloClubDesMontsDePlasne;
    // }

    // public function setDroitsCycloClubDesMontsDePlasne(bool $droitsCycloClubDesMontsDePlasne): void {
    //     $this->droitsCycloClubDesMontsDePlasne = $droitsCycloClubDesMontsDePlasne;
    // }

    public function getTemporaryCode(): ?string
    {
        return $this->temporaryCode;
    }

    public function setTemporaryCode(?string $temporaryCode): void
    {
        $this->temporaryCode = $temporaryCode;
    }

    public function getCodeExpiration(): ?\DateTime
    {
        return $this->codeExpiration;
    }

    public function setCodeExpiration(?\DateTime $codeExpiration): void
    {
        $this->codeExpiration = $codeExpiration;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function setAdmin(?bool $admin): void
    {
        $this->admin = $admin;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function setRole(Role $role): void
    {
        $this->role = $role;
    }
}