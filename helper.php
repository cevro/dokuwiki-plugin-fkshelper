<?php

require_once __DIR__ . '/fks/NavBar/BootstrapNavBar.php';

class helper_plugin_fkshelper extends DokuWiki_Plugin {
    const POSITION_LEFT = 'left';
    const POSITION_RIGHT = 'right';
    const POSITION_CENTER = 'center';

    /**
     * talčítko pre návrat do menu z admin prostredia
     *
     * @author Michal Červeňák <miso@fykos.cz>
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
     * extract param from text
     * @author Lukáš Ledvina
     * @param string $text for parsing
     * @return array parameters
     *
     *
     */
    public static function extractParamtext($text, $delimiter = ';', $sec_delimiter = '=', $packer = '"') {
        $param = array();
        $k = $v = "";
// state:
//  0: init
//  1: wait for value
//  2: wait for "end value
//  3: final state
//  4: error state
        $index = 0;
        $state = 0;
        while (true) {
            list($nindex, $nActChar) = self::getNextActiveChar($text, $index, $delimiter, $sec_delimiter, $packer);
            switch ($state) {
                case 0:
                    switch ($nActChar) {
                        case 0: // ;
                        case 3: // null
                            $k = trim(substr($text, $index, $nindex - $index));
                            $v = true;
                            if (!self::testKey($k)) $state = 4; else $param[$k] = $v;
                            break;
                        case 1: // =
                            $k = trim(substr($text, $index, $nindex - $index));
                            if (!self::testKey($k)) $state = 4; else $state = 1;
                            break;
                        case 2: // "
                            $state = 4; // error
                            break;
                        case 4: // white only
                            $state = 3; // end
                            break;
                    }
                    break;
                case 1:
                    switch ($nActChar) {
                        case 0: // ;
                        case 3: // null
                        case 4: // white only
                            $v = trim(substr($text, $index, $nindex - $index));
                            if ($v == "") {
                                msg("extractParamtext: parse warning: empty value after =.", -1);
                                $v = true;
                            }
                            $param[$k] = $v;
                            $state = 0;
                            break;
                        case 1: // =
                            msg("extractParamtext: parse error: 2x = in one expr.", -1);
                            $state = 4;
                            break;
                        case 2: // "
                            if (trim(substr($text, $index, $nindex - $index)) == "") {
                                $state = 2;
                            } else {
                                msg("extractParamtext: parse error: chars between = and \".", -1);
                                $state = 4;
                            }
                            break;
                    }
                    break;
                case 2:
                    $nindex = strlen($text) + 1;
                    for ($i = $index; $i < strlen($text); $i++) {
                        if ($text[$i] == $packer) {
                            $nindex = $i + 1;
                            break;
                        }
                    }
                    $v = substr($text, $index, $nindex - $index - 1);
                    if ($v == "") $v = true;
                    $param[$k] = $v;
                    $state = 0;
                    break;
                case 3:
                    return $param;
                case 4:
                    return $param;
            }
            $index = $nindex + 1;
        }
    }

    /**
     * test if key is valid (for extractParamtext)
     * @author Lukáš Ledvina
     * @param string $key
     * @return bool
     *
     *
     */
    private static function testKey($text) {
        $ret = ctype_alnum($text);
        if (!$ret) {
            msg("extractParamtext: parse error: Key \"" . $text . "\" is not valid", -1, '', '', MSG_USERS_ONLY);
        }
        return $ret;
    }

    /**
     * get next active char from text (for extractParamtext)
     * @author Lukáš Ledvina
     * @param string $text for parsing, $begin of parsing
     * @return array (position,type)
     *
     *
     */
    private static function getNextActiveChar($text, $begin, $delimiter = ';', $sec_delimiter = '=', $packer = '"') {
        if (trim(substr($text, $begin)) == "") return array(strlen($text), 4);
        for ($i = $begin; $i < strlen($text); $i++) {
            switch ($text[$i]) {
                case $delimiter:
                    return array($i, 0);
                case $sec_delimiter:
                    return array($i, 1);
                case $packer:
                    return array($i, 2);
            }
        }
        return array(strlen($text), 3);
    }

    public static function _is_even($i) {
        if ($i % 2) {
            return 'even';
        } else {
            return 'odd';
        }
    }

    public function matchClassesNIDs($attributes) {
        // match classes
        preg_match_all('/\.([a-zA-z0-9-_]*)/', $attributes, $classes);
        // match ID
        preg_match('/\#([a-zA-z0-9-_]*)/', $attributes, $id);

        return ['classes' => implode(" ", $classes[1]), 'id' => $id[1]];
    }


    public function findPosition($match) {

        if (preg_match('/\s+(.+)\s+/', $match)) {
            return self::POSITION_CENTER;
        } elseif (preg_match('/(.+)\s+/', $match)) {
            return self::POSITION_LEFT;
        } elseif (preg_match('/\s+(.+)/', $match)) {
            return self::POSITION_RIGHT;
        } else {
            return self::POSITION_CENTER;
        }
    }

    public function printLabel($label) {
        return $label ? '<div class="title"><span class="icon"></span><span class="label">' . htmlspecialchars($label) . '</span></div>' : '';
    }

    public function printBackgroundImage($image, $size) {
        return '<div class="image" style="background-image: url(\'' . ml($image, ['w' => $size]) . '\')"></div>';
    }

    public function printFullImage($image, $size) {
        return '<img src="' . ml($image, ['w' => $size]) . '"/>';
    }

    public function parseImageData($m) {
        global $conf;
        list($gallery, $href, $label) = preg_split('~(?<!\\\)' . preg_quote('|', '~') . '~', $m);
        $image = ['id' => pathID($gallery)];
        $position = $this->findPosition($gallery);
        if (!file_exists(mediaFN($gallery)) || is_dir(mediaFN($gallery))) {
            search($files, $conf['mediadir'], 'search_media', [], utf8_encodeFN(str_replace(':', '/', trim($gallery))));
            if (count($files)) {
                $image = $files[array_rand($files)];
                unset($files);
            }
        }
        return ['image' => $image, 'href' => $href, 'label' => $label, 'position' => $position];
    }

    public function printImageDiv(Doku_Renderer $renderer, $imageID, $label, $href, $full = true, $imgSize = 420, $param = []) {
        $renderer->doc .= '<div ' . buildAttributes($param) . '>';
        $renderer->doc .= '<div class="image-container">';
        $renderer->doc .= $href ? ('<a href="' . (preg_match('|^http[s]?://|', trim($href)) ? htmlspecialchars($href) : wl(cleanID($href))) . '">') : '';
        $renderer->doc .= $full ? $this->printFullImage($imageID, $imgSize) : $this->printBackgroundImage($imageID, $imgSize);
        $renderer->doc .= $this->printLabel($label);
        $renderer->doc .= $href ? '</a>' : '';
        $renderer->doc .= '</div>';
        $renderer->doc .= '</div>';
    }

}


