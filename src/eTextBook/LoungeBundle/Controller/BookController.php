<?php

namespace eTextBook\LoungeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Gedmo\Sluggable\Util as Sluggable;
use eTextBook\LoungeBundle\Lib\Book;


class BookController extends Controller {
    /**
     * @Route("/books", name="books")
     * @Template()
     */
    public function indexAction() {
        $fileManager = $this->get('fileManager');
        $booksDir = $this->container->getParameter('books_dir');
        $books = array();

        foreach($fileManager->fileList($booksDir) as $fileName) {
            $filePart = explode('.', $fileName);
            $filePart = end($filePart);
            if($filePart == 'etb') {
                $books[] = new Book($booksDir . $fileName);
            }
        }

        return array('books' => $books);
    }

    /**
     * @Route("/book/create", name="book-create")
     */
    public function createAction(Request $request) {
        $bookData = $request->get('book');
        $transliterate = $this->get('transliterate');

        $bookSlug = Sluggable\Urlizer::urlize($transliterate->transliterate($bookData['title'], 'ru'), '-');
        $booksDir = $this->container->getParameter('books_dir');

        if(is_file($booksDir . $bookSlug . 'etb')) {
            $response = array(
                'status' => 'failed'
                ,'reason' => 'Учебник с таким названием уже существует'
            );
        } else {
            $packer = $this->get('bookPacker');
            $packer->createBookPack($bookData);
            $response = array(
                'status' => 'success'
                ,'data' => array(
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
    public function editAction($slug, $module) {
        $book = new Book($this->container->getParameter('books_dir') . $slug . '.etb');
        $modules = $book->getModules();
        return array(
            'book' => $book
            ,'modules' => $modules
            ,'currentModule' => $module == ' ' && count($modules) > 0 ? $modules[0]->slug : $module
        );
    }

    /**
     * @Route("/book/create/module", name="book-create-module")
     */
    public function createModuleAction(Request $request) {
        $moduleData = $request->get('module');
        $book = new Book($moduleData['bookSlug']);
        $moduleSlug = $book->createModule($moduleData['title']);
        $this->get('bookPacker')->repackBook($moduleData['bookSlug']);
        $response = array('status' => 'success', 'data' => array('slug' => $moduleSlug));
        return new JsonResponse($response);
    }

    /**
     * @Route("/book/update/module", name="book-update-module")
     */
    public function updateModule(Request $request) {

        $content = $request->get('content');
        $slug = $request->get('book');
        $fileManager = $this->get('fileManager');

        $rootDir = $tempDir = $this->container->getParameter('books_dir') . $slug;
        $templateDir = $this->container->getParameter('book_template_dir');
        $rootDir .= "/" . $slug;

        $moduleFilePath = $rootDir . '/modules/' . $request->get('module') . '.html';

        $packer = $this->get('bookPacker');
        $packer->setCurrentBookSlug($slug);
        $packer->createDumpDirs();
        $packer->copyTemplateFiles();
        $packer->copyTmpFiles();

        $indexContent = file_get_contents($templateDir . "/index.html");

        $content = str_replace('/tmp/' . $slug . '/', '', $content);

        file_put_contents(
            $moduleFilePath,
            str_replace(
                array("-- title --", "-- content --"),
                array('', $content),
                $indexContent
            )
        );

        if(is_file($rootDir . "/../../" . $slug . ".etb")) {
            unlink($rootDir . "/../../" . $slug . ".etb");
        }

        $fileManager->zip($rootDir . "/../", $rootDir . "/../../" . $slug . ".etb");
        $fileManager->removeDir($tempDir);

        return new JsonResponse(array('status' => 'success'));
    }

    /**
     * @Route("/book/file-upload", name="book-file-upload")
     */
    public function fileUploadAction(Request $request) {

        $file = $request->files->get('upload-file');
        $bookSlug = $request->get('slug');

        $fileType = explode('/', $file->getMimeType());
        $tmpDir = $this->container->getParameter('book_tmp_dir');
        $fileName = $file->getClientOriginalName();

        switch($fileType[0]) {
            case 'image': {
                $uploadFilePath = $tmpDir . $bookSlug . '/content/img/';
                $file->move($uploadFilePath, $fileName);
                break;
            }
            case 'audio': {
                $uploadFilePath = $tmpDir . $bookSlug . '/content/audio/';
                $file->move($uploadFilePath, $fileName);
                break;
            }
            case 'video': {
                $uploadFilePath = $tmpDir . $bookSlug . '/content/video/';
                $file->move($uploadFilePath, $fileName);
                break;
            }
        }

        return new JsonResponse(array('status' => 'success', 'data' => array(
            'type' => $fileType[0]
            ,'name' => $fileName
        )));
    }
}
