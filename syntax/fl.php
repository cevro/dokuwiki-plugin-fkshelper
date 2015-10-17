<?php

/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Michal Červeňák <miso@fykos.cz>
 */
// must be run within Dokuwiki
if(!defined('DOKU_INC')){
    die();
}

class syntax_plugin_fkshelper_fl extends DokuWiki_Syntax_Plugin {

    public function getType() {
        return 'substition';
    }

    public function getPType() {
        return 'block';
    }

    public function getAllowedTypes() {
        return array('formatting','substition','disabled');
    }

    public function getSort() {
        return 226;
    }

    public function connectTo($mode) {

        $this->Lexer->addSpecialPattern('{{fl>.*\|.*}}',$mode,'plugin_fkshelper_fl');
    }

    /**
     * Handle the match
     */
    public function handle($match,$state) {
        //  var_dump($match);
        preg_match('/{{\s*fl\s*>(.*)\|(.*)}}/',$match,$matches);
        //var_dump($matches);
        list(,$link,$text) = $matches;

        return array($state,$link,$text);
    }

    public function render($mode,Doku_Renderer &$renderer,$data) {

        if($mode == 'xhtml'){
            list($state,$link,$text) = $data;

            $renderer->doc.='<div class="clearer"></div>';
            $renderer->doc.='<a href="'.wl(cleanID($link)).'">';
            $renderer->doc.='<button class="fast_link">';
            $renderer->doc.=htmlspecialchars(trim($text));
            $renderer->doc.='</button>';
            $renderer->doc.='</a>';
        }

        return true;
    }

}
