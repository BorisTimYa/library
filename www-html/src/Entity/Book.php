<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 */
class Book
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Name;

    /**
     * @ORM\ManyToMany(targetEntity=Author::class, inversedBy="Books")
     */
    private $Author;

    public function __construct()
    {
        $this->Author = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(int $langId = 0): ?string
    {
        return explode('|', $this->Name)[$langId ?: 0];
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    /**
     * @return Collection|Author[]
     */
    public function getAuthor(): Collection
    {
        return $this->Author;
    }

    public function addAuthor(Author $author): self
    {
        if (!$this->Author->contains($author)) {
            $this->Author[] = $author;
        }

        return $this;
    }

    public function removeAuthor(Author $author): self
    {
        $this->Author->removeElement($author);

        return $this;
    }

}
