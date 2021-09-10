<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
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
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $Name;

    /**
     * @ORM\ManyToMany(targetEntity=Author::class, inversedBy="Books")
     * @var Collection|\App\Entity\Author[]
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

    /**
     * @param int $langId
     *
     * @return string|null
     */
    public function getName(int $langId = 0): ?string
    {
        $translations = explode('|', $this->Name);

        return $translations[$langId] ?? $translations[0];
    }

    /**
     * @param string $Name
     *
     * @return $this
     */
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

    /**
     * @return \App\Entity\Author|array
     */
    public function getAuthorArray(): array
    {
        $authors = [];
        foreach ($this->getAuthor() as $author) {
            $authors[] = ['id' => $author->getId(), 'name' => $author->getName()];
        }

        return $authors;
    }

    /**
     * @param \App\Entity\Author $author
     *
     * @return $this
     */
    public function addAuthor(Author $author): self
    {
        if (!$this->Author->contains($author)) {
            $this->Author[] = $author;
        }

        return $this;
    }

    /**
     * @param \App\Entity\Author $author
     *
     * @return $this
     */
    public function removeAuthor(Author $author): self
    {
        $this->Author->removeElement($author);

        return $this;
    }

    /**
     * @param $name
     * @param \App\Repository\AuthorRepository $authorRepository
     * @param \Doctrine\ORM\EntityManager $entityManager
     *
     * @return $this
     */
    public function searchAddAuthor(array $authors, AuthorRepository $authorRepository, EntityManager $entityManager): self
    {
        foreach ($authors as $authorValue) {
            if (is_numeric($authorValue)) {
                if ($author = $authorRepository->find($authorValue)) {
                    $this->addAuthor($author);
                }
            } elseif ($author = $authorRepository->findOneBy(['Name' => $authorValue])) {
                $this->addAuthor($author);
            } else {
                $author = new Author();
                $author->setName($authorValue);
                $entityManager->persist($author);
                $entityManager->flush();
                $this->addAuthor($author);
            }
        }

        return $this;
    }

}
