<?php

namespace eTextBook\LoungeBundle\UseCases\Book;


class BookPublisher {

    private $package;
    private $publishBooksFolderPath;

    public function __construct(BookPackage $package) {
        $this->package = $package;
    }

    public function publish() {
        if($this->package->getBook()->getIsPublic()) {
            $this->package->getBook()->versionIncrement();
        } else { $this->package->getBook()->setIsPublic(true); }

        $previousVersionFilePath =
            $this->package->getBooksFolderPath() . $this->package->getBook()->getSlug() . '.etb';

        $this->package->getBook()->setPublicAt(new \DateTime());
        $this->package->updateBookSlug();
        $this->package->unpack($previousVersionFilePath);
        $this->package->createBootstrapFiles();
        $this->package->pack();
        $this->package->setBooksFolderPath($this->publishBooksFolderPath);
        $this->package->pack();
        $this->package->getBook()->setControlFileSum(
            md5_file($this->publishBooksFolderPath . $this->package->getBook()->getSlug() . '.etb')
        );
        $this->package->setTmpFolderPath($this->publishBooksFolderPath);
        $this->package->updateBookSlug();
        $this->package->unpack();
    }

    public function setPublishBooksFolderPath($publishBooksFolderPath) {
        $this->publishBooksFolderPath = $publishBooksFolderPath;
    }

    public function publishPdf($pdfGenerator) {
        $this->generateCommonModulesFile();
        $this->generatePdfFile($pdfGenerator);
    }

    public function generatePdfFile($pdfGenerator) {
        $pdfGenerator->setOption('disable-forms', true);
        $pdfGenerator->setOption('javascript-delay', 2000);
        $pdfGenerator->setOption('no-stop-slow-scripts', true);
        $pdfGenerator->setOption('viewport-size', 1024);
        $pdfGenerator->setOption('margin-left', 3);
        $pdfGenerator->setOption('margin-right', 3);
        $pdfGenerator->setOption('margin-top', 3);
        $pdfGenerator->setOption('margin-bottom', 3);
        $pdfGenerator->setOption('zoom', 0.8);
        $pdfGenerator->setOption('minimum-font-size', 14);
        $bookSlug = $this->package->getBook()->getSlug();
        $pdfGenerator->generate(
            $this->publishBooksFolderPath . $bookSlug . '/print.html',
            $this->publishBooksFolderPath . 'pdf/' . $bookSlug. '.pdf', array(), true
        );
    }

    public function generateCommonModulesFile() {
        $bookSlug = $this->package->getBook()->getSlug();
        $modules = $this->package->getBookModules();
        $content = '';
        foreach($modules as $module) {
            $content .= $this->package->getBookModuleContent($module->slug);
        }
        $changeContentPath = str_replace("/tmp/". $bookSlug . "/", "", $content);
        copy($this->package->getTemplateFolderPath() . "/js/print.min.js", $this->package->getBooksFolderPath(). $bookSlug . '/js/print.min.js');
        file_put_contents(
            $this->publishBooksFolderPath . $bookSlug . "/print.html",
            str_replace('-- modules --', $changeContentPath, file_get_contents($this->package->getTemplateFolderPath() . 'printTemplate.html'))
        );
    }

}