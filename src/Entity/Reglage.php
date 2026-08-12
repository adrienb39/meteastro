<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'site_settings')]
class Reglage
{
    // ID fixe à 1. Pas de génération automatique (pas de #[ORM\GeneratedValue]).
    #[ORM\Id]
    #[ORM\Column(name: 'id_setting', type: 'integer')]
    private int $id = 1;

    // --- Paramètres Généraux ---
    
    #[ORM\Column(name: 'site_name', type: 'string', length: 50)]
    private string $siteName;

    #[ORM\Column(name: 'image_fond_filename', type: 'string', length: 255, nullable: true)]
    private ?string $imageFondFilename = null;

    #[ORM\Column(name: 'logo_filename', type: 'string', length: 255, nullable: true)]
    private ?string $logoFilename = null;
    
    // --- Coordonnées du Club / Président ---

    #[ORM\Column(name: 'president_nom', type: 'string', length: 50)]
    private string $presidentNom;

    #[ORM\Column(name: 'president_adresse_rue', type: 'string', length: 50)]
    private string $presidentAdresseRue;

    #[ORM\Column(name: 'president_adresse_cp_ville', type: 'string', length: 150)]
    private string $presidentAdresseCpVille;

    #[ORM\Column(name: 'contact_email', type: 'string', length: 50)]
    private string $contactEmail;

    #[ORM\Column(name: 'contact_phone', type: 'string', length: 20)]
    private string $contactPhone;

    // --- Partenaire Principal (Jura Cycles) ---

    #[ORM\Column(name: 'partenaire_1_nom', type: 'string', length: 100, nullable: true)]
    private ?string $partenaire1Nom = null;

    #[ORM\Column(name: 'partenaire_1_url', type: 'string', length: 255, nullable: true)]
    private ?string $partenaire1Url = null;

    #[ORM\Column(name: 'partenaire_1_adresse_rue', type: 'string', length: 150, nullable: true)]
    private ?string $partenaire1AdresseRue = null;

    #[ORM\Column(name: 'partenaire_1_adresse_cp_ville', type: 'string', length: 150, nullable: true)]
    private ?string $partenaire1AdresseCpVille = null;

    #[ORM\Column(name: 'partenaire_1_tel', type: 'string', length: 20, nullable: true)]
    private ?string $partenaire1Tel = null;

    // --- Réseaux Sociaux ---

    #[ORM\Column(name: 'social_facebook_url', type: 'string', length: 255, nullable: true)]
    private ?string $socialFacebookUrl = null;

    #[ORM\Column(name: 'social_youtube_url', type: 'string', length: 255, nullable: true)]
    private ?string $socialYoutubeUrl = null;

    // --- Fédérations/Partenaires Institutionnels ---
    
    #[ORM\Column(name: 'ffvelo_url', type: 'string', length: 255, nullable: true)]
    private ?string $ffveloUrl = null;

    #[ORM\Column(name: 'codep39_url', type: 'string', length: 255, nullable: true)]
    private ?string $codep39Url = null;

    #[ORM\Column(name: 'theme_text_color', type: 'string', length: 20)]
    private string $themeTextColor;

    #[ORM\Column(name: 'theme_fond_color', type: 'string', length: 20)]
    private string $themeFondColor;

    #[ORM\Column(name: 'page_fond_color', type: 'string', length: 20)]
    private string $pageFondColor;

    #[ORM\Column(name: 'page_fond_transparent', type: 'boolean')]
    private bool $pageFondTransparent;
    
    // --- Constructeur ---

    public function __construct()
    {
        $this->id = 1;
    }

    // --- Getters et Setters ---

    public function getId(): int
    {
        return $this->id;
    }

    // Paramètres Généraux
    public function getSiteName(): string {
        return $this->siteName;
    }

    public function setSiteName(string $siteName): void {
        $this->siteName = $siteName;
    }

    public function getImageFondFilename(): ?string {
        return $this->imageFondFilename;
    }

    public function setImageFondFilename(?string $imageFondFilename): void {
        $this->imageFondFilename = $imageFondFilename;
    }

    public function getLogoFilename(): ?string {
        return $this->logoFilename;
    }

    public function setLogoFilename(?string $logoFilename): void {
        $this->logoFilename = $logoFilename;
    }

    // Coordonnées du Président
    public function getPresidentNom(): string {
        return $this->presidentNom;
    }

    public function setPresidentNom(string $presidentNom): void {
        $this->presidentNom = $presidentNom;
    }

    public function getPresidentAdresseRue(): string {
        return $this->presidentAdresseRue;
    }

    public function setPresidentAdresseRue(string $presidentAdresseRue): void {
        $this->presidentAdresseRue = $presidentAdresseRue;
    }

    public function getPresidentAdresseCpVille(): string {
        return $this->presidentAdresseCpVille;
    }

    public function setPresidentAdresseCpVille(string $presidentAdresseCpVille): void {
        $this->presidentAdresseCpVille = $presidentAdresseCpVille;
    }

    public function getContactEmail(): string {
        return $this->contactEmail;
    }

    public function setContactEmail(string $contactEmail): void {
        $this->contactEmail = $contactEmail;
    }

    public function getContactPhone(): string {
        return $this->contactPhone;
    }

    public function setContactPhone(string $contactPhone): void {
        $this->contactPhone = $contactPhone;
    }

    // Partenaire Principal (Jura Cycles)
    public function getPartenaire1Nom(): ?string {
        return $this->partenaire1Nom;
    }

    public function setPartenaire1Nom(?string $partenaire1Nom): void {
        $this->partenaire1Nom = $partenaire1Nom;
    }
    
    public function getPartenaire1Url(): ?string {
        return $this->partenaire1Url;
    }

    public function setPartenaire1Url(?string $partenaire1Url): void {
        $this->partenaire1Url = $partenaire1Url;
    }

    public function getPartenaire1AdresseRue(): ?string {
        return $this->partenaire1AdresseRue;
    }

    public function setPartenaire1AdresseRue(?string $partenaire1AdresseRue): void {
        $this->partenaire1AdresseRue = $partenaire1AdresseRue;
    }

    public function getPartenaire1AdresseCpVille(): ?string {
        return $this->partenaire1AdresseCpVille;
    }

    public function setPartenaire1AdresseCpVille(?string $partenaire1AdresseCpVille): void {
        $this->partenaire1AdresseCpVille = $partenaire1AdresseCpVille;
    }

    public function getPartenaire1Tel(): ?string {
        return $this->partenaire1Tel;
    }

    public function setPartenaire1Tel(?string $partenaire1Tel): void {
        $this->partenaire1Tel = $partenaire1Tel;
    }
    
    // Réseaux Sociaux
    public function getSocialFacebookUrl(): ?string {
        return $this->socialFacebookUrl;
    }

    public function setSocialFacebookUrl(?string $socialFacebookUrl): void {
        $this->socialFacebookUrl = $socialFacebookUrl;
    }

    public function getSocialYoutubeUrl(): ?string {
        return $this->socialYoutubeUrl;
    }

    public function setSocialYoutubeUrl(?string $socialYoutubeUrl): void {
        $this->socialYoutubeUrl = $socialYoutubeUrl;
    }
    
    // Fédérations/Partenaires Institutionnels
    public function getFfveloUrl(): ?string {
        return $this->ffveloUrl;
    }

    public function setFfveloUrl(?string $ffveloUrl): void {
        $this->ffveloUrl = $ffveloUrl;
    }

    public function getCodep39Url(): ?string {
        return $this->codep39Url;
    }

    public function setCodep39Url(?string $codep39Url): void {
        $this->codep39Url = $codep39Url;
    }

    public function getThemeTextColor(): string {
        return $this->themeTextColor;
    }

    public function setThemeTextColor(string $themeTextColor): void {
        $this->themeTextColor = $themeTextColor;
    }

    public function getThemeFondColor(): string {
        return $this->themeFondColor;
    }

    public function setThemeFondColor(string $themeFondColor): void {
        $this->themeFondColor = $themeFondColor;
    }

    public function getPageFondColor(): string {
        return $this->pageFondColor;
    }

    public function setPageFondColor(string $pageFondColor): void {
        $this->pageFondColor = $pageFondColor;
    }

    public function getPageFondTransparent(): bool {
        return $this->pageFondTransparent;
    }

    public function setPageFondTransparent(bool $pageFondTransparent): void {
        $this->pageFondTransparent = $pageFondTransparent;
    }
}