<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'message_sortie_hebdomadaire_a_definir')]
class MessageSortieHebdomadaireADefinir
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: 'id', type: "integer")]
    private int $id;

    #[ORM\Column(name: 'message_sortie_hebdomadaire_a_definir', type: "string", length: 50)]
    private string $message;

    public function getId(): int
    {
        return $this->id;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}