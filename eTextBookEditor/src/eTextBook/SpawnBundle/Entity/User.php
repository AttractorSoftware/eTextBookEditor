<?php

namespace eTextBook\SpawnBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity
 */
class User extends BaseUser {
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="\eTextBook\LoungeBundle\Entity\Book", mappedBy="user")
     */
    protected $books;

    /**
     * @ORM\ManyToMany(targetEntity="\eTextBook\LoungeBundle\Entity\Book", mappedBy="editUsers")
     **/
    private $editedBooks;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->books = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Add books
     *
     * @param \eTextBook\LoungeBundle\Entity\Book $books
     * @return User
     */
    public function addBook(\eTextBook\LoungeBundle\Entity\Book $books)
    {
        $this->books[] = $books;

        return $this;
    }

    /**
     * Remove books
     *
     * @param \eTextBook\LoungeBundle\Entity\Book $books
     */
    public function removeBook(\eTextBook\LoungeBundle\Entity\Book $books)
    {
        $this->books->removeElement($books);
    }

    /**
     * Get books
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBooks()
    {
        return $this->books;
    }


    /**
     * Add editedBooks
     *
     * @param \eTextBook\LoungeBundle\Entity\Book $editedBooks
     * @return User
     */
    public function addEditedBook(\eTextBook\LoungeBundle\Entity\Book $editedBooks)
    {
        $this->editedBooks[] = $editedBooks;

        return $this;
    }

    /**
     * Remove editedBooks
     *
     * @param \eTextBook\LoungeBundle\Entity\Book $editedBooks
     */
    public function removeEditedBook(\eTextBook\LoungeBundle\Entity\Book $editedBooks)
    {
        $this->editedBooks->removeElement($editedBooks);
    }

    /**
     * Get editedBooks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEditedBooks()
    {
        return $this->editedBooks;
    }
}
