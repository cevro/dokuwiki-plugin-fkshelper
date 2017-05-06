<?php

class syntax_plugin_fkshelper_imageheadline extends DokuWiki_Syntax_Plugin {

    /**
     *
     * @var helper_plugin_social
     */
    private $social;

    public function __construct() {
        $this->social = $this->loadHelper('social');
    }

    public function getType() {
        return 'baseonly';
    }

    public function getPType() {
        return 'block';
    }

    public function getAllowedTypes() {
        return array();
    }

    public function getSort() {
        return 226;
    }

    public function connectTo($mode) {

        $this->Lexer->addSpecialPattern('=+\|=+.+?=+\|=+', $mode, 'plugin_fkshelper_imageheadline');
    }

    public function handle($match, $state) {

        preg_match('/(=+\|=+)\s+(.*)\s*\|\s*(.*)\s+(=+\|=+)/', $match, $matches);

        list(, $lvls, $text, $image) = $matches;
        $lvl = 7 - substr_count($lvls, '=');
        return array($state, array($lvl, $text, $image));
    }

    public function render($mode, Doku_Renderer $renderer, $data) {
        global $ID;
        list($lvl, $text, $image) = $data[1];
        if ($mode == 'metadata') {
            if ($lvl == 1) {
                if ($this->social) {
                    $this->social->meta->addMetaData('og', 'image', ml($image));
                }
                p_set_metadata($ID, array('title' => $text));
            }
        }
        if ($mode == 'xhtml') {

            $i = ml($image, array('w' => 600));
            $renderer->doc .= '<div class="image_headline" style="background-image:url(' . $i . ')">';


            $renderer->doc .= '<h' . $lvl . '>';
            $renderer->doc .= htmlspecialchars($text);
            $renderer->doc .= '</h' . $lvl . '>';
            $renderer->doc .= '</div>';
        }
    }

}
