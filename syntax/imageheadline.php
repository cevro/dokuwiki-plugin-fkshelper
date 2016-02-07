<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of imageheadline
 *
 * @author root
 */
class syntax_plugin_fkshelper_imageheadline extends DokuWiki_Syntax_Plugin  {
    
    
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

        $this->Lexer->addSpecialPattern('=+\|=+.+?=+\|=+',$mode,'plugin_fkshelper_imageheadline');
    }

    /**
     * Handle the match
     */
    public function handle($match,$state) {

    
       preg_match('/(=+\|=+)\s+(.*)\s*\|\s*(.*)\s+(=+\|=+)/',$match,$matches);
   
       list(,$lvls,$text,$image)= $matches;
       $lvl = 7-substr_count($lvls,'=');
        return array($state,array($lvl,$text,$image));
    }

    public function render($mode,Doku_Renderer $renderer,$data) {
        global $ID;
        list($lvl,$text,$image)=$data[1];
       if($mode == 'metadata'){
           if($lvl ==1){
                  p_set_metadata($ID,array('title'=>$text));
           }
        
           //var_dump($renderer);
       }
       if($mode == 'xhtml'){
           
           $i = ml($image,array('w'=>600));
           $renderer->doc .= '<div class="image_headline" style="background-image:url('.$i.')">';
           
           
           $renderer->doc .= '<h'.$lvl.'>';
           $renderer->doc .= htmlspecialchars($text);
           $renderer->doc .= '</h'.$lvl.'>';
           $renderer->doc .= '</div>';
           
           
       }
    }
  
}
