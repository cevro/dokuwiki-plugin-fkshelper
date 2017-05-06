<?php

class action_plugin_fkshelper extends DokuWiki_Action_Plugin {
    /**
     * @var helper_plugin_fkshelper
     */
    private $helper;

    public function __construct() {
        $this->helper = $this->loadHelper('fkshelper');
    }

    public function register(Doku_Event_Handler $controller) {
        //   $controller->register_hook('ACTION_ACT_PREPROCESS','BEFORE',$this,'antiSpam');
    }

    public function antiSpam() {
        global $INPUT;
        $html_out = $this->getConf('deny_html_out');

        $deny_ip = array('');

        foreach ($deny_ip as $value) {
            if (($_SERVER['REMOTE_ADDR'] == $value) || ($INPUT->str('i_am_spamer') == 1)) {
                header($_SERVER["SERVER_PROTOCOL"] . " 418 HTCPCP Coffee not found");
                die($html_out);
            }
        }
    }
}
