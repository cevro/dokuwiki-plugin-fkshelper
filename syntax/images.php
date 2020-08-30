<?php

use dokuwiki\Extension\SyntaxPlugin;

require_once(DOKU_INC . 'inc/search.php');
require_once(DOKU_INC . 'inc/JpegMeta.php');

class syntax_plugin_fkshelper_images extends SyntaxPlugin {
    const POSITION_LEFT = 'left';
    const POSITION_RIGHT = 'right';
    const POSITION_CENTER = 'center';

    public function getType(): string {
        return 'substition';
    }

    public function getPType(): string {
        return 'block';
    }

    public function getSort(): int {
        return 227;
    }

    public function connectTo($mode): void {
        $this->Lexer->addSpecialPattern('{{im.*?\>.+?\|.*?\|.*?}}', $mode, 'plugin_fkshelper_images');
        $this->Lexer->addSpecialPattern('{{ib.*?\>.+?\|.*?\|.*?}}', $mode, 'plugin_fkshelper_images');
    }

    public function handle($match, $state, $pos, \Doku_Handler $handler): array {
        $matches = [];
        preg_match('/{{([a-z]+)(.*)>(.+?)}}/', $match, $matches);
        [, $type, $attributes, $p] = $matches;
        $attributes = self::prepareSelectors($attributes);
        $data = $this->parseImageData($p);
        return [$state, [$data, $attributes, $type]];
    }

    public function render($mode, \Doku_Renderer $renderer, $data): bool {
        if ($mode == 'xhtml') {
            /** @var \Doku_Renderer_xhtml $renderer */
            [$state, $matches] = $data;
            switch ($state) {
                case DOKU_LEXER_SPECIAL:
                    [$imageData, $attributes, $type] = $matches;
                    $param = [
                        'class' => 'image-show image-link ' . $type . ' ' . $attributes['classes'],
                        'id' => $attributes['id'],
                    ];

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
                            $param);
                    }
                    return true;
            }
        }
        return false;
    }

    private function parseImageData(string $match): array {
        global $conf;
        [$gallery, $href, $label] = preg_split('~(?<!\\\)' . preg_quote('|', '~') . '~', $match);
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

    private function printImageDiv(Doku_Renderer $renderer, ?string $imageId, ?string $label, ?string $href, bool $full = true, array $param = []) {

        $renderer->doc .= '<div ' . buildAttributes($param) . '>';
        $renderer->doc .= '<figure class="image-container w-100 h-100">';
        $renderer->doc .= $href ? ('<a href="' .
            (preg_match('|^http[s]?://|', trim($href)) ? htmlspecialchars($href) : wl(cleanID($href))) . '">') : '';

        $renderer->doc .= $this->printImage($imageId, $full);
        $renderer->doc .= $this->printLabel($label);
        $renderer->doc .= $href ? '</a>' : '';
        $renderer->doc .= '</figure>';
        $renderer->doc .= '</div>';
    }

    private function printLabel(?string $label): string {
        if (!$label) {
            return '';
        }
        $html = '<figcaption class="caption d-flex align-items-center justify-content-center w-100 h-100">';
        $html .= '<div class="text-center">';
        $icon = false;
        if (preg_match('|icon\=\"(.*)\"|', $label, $icons)) {
            $label = preg_replace('|icon\=\"(.*)\"|', '', $label);
            [, $icon] = $icons;
        }
        if ($icon) {
            $html .= '<span class="icon ' . $icon . '"></span>';
        }
        $html .= '<strong class="h4">' . htmlspecialchars($label) . '</strong></div>';
        $html .= '</figcaption>';

        return $html;
    }

    private function printImage(string $imageId, bool $full = true): string {
        $size = @getimagesize(mediaFN($imageId));
        if ($size && $size[0] > 1600) {
            $imgSize = 1600;
        }
        $src = ml($imageId, isset($imgSize) ? ['w' => $imgSize] : null);
        return $full ? $this->printFullImage($src) : $this->printBackgroundImage($src);
    }

    private function printBackgroundImage(string $src): string {
        return '<div class="image w-100 h-100" style="background-image: url(\'' . $src . '\')"></div>';
    }

    private function printFullImage(string $src): string {
        return '<img class="image w-100 h-100" src="' . $src . '"/>';
    }

    private function findPosition(string $match): string {

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

    public static function prepareSelectors(string $attributes): array {
        // match classes
        preg_match_all('/\.([a-zA-z0-9-_]*)/', $attributes, $classes);
        // match ID
        preg_match('/\#([a-zA-z0-9-_]*)/', $attributes, $id);

        return ['classes' => implode(" ", $classes[1]), 'id' => $id[1]];
    }
}
