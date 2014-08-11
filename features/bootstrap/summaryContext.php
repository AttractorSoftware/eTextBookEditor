<?php

use Features\Bootstrap\eTextBookContext;

/**
 * Features context.
 */
class SummaryContext extends eTextBookContext {

    /**
     * @Given /^Открываем созданный учебник$/
     */
    public function latestBookLinkViewClick() {
        $links = $this->findAllCss('.book-list li');
        $latestBook = $this->getVar('latestBook');
        foreach($links as $link) {
            $titleLink = $link->find('css', 'a.title');
            if(is_object($titleLink) && trim($titleLink->getHTML()) == $latestBook['title']) {
                $link->find('css', '.view-link')->click();
                sleep(2);
            }
        }
    }

    /**
     * @Given /^Открываем созданный учебник$/
     */
    public function openLastTextBook() {
        $this->latestBookLinkViewClick();
    }

}
