<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class action_plugin_fkshelper extends DokuWiki_Action_Plugin {

    /**
     * 
     * @param Doku_Event_Handler $controller
     */
    public function register(Doku_Event_Handler $controller) {

        $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'anti_spam');
        $controller->register_hook('PARSE_INI_FILE','BEFORE',$this,'change_ini');
    }
    
    public function change_ini(Doku_Event &$event, $param){
        
      //  $event->data['ini']='/var/www/html/vyfuk_web/lib/tpl/vyfuk/style.ini';
        $event->data['ini']='/var/www/html/vyfuk_web/lib/tpl/vyfuk/style_valentine.ini';
        $event->data['cashe']->removeCache();
        //var_dump($event);
    }

    public function anti_spam(Doku_Event &$event, $param) {
        global $INPUT;
        $html_out = $this->getConf('deny_html_out');


        
        $deny_ip = array('');
        

        foreach ($deny_ip as $value) {
            if (($_SERVER['REMOTE_ADDR'] == $value)||($INPUT->str('i_am_spamer') == 1)) {
                header($_SERVER["SERVER_PROTOCOL"] . " 418 HTCPCP Coffee not found");
               
                
                die($html_out);
                 
            }
        }
    }

}

