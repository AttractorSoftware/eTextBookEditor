<?php

namespace eTextBook\SpawnBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveBooksWithoutFilesCommand extends ContainerAwareCommand {
    protected function configure() {
        $this
            ->setName('books:removeWithoutFiles')
            ->setDescription('Remove books from database without .etb files')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        $books = $entityManager->getRepository('eTextBookLoungeBundle:Book')->findAll();
        $count = 0;
        foreach($books as $book) {
            $filePath = $this->getContainer()->getParameter('books_dir') . $book->getSlug() . '.etb';
            if(!is_file($filePath) || $book->getSlug() == "") {
                $entityManager->remove($book);
                $count++;
            }
        }
        $entityManager->flush();
        echo "Remove " . $count . " books \n";
    }
}