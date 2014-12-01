<?php

namespace eTextBook\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use eTextBook\LoungeBundle\Entity\Book;

/**
 * @Route("/api")
 */
class BookController extends Controller {
    /**
     * @Route("/books")
     */
    public function booksAction() {
        $books = array();
        $entityManager = $this->getDoctrine()->getManager();
        $books = $entityManager->getRepository('eTextBookLoungeBundle:Book')
            ->createQueryBuilder('book')
            ->where('book.isPublic = :isPublic')
            ->setParameter('isPublic', true)
            ->getQuery()
            ->getResult();

        $result = array();
        foreach($books as $book) {
            $result[] = $book->toArray();
        }

        return new JsonResponse(array('books' => $result));
    }
}
