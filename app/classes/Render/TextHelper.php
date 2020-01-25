<?php

Loader::addIncludePath(APP_PATH . LIB_PATH . 'htmlpurifier');
Loader::import('HTMLPurifier', null, false);

class Render_TextHelper {
    
    const SHORT_SUMMARY = 100;
    const MEDIUM_SUMMARY = 200;
    const LONG_SUMMARY = 500;
    
    const UTF8 = 'utf-8';
    const ISO88591 = 'iso-8859-1';
    
    const STRICT_TAGS = '';
    const WYSIWYG_TAGS = 'p[class],a[href|title|class],img[src|alt|width|height|title|class],em,strong';
	
    public function summarize($string, $length = Render_TextHelper::MEDIUM_SUMMARY, $hellip = '&hellip;') {
        //Extract first paragraph.
        $no = preg_match('!<\s*p[^>]*>.*?<\s*/\s*p[^>]*>!',
                $string, $paras);
        if ($no > 0)
            $string = $paras[0];
        
        $string = $this->clean($string);
        
        if (strlen($string) <= $length) {
            return $string;
        } else {
            $summary = substr($string, 0, $length);
            $pos = strrpos($summary, " ");
            if ($pos !== false) {
                return substr($summary, 0, $pos).$hellip;
            } else {
                return $summary.$hellip;
            }
        }
    }    
    
    public function clean($string, $allowedtags = self::STRICT_TAGS, $enc = self::UTF8) {    
        $string = preg_replace('!(&nbsp;|\s|\t)+!', ' ', $string);
        $string = preg_replace('!(\n|\r)+!', '\n', $string);
        
        $config = HTMLPurifier_Config::createDefault();        
        $config->set('Cache', 'SerializerPath', APP_PATH . CACHE_PATH);
        $config->set('Core', 'Encoding', $enc);
        $config->set('HTML', 'Doctype', 'XHTML 1.0 Strict');
        $config->set('HTML', 'Allowed', $allowedtags);
        $purifier = new HTMLPurifier($config);
        
        $string = $purifier->purify($string);
        
        return trim($string);       
    }
    
    public function __toString() {
        return get_class($this);
    }
}