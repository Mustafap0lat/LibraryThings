<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\ManyToMany(targetEntity="Book", inversedBy="user", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinTable(
     *     name="user_favorite_book",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="id")}
     * )
     */
    private $favoriteBooks;

    /**
     * @ORM\OneToMany(targetEntity="Note", mappedBy="user", cascade={"persist"})
     */
    private $notes;

    /**
     * @ORM\OneToMany(targetEntity="Rating", mappedBy="user", cascade={"persist"})
     */
    private $rating;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="user", cascade={"persist"})
     */
    private $categories;

    public function __construct()
    {
        $this->favoriteBooks = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->rating = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getFavoriteBooks(): Collection
    {
        return $this->favoriteBooks;
    }

    public function addFavoriteBook(Book $book): self
    {
        if (!$this->favoriteBooks->contains($book)) {
            $this->favoriteBooks[] = $book;
        }

        return $this;
    }

    public function removeFavoriteBook(Book $book): self
    {
        if ($this->favoriteBooks->contains($book)) {
            $this->favoriteBooks->removeElement($book);
        }

        return $this;
    }

    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function getRating(): Collection
    {
        return $this->rating;
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }
}