<?php

require_once(DOKU_INC . 'inc/search.php');
require_once(DOKU_INC . 'inc/JpegMeta.php');

class syntax_plugin_fkshelper_im extends DokuWiki_Syntax_Plugin {

    /**
     *
     * @var helper_plugin_fkshelper
     */
    private $helper;

    public function __construct() {
        $this->helper = $this->loadHelper('fkshelper');
    }

    public function getType() {
        return 'substition';
    }

    public function getPType() {
        return 'block';
    }

    public function getAllowedTypes() {
        return [];
    }

    public function getSort() {
        return 227;
    }

    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('{{im.*?\>.+?\|.*?\|.*?}}', $mode, 'plugin_fkshelper_im');
    }

    public function handle($match, $state) {
        $matches = [];
        preg_match('/{{im(.*)>(.+?)}}/', $match, $matches);
        list(, $attributes, $p) = $matches;
        $attributes = $this->helper->matchClassesNIDs($attributes);
        $data = $this->helper->parseImageData($p);
        return array($state, [$data, $attributes, 'im']);
    }

    public function render($mode, Doku_Renderer &$renderer, $data) {
        global $ID;
        if ($mode == 'xhtml') {
            /** @var Doku_Renderer_xhtml $renderer */
            list($state, $matches) = $data;
            list($data, $attributes, $type) = $matches;
            $param = [
                'class' => 'image-show image-link ' . $type . ' ' . $attributes['classes'],
                'id' => $attributes['id']
            ];
            $imgSize = 360;
            switch ($data['position']) {
                case 'left':
                    $param['class'] .= ' left';
                    break;
                case 'right':
                    $param['class'] .= ' right';
                    break;
                default :
                    $param['class'] .= ' center';
                    break;
            }
            if ($data['image'] == null) {
                $renderer->nocache();
                $renderer->doc .= '<a href="' . (preg_match('|^http[s]?://|', trim($data['href'])) ? htmlspecialchars($data['href']) : wl(cleanID($data['href']))) . '">' . htmlspecialchars($data['label']) . '</a>';
            } else {
                $this->helper->printImageDiv($renderer, $data['image']['id'], $data['label'], $data['href'], true, $imgSize, $param);
            }
        }

        return false;
    }


}
