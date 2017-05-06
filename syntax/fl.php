<?php

/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Michal Červeňák <miso@fykos.cz>
 */
// must be run within Dokuwiki
if (!defined('DOKU_INC')) {
    die();
}

class syntax_plugin_fkshelper_fl extends DokuWiki_Syntax_Plugin {
    /**
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
        return 226;
    }

    public function connectTo($mode) {

        $this->Lexer->addSpecialPattern('{{fl.*?\>.+?\|.+?}}', $mode, 'plugin_fkshelper_fl');
    }

    public function handle($match, $state) {
        preg_match('/{{\s*fl(.*)>(.*)\|(.*)}}/', $match, $matches);
        list(, $attributes, $link, $text) = $matches;
        $attributes = $this->helper->matchClassesNIDs($attributes);
        return array($state, $link, $text, $attributes);
    }

    public function render($mode, Doku_Renderer &$renderer, $data) {
        global $ID;
        if ($mode == 'xhtml') {
            list($state, $link, $text, $attributes) = $data;

            if (preg_match('|^http[s]?://|', $link)) {
                $renderer->doc .= '<a href="' . htmlspecialchars($link) . '">';
            } else {
                /** FUCK dokuwiki  */
                if (preg_match('/([^#]*)(#.*)/', $link, $ms)) {
                    list(, $id, $hash) = $ms;
                    $id = $id ?: $ID;
                    $renderer->doc .= '<a href="' . wl(cleanID($id)) . $hash . '">';
                } else {
                    $renderer->doc .= '<a href="' . wl(cleanID($link)) . '">';
                }
            }
            $renderer->doc .= '<span class="fast_link ' . hsc($attributes['classes']) . '" id="' . hsc($attributes['id']) . '">';
            $renderer->doc .= htmlspecialchars(trim($text));
            $renderer->doc .= '</span>';
            $renderer->doc .= '</a>';
            return true;
        }
        return false;
    }
}
