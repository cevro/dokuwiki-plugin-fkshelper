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

    /**
     * talčítko pre návrat do menu z admin prostredia 
     * 
     * @author Michal Červeňák
     * @return void
     * @param null
     */
    public function returnMenu() {
        $form = new Doku_Form(array(
            'id' => "returntomenu",
            'method' => 'POST',
            'action' => DOKU_BASE . "?do=admin"
        ));

        $form->addElement(form_makeButton('submit', '', $this->getLang('returntomenu')));
        html_form('returntomenu', $form);
    }

    /**
     * call form not class
     * @return void
     * @param null
     */
    public static function _returnMenu() {
        $helper = new helper_plugin_fkshelper;
        $helper->returnMenu();
    }

    /**
     * msg return html not print
     * @author = Michal Červeňák
     * @return string html of msg
     * 
     * 
     */
    public static function returnmsg($text, $lvl) {
        ob_start();
        msg($text, $lvl);
        $msg = ob_get_contents();
        ob_end_clean();
        return $msg;
    }

    /**
     * @author Michal Červeňák
     * @param string text for parsing
     * @return array parameters
     * 
     * extract param from text
     */
    public static function extractParamtext($text) {
        foreach (preg_split('/;/', $text)as $key => $value) {
            list($k, $v) = preg_split('/=/', $value);
            $k = str_replace(array("\n", " "), '', $k);
            $param[$k] = $v;
        }
        return $param;
    }

    /**
     * 
     */

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

/*
 * extend Doku html.php
 */

function html_facebook_btn($name = 'Share on FaceBook', $class = 'btn-social btn-facebook', $param = array()) {
    $r.= '<button  ' . buildAttributes($param) . ' class="' . $class . '">';
    $r.= '<i class="fa fa-facebook"></i>';
    $r.= $name . '</button>';
    return $r;
}

function html_button($name = 'btn', $class = 'btn', $params = array()) {
    $r.='<button ' . buildAttributes($params) . ' class="' . $class . '">';
    $r.=$name;
    $r.= '</button>';
    return $r;
}
