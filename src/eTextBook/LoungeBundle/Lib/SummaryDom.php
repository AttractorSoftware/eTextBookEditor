<?php

namespace eTextBook\LoungeBundle\Lib;

class SummaryDom extends SimpleHtmlDom
{
    function tidySave($filepath = '')
    {
        $ret = $this->root->innertext();
        $config = array(
            'indent' => true,
            'wrap' => 200
        );
        $tidy = tidy_parse_string($ret, $config, 'UTF8');
        $tidy->cleanRepair();
        $test = $tidy->value;
        if ($filepath !== '') {
            file_put_contents($filepath, $tidy->value, LOCK_EX);
        }
        return $test;
    }

    protected function prepare(
        $str,
        $lowercase = true,
        $stripRN = true,
        $defaultBRText = DEFAULT_BR_TEXT,
        $defaultSpanText = DEFAULT_SPAN_TEXT
    ) {
        $this->clear();

        // set the length of content before we do anything to it.
        $this->size = strlen($str);
        // Save the original size of the html that we got in.  It might be useful to someone.
        $this->original_size = $this->size;

        //before we save the string as the doc...  strip out the \r \n's if we are told to.
        if ($stripRN) {
            $str = str_replace("\r", " ", $str);
            $str = str_replace("\n", " ", $str);

            // set the length of content since we have changed it.
            $this->size = strlen($str);
        }

        $this->doc = $str;
        $this->pos = 0;
        $this->cursor = 1;
        $this->noise = array();
        $this->nodes = array();
        $this->lowercase = $lowercase;
        $this->default_br_text = $defaultBRText;
        $this->default_span_text = $defaultSpanText;
        $this->root = new SummaryDomElement($this);
        $this->root->tag = 'root';
        $this->root->_[HDOM_INFO_BEGIN] = -1;
        $this->root->nodetype = HDOM_TYPE_ROOT;
        $this->parent = $this->root;
        if ($this->size > 0) {
            $this->char = $this->doc[0];
        }
    }

    protected function parse()
    {
        if (($s = $this->copy_until_char('<')) === '') {
            return $this->read_tag();
        }

        // text
        $node = new SummaryDomElement($this);
        ++$this->cursor;
        $node->_[HDOM_INFO_TEXT] = $s;
        $this->link_nodes($node, false);

        return true;
    }

    protected function read_tag()
    {
        if ($this->char !== '<') {
            $this->root->_[HDOM_INFO_END] = $this->cursor;

            return false;
        }
        $begin_tag_pos = $this->pos;
        $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next

        // end tag
        if ($this->char === '/') {
            $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
            // This represents the change in the simple_html_dom trunk from revision 180 to 181.
            // $this->skip($this->token_blank_t);
            $this->skip($this->token_blank);
            $tag = $this->copy_until_char('>');

            // skip attributes in end tag
            if (($pos = strpos($tag, ' ')) !== false) {
                $tag = substr($tag, 0, $pos);
            }

            $parent_lower = strtolower($this->parent->tag);
            $tag_lower = strtolower($tag);

            if ($parent_lower !== $tag_lower) {
                if (isset($this->optional_closing_tags[$parent_lower]) && isset($this->block_tags[$tag_lower])) {
                    $this->parent->_[HDOM_INFO_END] = 0;
                    $org_parent = $this->parent;

                    while (($this->parent->parent) && strtolower($this->parent->tag) !== $tag_lower) {
                        $this->parent = $this->parent->parent;
                    }

                    if (strtolower($this->parent->tag) !== $tag_lower) {
                        $this->parent = $org_parent; // restore origonal parent
                        if ($this->parent->parent) {
                            $this->parent = $this->parent->parent;
                        }
                        $this->parent->_[HDOM_INFO_END] = $this->cursor;

                        return $this->as_text_node($tag);
                    }
                } else {
                    if (($this->parent->parent) && isset($this->block_tags[$tag_lower])) {
                        $this->parent->_[HDOM_INFO_END] = 0;
                        $org_parent = $this->parent;

                        while (($this->parent->parent) && strtolower($this->parent->tag) !== $tag_lower) {
                            $this->parent = $this->parent->parent;
                        }

                        if (strtolower($this->parent->tag) !== $tag_lower) {
                            $this->parent = $org_parent; // restore origonal parent
                            $this->parent->_[HDOM_INFO_END] = $this->cursor;

                            return $this->as_text_node($tag);
                        }
                    } else {
                        if (($this->parent->parent) && strtolower($this->parent->parent->tag) === $tag_lower) {
                            $this->parent->_[HDOM_INFO_END] = 0;
                            $this->parent = $this->parent->parent;
                        } else {
                            return $this->as_text_node($tag);
                        }
                    }
                }
            }

            $this->parent->_[HDOM_INFO_END] = $this->cursor;
            if ($this->parent->parent) {
                $this->parent = $this->parent->parent;
            }

            $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
            return true;
        }

        $node = new SummaryDomElement($this);
        $node->_[HDOM_INFO_BEGIN] = $this->cursor;
        ++$this->cursor;
        $tag = $this->copy_until($this->token_slash);
        $node->tag_start = $begin_tag_pos;

        // doctype, cdata & comments...
        if (isset($tag[0]) && $tag[0] === '!') {
            $node->_[HDOM_INFO_TEXT] = '<' . $tag . $this->copy_until_char('>');

            if (isset($tag[2]) && $tag[1] === '-' && $tag[2] === '-') {
                $node->nodetype = HDOM_TYPE_COMMENT;
                $node->tag = 'comment';
            } else {
                $node->nodetype = HDOM_TYPE_UNKNOWN;
                $node->tag = 'unknown';
            }
            if ($this->char === '>') {
                $node->_[HDOM_INFO_TEXT] .= '>';
            }
            $this->link_nodes($node, true);
            $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
            return true;
        }

        // text
        if ($pos = strpos($tag, '<') !== false) {
            $tag = '<' . substr($tag, 0, -1);
            $node->_[HDOM_INFO_TEXT] = $tag;
            $this->link_nodes($node, false);
            $this->char = $this->doc[--$this->pos]; // prev
            return true;
        }

        if (!preg_match("/^[\w-:]+$/", $tag)) {
            $node->_[HDOM_INFO_TEXT] = '<' . $tag . $this->copy_until('<>');
            if ($this->char === '<') {
                $this->link_nodes($node, false);

                return true;
            }

            if ($this->char === '>') {
                $node->_[HDOM_INFO_TEXT] .= '>';
            }
            $this->link_nodes($node, false);
            $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
            return true;
        }

        // begin tag
        $node->nodetype = HDOM_TYPE_ELEMENT;
        $tag_lower = strtolower($tag);
        $node->tag = ($this->lowercase) ? $tag_lower : $tag;

        // handle optional closing tags
        if (isset($this->optional_closing_tags[$tag_lower])) {
            while (isset($this->optional_closing_tags[$tag_lower][strtolower($this->parent->tag)])) {
                $this->parent->_[HDOM_INFO_END] = 0;
                $this->parent = $this->parent->parent;
            }
            $node->parent = $this->parent;
        }

        $guard = 0; // prevent infinity loop
        $space = array($this->copy_skip($this->token_blank), '', '');

        // attributes
        do {
            if ($this->char !== null && $space[0] === '') {
                break;
            }
            $name = $this->copy_until($this->token_equal);
            if ($guard === $this->pos) {
                $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
                continue;
            }
            $guard = $this->pos;

            // handle endless '<'
            if ($this->pos >= $this->size - 1 && $this->char !== '>') {
                $node->nodetype = HDOM_TYPE_TEXT;
                $node->_[HDOM_INFO_END] = 0;
                $node->_[HDOM_INFO_TEXT] = '<' . $tag . $space[0] . $name;
                $node->tag = 'text';
                $this->link_nodes($node, false);

                return true;
            }

            // handle mismatch '<'
            if ($this->doc[$this->pos - 1] == '<') {
                $node->nodetype = HDOM_TYPE_TEXT;
                $node->tag = 'text';
                $node->attr = array();
                $node->_[HDOM_INFO_END] = 0;
                $node->_[HDOM_INFO_TEXT] = substr($this->doc, $begin_tag_pos, $this->pos - $begin_tag_pos - 1);
                $this->pos -= 2;
                $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
                $this->link_nodes($node, false);

                return true;
            }

            if ($name !== '/' && $name !== '') {
                $space[1] = $this->copy_skip($this->token_blank);
                $name = $this->restore_noise($name);
                if ($this->lowercase) {
                    $name = strtolower($name);
                }
                if ($this->char === '=') {
                    $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
                    $this->parse_attr($node, $name, $space);
                } else {
                    //no value attr: nowrap, checked selected...
                    $node->_[HDOM_INFO_QUOTE][] = HDOM_QUOTE_NO;
                    $node->attr[$name] = true;
                    if ($this->char != '>') {
                        $this->char = $this->doc[--$this->pos];
                    } // prev
                }
                $node->_[HDOM_INFO_SPACE][] = $space;
                $space = array($this->copy_skip($this->token_blank), '', '');
            } else {
                break;
            }
        } while ($this->char !== '>' && $this->char !== '/');

        $this->link_nodes($node, true);
        $node->_[HDOM_INFO_ENDSPACE] = $space[0];

        // check self closing
        if ($this->copy_until_char_escape('>') === '/') {
            $node->_[HDOM_INFO_ENDSPACE] .= '/';
            $node->_[HDOM_INFO_END] = 0;
        } else {
            // reset parent
            if (!isset($this->self_closing_tags[strtolower($node->tag)])) {
                $this->parent = $node;
            }
        }
        $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next

        // If it's a BR tag, we need to set it's text to the default text.
        // This way when we see it in plaintext, we can generate formatting that the user wants.
        // since a br tag never has sub nodes, this works well.
        if ($node->tag == "br") {
            $node->_[HDOM_INFO_INNER] = $this->default_br_text;
        }

        return true;
    }

    public function loadWithBreaks($path)
    {
        if (preg_match("/^http:\/\//i", $path) || is_file($path)) {
            $text = file_get_contents($path);
        } else {
            $text = $path;
        }
        $this ->load($text, true, false);
    }

    public function loadWithoutBreaks($path)
    {
        if (preg_match("/^http:\/\//i", $path) || is_file($path)) {
            $text = file_get_contents($path);
        } else {
            $text = $path;
        }
        $this ->load($text);
    }

    public function setTitle($title)
    {
        $this->find('title', 0)->innertext = $title;
    }

    public function setBookName($name)
    {
        $this->find('.book-name', 0)->innertext = $name;
    }

    public function setBookAttributes($bookName)
    {
        $this->setTitle($bookName);
        $this->setBookName($bookName);
    }

    public function getSummaryList()
    {
        return $this->find('#moduleList', 0);
    }

    public function setSummaryList($content)
    {
        $this->find('.summary', 0)->innertext = $content;
    }

    public function getModulesList()
    {
        $modules = array();
        foreach ($this->find('.chapter') as $module) {
            $modules[] = $module->outertext;
        }

        return $modules;
    }

    public function getChapter($moduleSlug)
    {
        return $this->find('.chapter-link[href=modules/' . $moduleSlug . '.html]', 0)->parent;
    }

    public function getExercisesList()
    {
        $exercises = array();
        foreach ($this->find('block') as $exercise) {
            $id = $exercise->id;
            $title = $exercise->find('block-title view-element', 0)->innertext;
            $exercises[$id] = $title;
        }

        return $exercises;
    }

    public function inputModules($code)
    {
        $this->find('#moduleList', 0)->innertext = $code;
    }

    public function setChapterLink($href)
    {
        $this->find('.chapter-link', 0)->href = 'modules/' . $href . '.html';
    }

    public function setChapterTitle($moduleTitle)
    {
        $this->find('.chapter-link', 0)->innertext = $moduleTitle;
    }

    public function setModuleTitle($moduleTitle)
    {
        $this->find('module-title view-element', 0)->innertext = $moduleTitle;
    }

    public function setChapterAttributes($moduleSlug, $moduleTitle)
    {
        $this->setChapterLink($moduleSlug);
        $this->setChapterTitle($moduleTitle);
    }

    public function destroy()
    {
        $this->clear();
        unset($this);
    }

}


class SummaryDomElement extends simple_html_dom_node
{

    public function append($appendCode)
    {
        $this->innertext .= $appendCode;
    }

    public function prepend($prependCode)
    {
        $this->innertext = $prependCode . $this->innertext;
    }

    public function wrap($start, $end)
    {
        $this->innertext = $start . $this->innertext . $end;
    }

    public function insertExercisesIntoChapter($exercises)
    {
        $temp = $this->first_child()->outertext . '<ol class="exercises-list">';
        foreach ($exercises as $link => $title) {
            $temp .= '<li class="exercise"><a class="exercise-link" href="' . $link . '">
             ' . $title . '</a></li>';
        }
        $temp .= '</ol>';
        $this->innertext = $temp;
    }

}

