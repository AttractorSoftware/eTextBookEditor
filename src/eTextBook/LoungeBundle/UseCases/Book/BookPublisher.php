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

        $bookCurrentVersion = $this->package->getBook()->getVersion();
        $bookPreviousVersion = $bookCurrentVersion == 1 ? 1 : $bookCurrentVersion-1;
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

}