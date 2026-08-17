<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'menu_connect')]
class MenuConnect
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    private int $parent;

    #[ORM\Column(type: 'string', length: 255)]
    private string $url;

    #[ORM\Column(type: 'string', length: 255)]
    private string $menu_name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $class = null;

    public function getId(): int
    {
        return $this->id;
    }
    public function getParent(): int
    {
        return $this->parent;
    }
    public function getUrl(): string
    {
        return $this->url;
    }
    public function getMenuName(): string
    {
        return $this->menu_name;
    }
    public function getClass(): ?string
    {
        return $this->class;
    }
}