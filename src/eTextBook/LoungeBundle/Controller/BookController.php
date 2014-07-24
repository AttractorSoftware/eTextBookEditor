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
            $books[] = new Book($booksDir . $fileName);
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

            $bookDir = $tempDir = $booksDir . $bookSlug;
            mkdir($bookDir);
            $bookDir .= "/" . $bookSlug;

            $templateDir = $this->container->getParameter('book_template_dir');
            $cssDir = $bookDir . '/css';
            $fontsDir = $bookDir . '/fonts';
            $jsDir = $bookDir . '/js';
            $imgDir = $bookDir . '/img';
            $modulesDir = $bookDir . '/modules';
            $contentDir = $bookDir . '/content';
            $videoContentDir = $contentDir . '/video';
            $audioContentDir = $contentDir . '/audio';
            $imgContentDir = $contentDir . '/img';
            $infoFilePath = $bookDir . '/book.info';
            $indexFilePath = $bookDir . '/index.html';

            mkdir($bookDir);
            mkdir($cssDir);
            mkdir($jsDir);
            mkdir($imgDir);
            mkdir($fontsDir);
            mkdir($contentDir);
            mkdir($videoContentDir);
            mkdir($audioContentDir);
            mkdir($imgContentDir);
            mkdir($modulesDir);

            $fileManager = $this->get('fileManager');

            $fileManager->copyFilesFromDirectory($templateDir . "/css", $cssDir);
            $fileManager->copyFilesFromDirectory($templateDir . "/js", $jsDir);
            $fileManager->copyFilesFromDirectory($templateDir . "/fonts", $fontsDir);
            $fileManager->copyFilesFromDirectory($templateDir . "/img", $imgDir);

            file_put_contents($infoFilePath, "title =+= " . $bookData['title']);

            $indexContent = file_get_contents($templateDir . "/index.html");

            $content = '<e-text-book></e-text-book>';

            file_put_contents(
                $indexFilePath,
                str_replace(
                    array("-- title --", "-- content --"),
                    array($bookData['title'], $content),
                    $indexContent
                )
            );

            $fileManager->zip($bookDir . "/../", $bookDir . "/../../" . $bookSlug . ".etb");
            $fileManager->removeDir($tempDir);

            $response = array(
                'status' => 'ok'
                ,'data' => array('slug' => $bookSlug)
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
        return array(
            'book' => $book
            ,'modules' => $book->getModules()
            ,'currentModule' => $module
        );
    }

    /**
     * @Route("/book/create/module", name="book-create-module")
     */
    public function createModuleAction(Request $request) {
        $moduleData = $request->get('module');
        $book = new Book($moduleData['bookSlug']);
        $moduleSlug = $book->createModule($moduleData['title'], $this->container->getParameter('book_template_dir'));
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
        $tmpDir = $this->container->getParameter('book_tmp_dir') . $slug;
        mkdir($rootDir);
        $rootDir .= "/" . $slug;

        $cssDir = $rootDir . '/css';
        $fontsDir = $rootDir . '/fonts';
        $jsDir = $rootDir . '/js';
        $imgDir = $rootDir . '/img';
        $modulesDir = $rootDir . '/modules';
        $contentDir = $rootDir . '/content';
        $videoContentDir = $contentDir . '/video';
        $audioContentDir = $contentDir . '/audio';
        $imgContentDir = $contentDir . '/img';
        $infoFilePath = $rootDir . '/book.info';
        $moduleFilePath = $rootDir . '/modules/' . $request->get('module');

        mkdir($rootDir);
        mkdir($cssDir);
        mkdir($jsDir);
        mkdir($fontsDir);
        mkdir($contentDir);
        mkdir($videoContentDir);
        mkdir($audioContentDir);
        mkdir($imgContentDir);
        mkdir($modulesDir);

        $fileManager->copyFilesFromDirectory($templateDir . "/css", $cssDir);
        $fileManager->copyFilesFromDirectory($templateDir . "/js", $jsDir);
        $fileManager->copyFilesFromDirectory($templateDir . "/fonts", $fontsDir);
        $fileManager->copyFilesFromDirectory($templateDir . "/img", $imgDir);

        if(is_dir($tmpDir)) {
            $fileManager->copyFilesFromDirectory($tmpDir . '/content/img', $imgContentDir);
            $fileManager->copyFilesFromDirectory($tmpDir . '/content/audio', $audioContentDir);
            $fileManager->copyFilesFromDirectory($tmpDir . '/content/video', $videoContentDir);
        }

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
