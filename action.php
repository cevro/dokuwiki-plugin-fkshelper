<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class action_plugin_fkshelper extends DokuWiki_Action_Plugin {

    public function __construct() {
        $this->helper = $this->loadHelper('fkshelper');
    }

    /**
     * 
     * @param Doku_Event_Handler $controller
     */
    public function register(Doku_Event_Handler $controller) {

        $controller->register_hook('ACTION_ACT_PREPROCESS','BEFORE',$this,'anti_spam');
        
        $controller->register_hook('AJAX_CALL_UNKNOWN','BEFORE',$this,'orgFloatPhoto');
        //$controller->register_hook('TPL_ACT_RENDER','BEFORE',$this,'RadioPlayer');
    }

   
    public function orgFloatPhoto(Doku_Event &$event,$param) {
        global $INPUT;
        if($INPUT->str('target') != 'person'){
            return;
        }
        header('Content-Type: application/json');
        $event->stopPropagation();
        $event->preventDefault();

        require_once DOKU_INC.'inc/JSON.php';
        $link = $this->helper->scaleOrgPhoto($INPUT->str('person_id'),80);
        $json = new JSON();
        echo $json->encode(array('src' => $link));
    }

    public function anti_spam(Doku_Event &$event,$param) {
        global $INPUT;
        $html_out = $this->getConf('deny_html_out');



        $deny_ip = array('');


        foreach ($deny_ip as $value) {
            if(($_SERVER['REMOTE_ADDR'] == $value) || ($INPUT->str('i_am_spamer') == 1)){
                header($_SERVER["SERVER_PROTOCOL"]." 418 HTCPCP Coffee not found");


                die($html_out);
            }
        }
    }

    public function RadioPlayer() {
        echo '<div class = "aside_media" style = "z-index:1000;background-color: black; position:fixed;bottom:0;right: 0; padding: 1em; border: solid 1px #ccc;">
        <span style = "font-weight:bold;color:red">Čo tak si sprijemníť testovanie dávkou hardrocku alebo metálu?</span>
        <br />
        <audio controls>
        <source src = "http://212.111.2.151:8000/rm_hard_256.mp3" type = "audio/mpeg">
        Your browser does not support the audio tag.
        </audio>
        </div>';
    }

}
