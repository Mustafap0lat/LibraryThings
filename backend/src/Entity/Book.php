<?php
// src/Entity/Book.php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

// /**
//  * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
//  */
// class Book
// {
//     /**
//      * @ORM\Id
//      * @ORM\GeneratedValue(strategy="AUTO")
//      * @ORM\Column(type="integer")
//      */
//     private $id;

//     /**
//      * @ORM\Column(type="string")
//      */
//     private $title;

//     /**
//      * @ORM\Column(type="string")
//      */
//     private $isbn;

//     /**
//      * @ORM\ManyToOne(targetEntity="Category", inversedBy="books")
//      * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
//      */
//     private $category;

//     /**
//      * @ORM\OneToMany(targetEntity="Note", mappedBy="book")
//      */
//     private $notes;

//     public function __construct()
//     {
//         $this->notes = new ArrayCollection();
//     }

//     public function getId(): ?int
//     {
//         return $this->id;
//     }

//     public function getTitle(): ?string
//     {
//         return $this->title;
//     }

//     public function setTitle(string $title): self
//     {
//         $this->title = $title;

//         return $this;
//     }

//     public function getIsbn(): ?string
//     {
//         return $this->isbn;
//     }

//     public function setIsbn(string $isbn): self
//     {
//         $this->isbn = $isbn;

//         return $this;
//     }

//     public function getCategory(): ?Category
//     {
//         return $this->category;
//     }

//     public function setCategory(?Category $category): self
//     {
//         $this->category = $category;

//         return $this;
//     }

//     /**
//      * @return Collection|Note[]
//      */
//     public function getNotes(): Collection
//     {
//         return $this->notes;
//     }
// }

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"public"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=13, unique=true)
     * @Groups({"public"})
     */
    private $isbn;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="books")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * @Groups({"public"})
     */
    private $category;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="favoriteBooks")
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }
}

