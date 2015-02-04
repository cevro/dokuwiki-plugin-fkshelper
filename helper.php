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
    /*
     * © Michal Červeňák
     * 
     * 
     * talčítko pre návrat do menu z admin prostredia (možno do pluginu fksadminpage ?FR
     */

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
     * @param string $text 
     * @param int $lvl
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
     * @param string $text for parsing
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
     * @author Michal Červeňák <miso@fykos.cz>
     * @param array $arr atributes 
     * @return string
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

    /**
     * @author Michal Červeňák <miso@fykos.cz>
     * @param string $dir
     * @param bool $subdir
     * @return array
     */
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

    private function renderstreamB() {
        $this->helper->Sdata['feeds'] = $this->helper->Sdata['feed'];
        foreach ($this->helper->loadstream() as $key => $value) {
            if ($this->helper->Sdata['feed']) {
                list($this->to_page['items'][], $this->to_page['img'][], $this->to_page['indic'][]) = $this->helper->renderfullnews($value, 'fksnewseven');
                $this->helper->Sdata['feed'] --;
            } else {
                break;
            }
        }


        foreach ($this->to_page['items'] as $k => $v) {
            $this->to_page['html_items'] .= '<div class="item';
            if ($k == 1) {
                $this->to_page['html_items'] .=' active';
            }
            $this->to_page['html_items'] .='">';
            $this->to_page['html_items'] .='
      <img src="' . $this->to_page['img'][$k] . '" alt="">
      <div class="carousel-caption">' . $v . '

                
                  </div>
    </div>';
            $this->to_page['html_indic'] .=' <li data-target="#carousel-example-generic" data-slide-to="' . $k . '" class="active"></li>';
        }
        return'      <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
    ' . $this->to_page['html_indic'] . '
  </ol>

  <!-- Wrapper for slides -->
   <div class="carousel-inner" role="listbox">
   ' . $this->to_page['html_items'] . '
       </div>
  

  <!-- Controls -->
  <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right"></span>
    <span class="sr-only">Next</span>
  </a>
</div>';
    }

}

/**
 * extend Doku html.php
 */

/**
 * @author Michal Červeňák <miso@fykos.cz>
 * @param string $name
 * @param string $class
 * @param array $params
 * @return string
 */
function html_facebook_btn($name = 'Share on FaceBook', $class = 'btn-social btn-facebook', $params = array()) {
    $r.= '<button  ' . buildAttributes($params) . ' class="' . $class . '">';
    $r.= '<i class="fa fa-facebook"></i>';
    $r.= $name . '</button>';
    return $r;
}

/**
 * @author Michal Červeňák <miso@fykos.cz>
 * @param string $name
 * @param string $class
 * @param array $params
 * @return string
 */
function html_button($name = 'btn', $class = 'btn', $params = array()) {
    $r.='<button ' . buildAttributes($params) . ' class="' . $class . '">';
    $r.=$name;
    $r.= '</button>';
    return $r;
}
