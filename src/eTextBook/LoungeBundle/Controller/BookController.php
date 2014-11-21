<?php

namespace eTextBook\LoungeBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use eTextBook\LoungeBundle\Lib\BookPacker;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use eTextBook\LoungeBundle\Entity\Book;
use eTextBook\LoungeBundle\UseCases\Book\BookPackage;
use eTextBook\LoungeBundle\UseCases\Book\BookPublisher;


class BookController extends Controller
{
    /**
     * @Route("/books", name="books")
     * @Template()
     */
    public function indexAction() {
        $entityManager = $this->getDoctrine()->getManager();
        $books = $entityManager->getRepository('eTextBookLoungeBundle:Book')
            ->createQueryBuilder('book')
            ->where('book.isPublic = :isPublic')
            ->setParameter('isPublic', true)
            ->getQuery()
            ->getResult();
        return array('books' => $books);
    }

    /**
     * @Route("/my-books", name="my-books")
     * @Template()
     */
    public function myBooksAction() {
        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $books = $this->getUser()->getBooks();
            $books = new ArrayCollection(
                array_merge($books->toArray(), $this->getUser()->getEditedBooks()->toArray())
            );
        } else {
            $books = array();
        }
        return array('books' => $books);
    }

    /**
     * @Route("/book/create", name="book-create")
     */
    public function createAction(Request $request) {
        $entityManager = $this->getDoctrine()->getManager();
        $bookData = $request->get('book');
        $book = new Book();
        $book->setTitle(isset($bookData['title']) ? $bookData['title'] : '');
        $book->setAuthors(isset($bookData['authors']) ? $bookData['authors'] : '');
        $book->setEditor(isset($bookData['editor']) ? $bookData['editor'] : '');
        $book->setIsbn(isset($bookData['isbn']) ? $bookData['isbn'] : '');
        $book->setCover(isset($bookData['cover']) ? $bookData['cover'] : '');
        $book->setFile(isset($bookData['file']) ? $bookData['file'] : '');
        $book->setUser($this->getUser());

        $entityManager->persist($book);
        $entityManager->flush();

        $package = new BookPackage($book);
        $package->setBooksFolderPath($this->container->getParameter('books_dir'));
        $package->setTemplateFolderPath($this->container->getParameter('book_template_dir'));
        $package->setTmpFolderPath($this->container->getParameter('book_tmp_dir'));
        $package->updateBookSlug();
        $package->createBootstrapFiles();
        $package->pack();

        $entityManager->persist($book);
        $entityManager->flush();

        $response = array(
            'status' => 'success',
            'data' => array(
                'slug' => $book->getSlug()
            )
        );

        return new JsonResponse($response);
    }

    /**
     * @Route("/book/edit/{slug}/{module}", name="book-edit")
     * @Template()
     */
    public function editAction($slug, $module) {
        $entityManager = $this->getDoctrine()->getManager();
        $book = $entityManager->getRepository('eTextBookLoungeBundle:Book')->findOneBySlug($slug);
        $package = new BookPackage($book);
        $package->setTmpFolderPath($this->container->getParameter('book_tmp_dir'));
        $package->setBooksFolderPath($this->container->getParameter('books_dir'));
        $package->updateBookSlug();
        $modules = $package->getBookModules();
        if($module == " ") {
            $currentModuleContent = isset($modules[0]->slug) ? $package->getBookModuleContent($modules[0]->slug) : '';
        } else {
            $currentModuleContent = $package->getBookModuleContent($module);
        }
        return array(
            'hasEditPermissions' => $book->hasEditPermissionForUser($this->getUser()->getId()),
            'book' => $book,
            'package' => $package,
            'currentModuleContent' => $currentModuleContent,
            'currentModule' => $module == ' ' && count($modules) > 0 ? $modules[0]->slug : $module
        );
    }

    /**
     * @Route("/publish/{slug}", name="book-publish")
     */
    public function publishAction($slug) {
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('eTextBookLoungeBundle:Book')->findOneBy(
            array('slug' => $slug)
        );

        if (!$book) {
            throw $this->createNotFoundException(
                'No product found for id ' . $slug
            );
        }

        $package = new BookPackage($book);
        $package->setBooksFolderPath($this->container->getParameter('books_dir'));
        $package->setTemplateFolderPath($this->container->getParameter('book_template_dir'));
        $package->setTmpFolderPath($this->container->getParameter('book_tmp_dir'));
        $package->updateBookSlug();
        $publisher = new BookPublisher($package);
        $publisher->setPublishBooksFolderPath($this->container->getParameter('public_dir'));
        $publisher->publish();

        $em->persist($package->getBook());
        $em->flush();

//        $printPublic = new PrintPublic();
//        $printPublic->setBook($book);
//        $printPublic->setETBBook(new Book($this->container->getParameter('books_dir') . $slug . '.etb'));
//        $printPublic->setBookPath($this->get('kernel')->getRootDir() . '/../web/tmp/');
//        $printPublic->setPrintPath($this->get('kernel')->getRootDir() . '/../web/publicBooks/');
//        $printPublic->generate();
//
//        $knp = $this->get('knp_snappy.pdf');
//        $knp->setOption('disable-forms', true);
//        $knp->setOption('javascript-delay', 2000);
//        $knp->setOption('no-stop-slow-scripts', true);
//        $knp->setOption('viewport-size', 1024);
//        $knp->setOption('margin-left', 3);
//        $knp->setOption('margin-right', 3);
//        $knp->setOption('margin-top', 3);
//        $knp->setOption('margin-bottom', 3);
//        $knp->setOption('orientation', 'Landscape');
//        $knp->generate(
//        $this->get('kernel')->getRootDir() . '/../web/publicBooks/' . $book->getSlug(). '/print.html',
//        $this->get('kernel')->getRootDir() . '/../web/publicBooks/pdf/' . $book->getSlug(). '.pdf', array(), true);

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
    public function createModuleAction(Request $request) {
        $entityManager = $this->getDoctrine()->getManager();
        $moduleData = $request->get('module');
        $book = $entityManager->getRepository('eTextBookLoungeBundle:Book')->findOneBySlug($moduleData['bookSlug']);
        $package = new BookPackage($book);
        $package->setTemplateFolderPath($this->container->getParameter('book_template_dir'));
        $package->setTmpFolderPath($this->container->getParameter('book_tmp_dir'));
        $package->setBooksFolderPath($this->container->getParameter('books_dir'));
        $package->updateBookSlug();
        $moduleSlug = $package->addModule($moduleData['title']);
        $package->pack();
        $response = array('status' => 'success', 'data' => array('slug' => $moduleSlug));
        return new JsonResponse($response);
    }

    /**
     * @Route("/book/update/module", name="book-update-module")
     */
    public function updateModule(Request $request) {
        $content = $request->get('content');
        $bookSlug = $request->get('book');
        $moduleSlug = $request->get('module');
        $indexTasks = $request->get('blocks');

        $book = $this->getDoctrine()->getManager()->getRepository('eTextBookLoungeBundle:Book')->findOneBySlug($bookSlug);
        $package = new BookPackage($book);
        $package->setTmpFolderPath($this->container->getParameter('book_tmp_dir'));
        $package->setBooksFolderPath($this->container->getParameter('books_dir'));
        $package->setTemplateFolderPath($this->container->getParameter('book_template_dir'));
        $package->updateBookSlug();
        $package->createBootstrapFiles();
        $package->updateModuleContent($moduleSlug, $content);
        $package->updateBookInfoSummary($moduleSlug, $indexTasks);
        $package->updateBookSummary();
        $package->pack();

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
    public function saveEditPermissionsAction($bookSlug, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $book = $entityManager->getRepository("eTextBookLoungeBundle:Book")->findOneBySlug($bookSlug);
        $currentUser = $this->getUser();
        $result = array('status' => 1);
        if (!$book->hasEditPermissionForUser($currentUser->getId())) {
            $result['status'] = 0;
            $result['reason'] = 'No permissions';
        } else {
            foreach ($book->getEditUsers() as $user) {
                $book->getEditUsers()->removeElement($user);
            }
            $entityManager->persist($book);
            $entityManager->flush();

            if ($request->get('users') != "") {
                foreach ($request->get('users') as $userEmail) {
                    $user = $entityManager->getRepository('eTextBookSpawnBundle:User')->findOneByEmail($userEmail);
                    if (is_object($user)) {
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


