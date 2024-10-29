<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class auto_hads_html_node {

    public $TAG = '';
    public $plaintext = '';
    private $element;
    public $innerHTML = '';
    private $link_target = '';
    private $xpath;

    /**
     * Content constructor.
     *
     * @param $element
     */
    public function auto_hads_html_node(DOMNode $element = NULL,$xpath = NULL, $link_target = '') {
        if ($element != NULL) {
            $this->TAG = $element->nodeName;
            $this->plaintext = trim($element->textContent);
            if ($element->attributes->length) {
                foreach ($element->attributes as $key => $attr) {
                    $this->$key = $attr->value;
                }
            }
            $this->innerHTML = $this->getInnerHTML($element);
            $this->element = $element;
            $this->xpath = $xpath;
            $this->link_target = $link_target;
        }
    }

    public function get($key) {
        if (!empty($key) && isset($this->$key)) {
            return $this->$key;
        }
        return '';
    }

    public function __get($key) {
//        if ( in_array( $key, array( 'payment_gateways', 'shipping', 'mailer', 'checkout' ), true ) ) {
//                return $this->$key();
//        }
    }

    private function getInnerHTML(DOMNode $element = NULL) {
        if ($element) {
            $innerHTML = "";
            $children = $element->childNodes;
            if($children){
                foreach ($children as $child) {
                    $innerHTML .= $element->ownerDocument->saveHTML($child);
                }
            }
            return $innerHTML;
        }
        return '';
    }
    public function replace_spac($html, $tags) {
        $htmlReturn = $html;
        if(empty($tags)){
            return $htmlReturn;
        }
        if(!is_array($tags))
        {
            $tag = $tags;
            $htmlReturn = preg_replace('#<('  .$tag . ')[^>]*>(.*?)</\1>#si', '[\1]\2[/\1]', $htmlReturn);
            return $htmlReturn;
        }
        foreach ($tags as $tag) {
            $htmlReturn = preg_replace('#<('  .$tag . ')[^>]*>(.*?)</\1>#si', '[\1]\2[/\1]', $htmlReturn);
        }
        return $htmlReturn;
    }
    
    private function getChildsNodeSkip($selector,$replaces = array(), $keep_img = false, $keep_tags = false, $keep_tables = false) {
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        if(empty($selector) && !$replaces && !$keep_img && !$keep_tags && !$keep_tables){
            return $this->getChildNodes($this->element);
        }
        $html = str_replace($replaces, '', $this->innerHTML);
        if ($keep_img) {
            $html = preg_replace('#</?img((\s+\w+(.*?=\s*(?:".*?"|\'.*?\'|[\^\'">\s]+))?)+\s*|\s*)/?>#', '[img]\1[/img]', $html);
        }
        if ($keep_tables) {
            $html = $this->replace_spac($html, array('table','thead|tbody|tfoot','tr','td|th'));
        }
        if ($keep_tags) {
            $html = $this->replace_spac($html, 'h1|h2|h3|h4|h5|h6|b|i|strong|em|ul|ol|li');
        }

        if (!empty($html)) {
            $html = $this->getEncoding($html);
        }
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXpath($dom);
        if (!empty($selector)) {
            $elements = $xpath->evaluate(auto_hads_selector_to_xpath($selector));
            for ($i = 0, $length = $elements->length; $i < $length; ++$i) {
                if ($elements->item($i)->nodeType == XML_ELEMENT_NODE) {
                    $item = $elements->item($i);
                    $item->parentNode->removeChild($item);
                }
            }
        }

        $body = $dom->getElementsByTagName('body');
        $items = $this->getChildNodes($body->item(0));

        return $items;
    }

    private function getEncoding($html) {
        return '<?xml encoding="utf-8" ?>' . $html;
    }

    private function download_add_attachment_img($keep_img = false, $download_img = false, $imgAlign = '') {
        if (!$keep_img) {
            return;
        }
        if(!$this->element)
        {
            return;
        }
        $elements = $this->xpath->query("//img",  $this->element);

        if (is_null($elements)) {
            return;
        }
        foreach ($elements as $element) {
            if ($element->attributes->length) {
                $attachs = array();
                foreach ($element->attributes as $key => $val) {
                    switch ($key) {
                        case 'src':
                            $attachs = $this->download_element_img($val, $download_img);
                            break;
                        case 'alt':
                        case 'class':
                            break;
                        default:
                            $element->removeAttribute($key);
                            break;
                    }
                }
                $class = 'alignnone size-full';
                if (!empty($imgAlign)) {
                    $class = 'align' . $imgAlign . ' size-full ';
                }

                if ($attachs) {
                    $class .= ' wp-image-' . $attachs['attach_id'];
                    $element->setAttribute('src', $attachs['attach_url']);
                }
                $element->setAttribute('class', $class);
            }
        }
       
        $this->innerHTML = $this->getInnerHTML($this->element);
    }
    
    private function download_element_img($val,$download) {
        if(!$download){
            return array();
        }
        $src = auto_hads_check_url($val->value, $this->link_target);
        return auto_hads_add_attachment_url_resize($src);
    }


    private function getChildNodes(DOMNode $element) {
        $children = $element->childNodes;
        $childs = array();
        foreach ($children as $child) {
            $childinfo = new auto_hads_html_node($child);
            $childs[] = $childinfo;
        }
        return $childs;
    }

    public function getContentText($replaces = array(), $imgAlign = '', $skipTags = '', $keep_img = false, $keep_tags = false, $keep_tables = false, $download_img = false) {
        $this->download_add_attachment_img($keep_img, $download_img, $imgAlign);
        $childs = $this->getChildsNodeSkip($skipTags,$replaces, $keep_img, $keep_tags, $keep_tables);
        $textHTML = '';
        foreach ($childs as $node) {
            $textHTML .= $node->plaintext . PHP_EOL . PHP_EOL;
        }
        $textHTML = str_replace('&nbsp;', ' ', $textHTML);

        $textHTML = preg_replace('/\[(h1|h2|h3|h4|h5|h6|b|i|strong|em|ul|ol|li)\][[:space:]]*\[\/\1\]/is', '', $textHTML);
        $textHTML = preg_replace('/\[(h1|h2|h3|h4|h5|h6|b|i|strong|em|ul|ol|li)\]\n\[\/\1\]/is', '', $textHTML);

        $textHTML = trim($textHTML);
        if ($keep_img) {
            $textHTML = preg_replace('/\[img\](.*?)\[\/img\]/is', '<img $1/>', $textHTML);
        }
        if ($keep_tags) {
            $textHTML = preg_replace('/\[(h1|h2|h3|h4|h5|h6|b|i|strong|em|ul|ol|li)\](.*?)\[\/\1\]/is', '<\1>\2</\1>', $textHTML);
        }
        if ($keep_tables) {

            $textHTML = preg_replace('/\[(table)\](.*?)\[\/\1\]/is', '<\1 class="table table-bordered table-responsive table-striped">\2</\1>', $textHTML);
            $textHTML = preg_replace('/\[(thead|tbody|tfoot)\](.*?)\[\/\1\]/is', '<\1>\2</\1>', $textHTML);
            $textHTML = preg_replace('/\[(tr)\](.*?)\[\/\1\]/is', '<\1>\2</\1>', $textHTML);
            $textHTML = preg_replace('/\[(td|th)\](.*?)\[\/\1\]/is', '<\1>\2</\1>', $textHTML);
        }
        return $textHTML;
    }

    public function find($selector) {
        
        $elements = $this->xpath->evaluate(auto_hads_selector_to_xpath($selector), $this->element);
        $array = array();
        for ($i = 0, $length = $elements->length; $i < $length; ++$i) {
            if ($elements->item($i)->nodeType == XML_ELEMENT_NODE) {
                $node = new auto_hads_html_node($elements->item($i),$this->xpath,  $this->link_target);
                $array[] = $node;
            }
        }
        return $array;
    }

}
