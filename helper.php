<?php

/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Michal Červeňák <miso@fykos.cz>
 */
// must be run within Dokuwiki
if (!defined('DOKU_INC')) {
    die();
}

class helper_plugin_fkshelper extends DokuWiki_Plugin {

    function returnMenu() {
        global $lang;
        $form = new Doku_Form(array(
            'id' => "returntomenu",
            'method' => 'POST',
            'action' => DOKU_BASE . "?do=admin"
        ));

        $form->addElement(form_makeButton('submit', '', $this->getLang('returntomenu')));
        html_form('returntomenu', $form);
    }

    /*
     * © Michal Červeňák
     * 
     * 
     * 
     * msg return html not print
     */

    function returnmsg($text, $lvl) {
        ob_start();
        msg($text, $lvl);
        $msg = ob_get_contents();
        ob_end_clean();
        return $msg;
    }

    /*
     * © Michal Červeňák
     * 
     * 
     * 
     * extract param from text
     */

    public static function extractParamtext($text) {
        foreach (preg_split('/;/', $text)as $key => $value) {
            list($k, $v) = preg_split('/=/', $value);
            $param[$k] = $v;
        }
        return $param;
    }

    public static function buildStyle($arr) {
        $r = "";
        if (is_array($arr)) {
            foreach ($arr as $key => $value) {
                $r.=rawurldecode($key) . ':';
                $r.=rawurldecode($value) . ';';
            }
        } else {
            $r.=str_replace(',', ';', $arr);
        }
        //var_dump($r);
        //print_r($arr);
        return $r;
    }

    public static function filefromdir($dir, $subdir = true) {
        if ($subdir) {
            $result = array();
            $cdir = scandir($dir);
            foreach ($cdir as $key => $value) {
                if (!in_array($value, array(".", ".."))) {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                        $result = array_merge($result, self::filefromdir($dir . DIRECTORY_SEPARATOR . $value));
                    } else {
                        $result[] = $dir . DIRECTORY_SEPARATOR . $value;
                    }
                }
            }
        } else {
            $result = array();
            $cdir = scandir($dir);
            foreach ($cdir as $key => $value) {
                if (!in_array($value, array(".", ".."))) {
                    if (!is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                        $result[] = $dir . DIRECTORY_SEPARATOR . $value;
                    }
                }
            }
        }
        return $result;
    }

}
