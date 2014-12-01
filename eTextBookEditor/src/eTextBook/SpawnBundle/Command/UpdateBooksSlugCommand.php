<?php

namespace eTextBook\SpawnBundle\Command;

use eTextBook\LoungeBundle\UseCases\Book\BookPackage;
use eTextBook\LoungeBundle\UseCases\Book\BookPublisher;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateBooksSlugCommand extends ContainerAwareCommand {
    protected function configure() {
        $this
            ->setName('books:updateSlug')
            ->setDescription('Update books slug')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        $books = $entityManager->getRepository('eTextBookLoungeBundle:Book')->findAll();
        foreach($books as $book) {
            $oldSlug = $book->getSlug();
            $package = new BookPackage($book);
            $package->setTmpFolderPath($this->getContainer()->getParameter('book_tmp_dir'));
            $package->setBooksFolderPath($this->getContainer()->getParameter('books_dir'));
            $package->setTemplateFolderPath($this->getContainer()->getParameter('book_template_dir'));
            $package->updateBookSlug();
            $package->unpack($this->getContainer()->getParameter('books_dir') . $oldSlug . '.etb');
            $package->pack();
            if($book->getIsPublic()) {
                $package->setBooksFolderPath($this->getContainer()->getParameter('public_dir'));
                $package->updateBookSlug();
                $package->pack();
                $package->setTmpFolderPath($this->getContainer()->getParameter('public_dir'));
                $package->updateBookSlug();
                $package->unpack();
            }
            $entityManager->persist($book);
            echo $book->getTitle() . " - " . $oldSlug . " | " . $book->getSlug() . "\n";
        } $entityManager->flush();
    }
}