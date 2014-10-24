<?php

namespace eTextBook\LoungeBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Gedmo\Sluggable\Util as Sluggable;
use eTextBook\LoungeBundle\Lib\Book;
use eTextBook\LoungeBundle\Entity\Book as eBook;


class BookController extends Controller
{
    /**
     * @Route("/books", name="books")
     * @Template()
     */
    public function indexAction()
    {
        $books = array();
        $entityManager = $this->getDoctrine()->getManager();
        $books['publicBooks'] = $entityManager->getRepository('eTextBookLoungeBundle:Book')
            ->createQueryBuilder('book')
            ->where('book.isPublic = :isPublic')
            ->setParameter('isPublic', true)
            ->getQuery()
            ->getResult();

        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $books['userBooks'] = $this->getUser()->getBooks();
            $books['userBooks'] = new ArrayCollection(
                array_merge($books['userBooks']->toArray(), $this->getUser()->getEditedBooks()->toArray())
            );
        }

        return $books;
    }

    /**
     * @Route("/book/create", name="book-create")
     */
    public function createAction(Request $request)
    {
        $bookData = $request->get('book');
        $transliterate = $this->get('transliterate');
        $bookSlug = Sluggable\Urlizer::urlize($transliterate->transliterate($bookData['title'], 'ru'), '-');

        $book = new eBook();
        $book->setTitle(isset($bookData['title']) ? $bookData['title'] : '');
        $book->setAuthors(isset($bookData['authors']) ? $bookData['authors'] : '');
        $book->setEditor(isset($bookData['editor']) ? $bookData['editor'] : '');
        $book->setIsbn(isset($bookData['isbn']) ? $bookData['isbn'] : '');
        $book->setCover(isset($bookData['cover']) ? $bookData['cover'] : '');
        $book->setFile(isset($bookData['file']) ? $bookData['file'] : '');
        $book->setSlug($bookSlug);
        $book->setUser($this->getUser());

        $creator = $this->get('createETBFile');
        $creator->setBook($book);

        if (!$creator->execute()) {
            $response = array(
                'status' => 'failed'
            ,
                'reason' => 'Учебник с таким названием уже существует'
            );
        } else {
            $response = array(
                'status' => 'success'
            ,
                'data' => array(
                    'slug' => $bookSlug
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/book/edit/{slug}/{module}", name="book-edit")
     * @Template()
     */
    public function editAction($slug, $module) {
        $entityManager = $this->getDoctrine()->getManager();
        $book = new Book($this->container->getParameter('books_dir') . $slug . '.etb');
        $dbBook = $entityManager->getRepository('eTextBookLoungeBundle:Book')->findOneBySlug($slug);
        $modules = $book->getModules();

        return array(
            'hasEditPermissions' => $dbBook->hasEditPermissionForUser($this->getUser()->getId()),
            'dbBook' => $dbBook,
            'book' => $book,
            'modules' => $modules,
            'currentModule' => $module == ' ' && count($modules) > 0 ? $modules[0]->slug : $module
        );
    }

    /**
     * @Route("/publish/{slug}", name="book-publish")
     */
    public function publishAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('eTextBookLoungeBundle:Book')->findOneBy(
            array('slug' => $slug)
        );

        if (!$book) {
            throw $this->createNotFoundException(
                'No product found for id ' . $slug
            );
        }

        $book->setIsPublic(1);
        $em->flush();
        $book = $this->get('bookLoader')->load($slug);

        $updater = $this->get('updateETBFile');
        $updater->setBook($book);
        $updater->publishBook($slug);

        return $this->redirect($this->generateUrl('books'));

    }

    /**
     * @Route("/book/view/{slug}/{module}", name="book-view")
     * @Template()
     */
    public function viewAction($slug, $module)
    {
        $book = new Book($this->container->getParameter('books_dir') . $slug . '.etb');
        $modules = $book->getModules();

        return array(
            'book' => $book,
            'modules' => $modules,
            'currentModule' => $module == ' ' && count($modules) > 0 ? $modules[0]->slug : $module
        );
    }

    /**
     * @Route("/book/create/module", name="book-create-module")
     */
    public function createModuleAction(Request $request)
    {
        $moduleData = $request->get('module');
        $book = $this->get('bookLoader')->load($moduleData['bookSlug']);

        $updater = $this->get('updateETBFile');
        $updater->setBook($book);
        $moduleSlug = $updater->addModule($moduleData['title']);

        $response = array('status' => 'success', 'data' => array('slug' => $moduleSlug));

        return new JsonResponse($response);
    }

    /**
     * @Route("/book/update/module", name="book-update-module")
     */
    public function updateModule(Request $request)
    {
        $updater = $this->get('updateETBFile');

        $content = $request->get('content');
        $bookName = $request->get('book');
        $moduleSlug = $request->get('module');

        $book = new eBook();
        $book->setSlug($bookName);

        $updater->setBook($book);
        $updater->updateModuleContent($bookName, $moduleSlug, $content);
        $updater->copyTemplateFiles();
        $updater->pack();

        return new JsonResponse(array('status' => 'success'));
    }

    /**
     * @Route("/book/file-upload", name="book-file-upload")
     */
    public function fileUploadAction(Request $request)
    {
        $file = $request->files->get('upload-file');
        $bookSlug = $request->get('slug');
        $fileType = explode('/', $file->getMimeType());
        $tmpDir = $this->container->getParameter('book_tmp_dir');
        $fileName = $file->getClientOriginalName();

        switch ($fileType[0]) {
            case 'image':
            {
                $uploadFilePath = $tmpDir . $bookSlug . '/content/img/';
                $file->move($uploadFilePath, $fileName);
                break;
            }
            case 'audio':
            {
                $uploadFilePath = $tmpDir . $bookSlug . '/content/audio/';
                $file->move($uploadFilePath, $fileName);
                break;
            }
            case 'video':
            {
                $uploadFilePath = $tmpDir . $bookSlug . '/content/video/';
                $file->move($uploadFilePath, $fileName);
                break;
            }
        }

        return new JsonResponse(
            array(
                'status' => 'success',
                'data' => array(
                    'type' => $fileType[0]
                ,
                    'name' => $fileName
                )
            )
        );
    }

    /**
     * @Route("/book/cover-upload", name="book-cover-upload")
     */
    public function coverUpload(Request $request)
    {
        $coverFile = $request->files->get('cover');
        $tmpTitle = date('dmYHis') . '.' . $coverFile->guessExtension();
        $coverTmpDir = $this->container->getParameter('book_tmp_dir') . 'cover';
        if (!is_dir($coverTmpDir)) {
            mkdir($coverTmpDir);
        }
        $coverFile->move($coverTmpDir, $tmpTitle);

        return new JsonResponse(array('fileName' => $tmpTitle));
    }

    /**
     * @Route("/book/upload", name="book-upload")
     */
    public function bookUpload(Request $request)
    {
        $bookFile = $request->files->get('book-file');
        $extension = explode(".", $bookFile->getClientOriginalName());
        $tmpTitle = date('dmYHis') . '.' . end($extension);
        $coverTmpDir = $this->container->getParameter('book_tmp_dir') . 'book';
        if (!is_dir($coverTmpDir)) {
            mkdir($coverTmpDir);
        }
        $bookFile->move($coverTmpDir, $tmpTitle);

        return new JsonResponse(array('fileName' => $tmpTitle));
    }

    /**
     * @Route("/save-edit-permissions/{bookSlug}", name="save-edit-permissions")
     */
    public function saveEditPermissionsAction($bookSlug, Request $request) {
        $entityManager = $this->getDoctrine()->getManager();
        $book = $entityManager->getRepository("eTextBookLoungeBundle:Book")->findOneBySlug($bookSlug);
        $currentUser = $this->getUser();
        $result = array('status' => 1);
        if(!$book->hasEditPermissionForUser($currentUser->getId())) {
            $result['status'] = 0;
            $result['reason'] = 'No permissions';
        } else {
            foreach ($book->getEditUsers() as $user) {
                $book->getEditUsers()->removeElement($user);
            }
            $entityManager->persist($book);
            $entityManager->flush();

            if($request->get('users') != "") {
                foreach($request->get('users') as $userEmail) {
                    $user = $entityManager->getRepository('eTextBookSpawnBundle:User')->findOneByEmail($userEmail);
                    if(is_object($user)) {
                        $book->addEditUser($user);
                    }
                }

                $entityManager->persist($book);
                $entityManager->flush();
            }

        }

        return new JsonResponse($result);
    }

}


