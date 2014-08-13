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
        $fileManager = $this->get('fileManager');
        $bookLoader = $this->get('bookLoader');

        $booksDir = $this->container->getParameter('books_dir');
        $result = array('books' => array());

        foreach ($fileManager->fileList($booksDir) as $fileName) {
            $fileParts = explode('.', $fileName);
            $extension = end($fileParts);
            if ($extension == 'etb') {
                $book = $bookLoader->load($fileParts[0]);
                $result['books'][] = $book->toArray();
            }
        }

        return new JsonResponse($result);
    }
}
