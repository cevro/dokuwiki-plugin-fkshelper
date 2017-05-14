<?php

require_once(DOKU_INC . 'inc/search.php');
require_once(DOKU_INC . 'inc/JpegMeta.php');

class syntax_plugin_fkshelper_images extends DokuWiki_Syntax_Plugin {
    const POSITION_LEFT = 'left';
    const POSITION_RIGHT = 'right';
    const POSITION_CENTER = 'center';

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

    public function getSort() {
        return 227;
    }

    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('{{im.*?\>.+?\|.*?\|.*?}}', $mode, 'plugin_fkshelper_images');
        $this->Lexer->addSpecialPattern('{{ib.*?\>.+?\|.*?\|.*?}}', $mode, 'plugin_fkshelper_images');
    }

    public function handle($match, $state) {
        $matches = [];
        preg_match('/{{([a-z]+)(.*)>(.+?)}}/', $match, $matches);
        list(, $type, $attributes, $p) = $matches;
        $attributes = $this->helper->matchClassesNIDs($attributes);
        $data = $this->parseImageData($p);
        return [$state, [$data, $attributes, $type]];
    }

    public function render($mode, Doku_Renderer &$renderer, $data) {
        if ($mode == 'xhtml') {
            /** @var Doku_Renderer_xhtml $renderer */
            list($state, $matches) = $data;
            switch ($state) {
                case DOKU_LEXER_SPECIAL:
                    list($imageData, $attributes, $type) = $matches;
                    $param = [
                        'class' => 'image-show image-link ' . $type . ' ' . $attributes['classes'],
                        'id' => $attributes['id']
                    ];
                    $imgSize = 360;

                    if ($imageData['image'] == null) {
                        $renderer->nocache();
                        $renderer->doc .= '<a href="' . (preg_match('|^http[s]?://|',
                                trim($imageData['href'])) ? htmlspecialchars($imageData['href']) : wl(cleanID($imageData['href']))) .
                            '">' . htmlspecialchars($imageData['label']) . '</a>';
                    } else {
                        $this->printImageDiv($renderer,
                            $imageData['image']['id'],
                            $imageData['label'],
                            $imageData['href'],
                            $type == 'im',
                            $imgSize,
                            $param);
                    }
                    return true;
            }
        }
        return false;
    }

    private function parseImageData($m) {
        global $conf;
        list($gallery, $href, $label) = preg_split('~(?<!\\\)' . preg_quote('|', '~') . '~', $m);
        $image = ['id' => pathID($gallery)];

        if (!file_exists(mediaFN($gallery)) || is_dir(mediaFN($gallery))) {
            search($files, $conf['mediadir'], 'search_media', [], utf8_encodeFN(str_replace(':', '/', trim($gallery))));
            if (count($files)) {
                $image = $files[array_rand($files)];
                unset($files);
            }
        }
        return ['image' => $image, 'href' => $href, 'label' => $label];
    }

    private function printImageDiv(Doku_Renderer $renderer, $imageID, $label, $href, $full = true, $imgSize = 420, $param = []) {
        $renderer->doc .= '<div ' . buildAttributes($param) . '>';
        $renderer->doc .= '<div class="image-container">';
        $renderer->doc .= $href ? ('<a href="' .
            (preg_match('|^http[s]?://|', trim($href)) ? htmlspecialchars($href) : wl(cleanID($href))) . '">') : '';
        $renderer->doc .= $full ? $this->printFullImage($imageID, $imgSize) : $this->printBackgroundImage($imageID,
            $imgSize);
        $renderer->doc .= $this->printLabel($label);
        $renderer->doc .= $href ? '</a>' : '';
        $renderer->doc .= '</div>';
        $renderer->doc .= '</div>';
    }

    private function printLabel($label) {
        return $label ? '<div class="title display-4"><span class="icon"></span><strong>' . htmlspecialchars($label) .
            '</strong></div>' : '';
    }

    private function printBackgroundImage($image, $size) {
        return '<div class="image" style="background-image: url(\'' . ml($image, ['w' => $size]) . '\')"></div>';
    }

    private function printFullImage($image, $size) {
        return '<img src="' . ml($image, ['w' => $size]) . '"/>';
    }

    private function findPosition($match) {

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
}
