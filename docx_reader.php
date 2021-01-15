<?php

class Docx_reader {

    private $fileData = false;
    private $errors = array();
    private $styles = array();

    public function __construct() {

    }

    private function load($file) {

        if (file_exists($file)) {
            $zip = new ZipArchive();
            $openedZip = $zip->open($file);
            if ($openedZip === true) {
                //attempt to load styles:
                if (($styleIndex = $zip->locateName('word/styles.xml')) !== false) {
                    $stylesXml = $zip->getFromIndex($styleIndex);
                    $xml = simplexml_load_string($stylesXml);
                    $namespaces = $xml->getNamespaces(true);

                    $children = $xml->children($namespaces['w']);

                    foreach ($children->style as $s) {
                        $attr = $s->attributes('w', true);
                        if (isset($attr['styleId'])) {
                            $tags = array();
                            $attrs = array();
                            foreach (get_object_vars($s->rPr) as $tag => $style) {
                                $att = $style->attributes('w', true);
                                switch ($tag) {
                                    case "b":
                                        $tags[] = 'strong';
                                        break;
                                    case "i":
                                        $tags[] = 'em';
                                        break;
                                    /*case "color":
                                        //echo (String) $att['val'];
                                        $attrs[] = 'color:#' . $att['val'];
                                        break;*/
                                    case "sz":
                                        $attrs[] = $att['val'];
                                        break;
                                }
                            }
                            $styles[(String)$attr['styleId']] = array('tags' => $tags, 'attrs' => $attrs);
                        }
                    }
                    $this->styles = $styles;
                }

                if (($index = $zip->locateName('word/document.xml')) !== false) {
                    // If found, read it to the string
                    $data = $zip->getFromIndex($index);
                    // Close archive file
                    $zip->close();
                    return $data;
                }
                $zip->close();
            }
            else {
                switch($openedZip) {
                    case ZipArchive::ER_EXISTS:
                        $this->errors[] = 'File exists.';
                        break;
                    case ZipArchive::ER_INCONS:
                        $this->errors[] = 'Inconsistent zip file.';
                        break;
                    case ZipArchive::ER_MEMORY:
                        $this->errors[] = 'Malloc failure.';
                        break;
                    case ZipArchive::ER_NOENT:
                        $this->errors[] = 'No such file.';
                        break;
                    case ZipArchive::ER_NOZIP:
                        $this->errors[] = 'File is not a zip archive.';
                        break;
                    case ZipArchive::ER_OPEN:
                        $this->errors[] = 'Could not open file.';
                        break;
                    case ZipArchive::ER_READ:
                        $this->errors[] = 'Read error.';
                        break;
                    case ZipArchive::ER_SEEK:
                        $this->errors[] = 'Seek error.';
                        break;
                }
            }
        } else {
            $this->errors[] = 'File does not exist.';
        }
    }

    public function setFile($path) {
        $this->fileData = $this->load($path);
    }

    public function to_plain_text() {
        if ($this->fileData) {
            return strip_tags($this->fileData);
        } else {
            return false;
        }
    }

    public function to_html($path) {
        $h1 = str_replace(["./text_doc/",".docx"],["",""], $path);

        if ($this->fileData) {
            $xml = simplexml_load_string($this->fileData);
            $namespaces = $xml->getNamespaces(true);

            $children = $xml->children($namespaces['w']);
            $html = '';

            foreach ($children->body->p as $p) {
                $style = '';


                $startTags = array();
                $startAttrs = array();

                if($p->pPr->pStyle) {
                    $objectAttrs = $p->pPr->pStyle->attributes('w',true);
                    $objectStyle = (String) $objectAttrs['val'];
                    if(isset($this->styles[$objectStyle])) {
                        $startTags = $this->styles[$objectStyle]['tags'];
                        $startAttrs = $this->styles[$objectStyle]['attrs'];
                    }
                }



                if ($p->pPr->spacing) {
                    $att = $p->pPr->spacing->attributes('w', true);
                    if (isset($att['before'])) {
                        $style.='padding-top:' . ($att['before'] / 10) . 'px;';
                    }
                    if (isset($att['after'])) {
                        $style.='padding-bottom:' . ($att['after'] / 10) . 'px;';
                    }
                }

                $html.='<p>';
                $li = false;
                $title = '';
                $title2 ='';
                if ($p->pPr->numPr) {
                    $li = true;
                    $html.='<li>';
                }

                foreach ($p->r as $part) {
                    //echo $part->t;
                    $tags = $startTags;
                    $attrs = $startAttrs;

                    foreach (get_object_vars($part->pPr) as $k => $v) {
                        if ($k = 'numPr') {
                            $tags[] = 'li';
                        }
                    }

                    foreach (get_object_vars($part->rPr) as $tag => $style) {
                        //var_dump($style->attributes());
                        $att = $style->attributes('w', true);

                        switch ($tag) {
                            case "b":
                                $tags[] = 'strong';
                                break;
                            case "i":
                                $tags[] = 'em';
                                break;
                            /*case "color":
                                //echo (String) $att['val'];
                                $attrs[] = 'color:#' . $att['val'];
                                break;*/
                            case "sz":
                                $attrs[] =  $att['val'];
                                break;
                        }
                    }
                    $openTags = '';
                    $closeTags = '';
                    foreach ($tags as $tag) {
                        $openTags.='<' . $tag . '>';
                        $closeTags.='</' . $tag . '>';
                    }

                    if(empty($attrs)){
                        $html .= $openTags . $part->t . $closeTags;
                    }else {

                        switch (implode(';', $attrs)){
                            case '52':
                            case '32':
                                $title.=  $openTags . $part->t . $closeTags;

                                break;
                            case '28':
                                $title2.=  $openTags . $part->t . $closeTags;
                                break;
                            default:
                                $html .= '<span>' . $openTags . $part->t . $closeTags . '</span>';
                        }

                        /*$html .= '<span style="' . implode(';', $attrs) . '">' . $openTags . $part->t . $closeTags . '</span>';*/
                    }

                }

                if ($li) {
                    $html.='</li>';
                }

                if(!empty($title)){
                    $html.= "<h1>".$title."</h1>";
                }elseif (!empty($title2)){
                    $html.= "<h2>".$title2."</h2>";
                }

                $html.="</p>";
            }



            //Trying to weed out non-utf8 stuff from the file:
            $regex = <<<'END'
/
  (
    (?: [\x00-\x7F]                 # single-byte sequences   0xxxxxxx
    |   [\xC0-\xDF][\x80-\xBF]      # double-byte sequences   110xxxxx 10xxxxxx
    |   [\xE0-\xEF][\x80-\xBF]{2}   # triple-byte sequences   1110xxxx 10xxxxxx * 2
    |   [\xF0-\xF7][\x80-\xBF]{3}   # quadruple-byte sequence 11110xxx 10xxxxxx * 3 
    ){1,100}                        # ...one or more times
  )
| .                                 # anything else
/x
END;
            preg_replace($regex, '$1', $html);
            $html = $this->clean_html(htmlspecialchars($html), $h1);
            return $html;
            exit();
        }
    }

    public function get_errors() {
        return $this->errors;
    }

    private function getStyles() {

    }

    public function clean_html($html, $h1){
        $result = array();
        $clean_tags = array("&lt;p&gt;&lt;/p&gt;", "&lt;p&gt; &lt;/p&gt;", "&lt;strong&gt; &lt;/strong&gt;","&lt;strong&gt;&lt;/strong&gt;");
        $change_string_for_clean_tags = array("","","","");
        preg_match('/(\&lt\;h1\&gt\;)(.+)?(\&lt\;\/h1\&gt\;)/', $html, $arr);

        $result['title'] = $h1;
        $result['text'] =str_replace($arr[0], "", $html);
        $result['text'] = str_replace($clean_tags,$change_string_for_clean_tags, $result['text']);
        $result['metaTitle'] = $h1.' в Москве - Автосервис "Роверсити"';
        $result['metaDescription'] = $h1. ' в Москве. Бесплатная диагностика. Гарантия качества. Записаться - 8(495)150-70-69.';
        $result['metaKeywords'] = $h1;

        return $result;
    }
}
