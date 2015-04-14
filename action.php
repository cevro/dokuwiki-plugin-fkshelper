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
    }

    public function anti_spam(Doku_Event &$event, $param) {
        global $INPUT;
        $html_out = $this->getConf('deny_html_out');


        
        $deny_ip = array('81.162.202.6');
        //$deny_ip = array('127.0.0.1', '81.162.202.6');

        foreach ($deny_ip as $value) {
            if (($_SERVER['REMOTE_ADDR'] == $value)||($INPUT->str('i_am_spamer') == 1)) {
                header($_SERVER["SERVER_PROTOCOL"] . " 418 HTCPCP Coffee not found");
               
                
                die($html_out);
                 
            }
        }
    }

}

/*
 * <html>
    <head>
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script>
        jQuery(function(){
        
            var $ = jQuery;
            $(window).load(function(){
            $("img").animate({width:"500%",},20000);
            });
        });
        </script>
    </head>
    <body style="overflow:hidden;">
        <div style="margin-left:auto;margin-right:auto;width:50%;text-align:center">
            <h1>You are fucking spamer</h1>
           
        </div>    
        <img src="http://cdn.meme.am/instances/400x/54995689.jpg" style="position:absolute" alt="fuck you bro"/>
    </body>
</html>

 */
