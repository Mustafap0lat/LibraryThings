<?php
// src/Entity/Category.php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Book;
use App\Repository\CategoryRepository;
use App\Repository\FavoriteBookRepository;
use Symfony\Component\Serializer\Annotation\Groups;
// /**
//  * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
//  */
// class Category
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
//     private $categoryName;

//     /**
//      * @ORM\OneToMany(targetEntity="Book", mappedBy="category")
//      */
//     private $books;

//     public function __construct()
//     {
//         $this->books = new ArrayCollection();
//     }

//     public function getId(): ?int
//     {
//         return $this->id;
//     }

//     public function getCategoryName(): ?string
//     {
//         return $this->categoryName;
//     }

//     public function setCategoryName(string $categoryName): self
//     {
//         $this->categoryName = $categoryName;

//         return $this;
//     }

//     /**
//      * @return Collection|Book[]
//      */
//     public function getBooks(): Collection
//     {
//         return $this->books;
//     }
// }

// NEW ============= 
/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
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
    private $categoryName;

    /**
     * @ORM\OneToMany(targetEntity="Book", mappedBy="category")
     */
    private $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoryName(): ?string
    {
        return $this->categoryName;
    }

    public function setCategoryName(string $categoryName): self
    {
        $this->categoryName = $categoryName;

        return $this;
    }

    public function getBooks(): Collection
    {
        return $this->books;
    }
}