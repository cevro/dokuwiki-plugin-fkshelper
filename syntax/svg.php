<?php

/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Michal Červeňák <miso@fykos.cz>
 */
// must be run within Dokuwiki
if(!defined('DOKU_INC')){
    die();
}

class syntax_plugin_fkshelper_svg extends DokuWiki_Syntax_Plugin {

    public function getType() {
        return 'substition';
    }

    public function getPType() {
        return 'normal';
    }

    public function getAllowedTypes() {
        return array('formatting','substition','disabled');
    }

    public function getSort() {
        return 226;
    }

    public function connectTo($mode) {

        $this->Lexer->addSpecialPattern('<svg-data\s.*/>',$mode,'plugin_fkshelper_svg');
    }

    /**
     * Handle the match
     */
    public function handle($match,$state) {

        preg_match('#src="(.*)"#',$match,$matches);
        list(,$src) = $matches;

        return array($state,array('src' => trim($src)));
    }

    public function render($mode,Doku_Renderer &$renderer,$data) {
        if($mode == 'xhtml'){
            list(,$data) = $data;
            $p = pathinfo($data['src']);
            if($p['extension']=='svg'){
                $patch=  mediaFN($data['src']);
                
                $renderer->doc.=io_readFile($patch);
            }else{
                msg('File is not svg',-1);
            }
            


            return true;
        }else{
            return false;
        }
    }

}
