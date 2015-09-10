<?php

/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Michal Červeňák <miso@fykos.cz>
 */
// must be run within Dokuwiki
if(!defined('DOKU_INC')){
    die();
}

class syntax_plugin_fkshelper_person extends DokuWiki_Syntax_Plugin {

    public $helper;
   

    function __construct() {
        $this->helper = $this->loadHelper('fkshelper');
    }

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
        $this->Lexer->addSpecialPattern('{{person>.+?}}',$mode,'plugin_fkshelper_person');
    }


    public function handle($match,$state,$pos,Doku_Handler &$handler) {
        
        $id=trim(substr($match,9,-2));
        
        $data = $this->helper->getOrgData($id);
      
      
       return($data);
    }

    /**
     * Render xhtml output or metadata
     *
     * @param string         $mode      Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer  $renderer  The renderer
     * @param array          $data      The data from the handler() function
     * @return bool If rendering was successful.
     */
    public function render($mode,Doku_Renderer &$renderer,$data) {
        //var_dump($data);
         
        if($mode == 'xhtml'){
           $renderer->doc.='<span class="org" id="person'.$data['person_id'].'">'."\n".
                   '<a href="'.wl(cleanID($this->getConf('org_page'))).'#'.$data['person_id'].'">'.$data['name'].'</a></span>';
            
            
            
            return true;
        }else if($mode == 'metadata'){
           

            return true;
        }
        return false;
    }

    


}
