<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'photos_videos')]
class PhotoVideo
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: 'id_photo_video', type: "integer")]
    private int $id; // Utiliser ?int pour les propriétés qui n'ont pas de valeur avant la persistance

    #[ORM\Column(name: 'titre_photo_video', type: "string", length: 255)]
    private string $titre;

    /**
     * Contient soit le chemin relatif du fichier image (ex: uploads/medias/mon_image.jpg),
     * soit l'URL de la vidéo externe (ex: https://youtube.com/...).
     */
    #[ORM\Column(name: 'fichier_photo_video', type: "string", length: 255)]
    private string $fichier;

    /**
     * Type de média : 'image' ou 'video'.
     */
    #[ORM\Column(name: 'type_photo_video', type: "string", length: 50)]
    private string $type;

    #[ORM\Column(name: 'date_ajout_photo_video', type: "datetime")]
    private \DateTimeInterface $dateAjout;

    // --- CONSTRUCTEUR ---
    public function __construct()
    {
        $this->dateAjout = new \DateTime();
    }

    // --- GETTERS & SETTERS ---

    public function getId(): int
    {
        return $this->id;
    }

    // --- Titre ---
    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): void
    {
        $this->titre = $titre;
    }

    // --- Fichier (Chemin ou URL) ---
    public function getFichier(): string
    {
        return $this->fichier;
    }

    public function setFichier(string $fichier): void
    {
        $this->fichier = $fichier;
    }

    // --- Type ('image' ou 'video') ---
    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    // --- Date d'Ajout ---
    public function getDateAjout(): \DateTimeInterface
    {
        return $this->dateAjout;
    }

    public function setDateAjout(\DateTimeInterface $dateAjout): void
    {
        $this->dateAjout = $dateAjout;
    }

    public function getEmbedUrl(): string
    {
        // On suppose que l'URL est de YouTube (le cas le plus courant pour un iframe)
        // La propriété $this->fichier contient l'URL brute de la vidéo (ex: https://www.youtube.com/watch?v=dQw4w9WgXcQ)

        if ($this->type !== 'video') {
            return '';
        }
        
        $url = $this->fichier;

        // 1. Gère les liens watch?v= (URLs classiques)
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|\/(?:watch\?.*v=|(?:embed|v)\/))|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            $videoId = $matches[1];
            // Retourne l'URL d'embed standard de YouTube
            return "https://www.youtube.com/embed/{$videoId}"; 
        }

        // 2. Si l'URL est déjà une URL d'embed ou autre format (ex: DailyMotion, Vimeo)
        // On retourne l'URL telle quelle si elle semble déjà utilisable dans un iframe
        if (str_contains($url, 'embed') || str_contains($url, 'player.vimeo')) {
             return $url;
        }

        return ''; // Retourne une chaîne vide si non reconnu
    }
}