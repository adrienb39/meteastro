<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'astronomie')]
class Astronomie
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: 'id', type: "integer")]
    private int $id;

    #[ORM\Column(name: 'title', type: "string", length: 100)]
    private string $title;

    #[ORM\Column(name: 'title_contenu', type: "string", length: 100)]
    private string $titleContenu;

    #[ORM\Column(name: 'contenu', type: "string")]
    private string $contenu;

    #[ORM\Column(name: 'filename', type: "string", length: 200)]
    private string $filename;

    #[ORM\Column(name: 'background_img', type: "string", length: 255, nullable: true)]
    private ?string $backgroundImg;

    #[ORM\Column(name: 'gallery_images', type: "string", nullable: true)]
    private ?string $galleryImages;

    #[ORM\Column(name: 'music_file', type: "string", nullable: true)]
    private ?string $musicFile;

    #[ORM\Column(name: 'show_images', type: "boolean")]
    private bool $showImages = 1;

    #[ORM\Column(name: 'enable_music', type: "boolean")]
    private bool $enableMusic = 1;

    #[ORM\Column(name: 'background_mode', type: "string", length: 20)]
    private string $backgroundMode = 'animated';

    #[ORM\Column(name: 'hud_feed_id', type: "string", length: 100, nullable: true)]
    private ?string $hudFeedId;

    #[ORM\Column(name: 'verified', type: "boolean")]
    private bool $verified;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'id_users', referencedColumnName: 'id')]
    private User $user;

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitleContenu(): string
    {
        return $this->titleContenu;
    }

    public function setTitleContenu(string $titleContenu): void
    {
        $this->titleContenu = $titleContenu;
    }

    public function getContenu(): string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): void
    {
        $this->contenu = $contenu;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    public function getBackgroundImg(): ?string
    {
        return $this->backgroundImg;
    }

    public function setBackgroundImg(?string $backgroundImg): void
    {
        $this->backgroundImg = $backgroundImg;
    }

    public function getGalleryImages(): ?string
    {
        return $this->galleryImages;
    }

    public function setGalleryImages(?string $galleryImages): void
    {
        $this->galleryImages = $galleryImages;
    }

    public function getMusicFile(): ?string
    {
        return $this->musicFile;
    }

    public function setMusicFile(?string $musicFile): void
    {
        $this->musicFile = $musicFile;
    }

     public function getShowImages(): bool
    {
        return $this->showImages;
    }

    public function setShowImages(bool $showImages): void
    {
        $this->showImages = $showImages;
    }

    public function getEnableMusic(): bool
    {
        return $this->enableMusic;
    }

    public function setEnableMusic(bool $enableMusic): void
    {
        $this->enableMusic = $enableMusic;
    }

    public function getBackgroundMode(): string
    {
        return $this->backgroundMode;
    }

    public function setBackgroundMode(string $backgroundMode): void
    {
        $this->backgroundMode = $backgroundMode;
    }

    public function getHudFeedId(): ?string
    {
        return $this->hudFeedId;
    }

    public function setHudFeedId(?string $hudFeedId): void
    {
        $this->hudFeedId = $hudFeedId;
    }

    public function getVerified(): bool
    {
        return $this->verified;
    }

    public function setVerified(bool $verified): void
    {
        $this->verified = $verified;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}