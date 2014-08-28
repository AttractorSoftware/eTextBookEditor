<?php

namespace eTextBook\LoungeBundle\Controller;

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
        $fileManager = $this->get('fileManager');
        $booksDir = $this->container->getParameter('books_dir');
        $books = array();

        foreach ($fileManager->fileList($booksDir) as $fileName) {
            $filePart = explode('.', $fileName);
            $filePart = end($filePart);
            if ($filePart == 'etb') {
                $books[] = new Book($booksDir . $fileName);
            }
        }

        return array('books' => $books);
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
        $book->setSlug($bookSlug);

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
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/book/edit/{slug}/{module}", name="book-edit")
     * @Template()
     */
    public function editAction($slug, $module)
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

        $updater->updateModuleContent($bookName, $moduleSlug, $content);

        $book = new eBook();
        $book->setSlug($bookName);
        $updater->setBook($book);
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
}
