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

        $this->Lexer->addSpecialPattern('{{fl.*?\>.+?\|.+?}}',$mode,'plugin_fkshelper_fl');
    }

    /**
     * Handle the match
     */
    public function handle($match,$state) {

        preg_match('/{{\s*fl(.*)>(.*)\|(.*)}}/',$match,$matches);

        list(,$attrs,$link,$text) = $matches;
        preg_match('/\.([a-zA-z0-9-_]*)/',$attrs,$classs);
        preg_match('/\#([a-zA-z0-9-_]*)/',$attrs,$ids);

        return array($state,$link,$text,$classs[1],$ids[1]);
    }

    public function render($mode,Doku_Renderer &$renderer,$data) {

        if($mode == 'xhtml'){
            list($state,$link,$text,$class,$id) = $data;
            if(!$class){
                $class = "default";
            }
            
            $renderer->doc.='<div class="clearer"></div>';
            if(preg_match('/^http[s]:\/\//',$link)){               
                $renderer->doc.='<a href="'.htmlspecialchars($link).'">';
            }else{
                 $renderer->doc.='<a href="'.wl(cleanID($link)).'">';
            }            
            $renderer->doc.='<button class="fast_link '.urlencode($class).'" id="'.urlencode($id).'">';
            $renderer->doc.=htmlspecialchars(trim($text));
            $renderer->doc.='</button>';
            $renderer->doc.='</a>';

            return true;
        }
        return false;
    }

}
