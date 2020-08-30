<?php

use dokuwiki\Extension\SyntaxPlugin;

/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Michal Červeňák <miso@fykos.cz>
 */
class syntax_plugin_fkshelper_fl extends SyntaxPlugin {

    public function getType(): string {
        return 'substition';
    }

    public function getPType(): string {
        return 'block';
    }

    public function getSort(): int {
        return 226;
    }

    public function connectTo($mode): void {
        $this->Lexer->addSpecialPattern('{{fl.*?\>.+?\|.+?}}', $mode, 'plugin_fkshelper_fl');
        $this->Lexer->addSpecialPattern('{{button.*?\>.+?\|.+?}}', $mode, 'plugin_fkshelper_fl');
        $this->Lexer->addSpecialPattern('{{media-button.*?\>.+?\|.+?}}', $mode, 'plugin_fkshelper_fl');
    }

    public function handle($match, $state, $pos, Doku_Handler $handler): array {
        preg_match('/{{\s*(media-)?(button|fl)(.*)>(.*)\|(.*)}}/', $match, $matchesButton);
        [, $is_media, $button_type, $attributes, $link, $text] = $matchesButton;
        $type = 'link';

        /* Test if media */
        if ($is_media) {
            /* If the query contains question mark test if the page exists */
            if (substr($link, -1) === '?' && (!media_ispublic($link) || !file_exists(mediaFN($link)))) {
                return [$state];
            }

            $type = 'ml';
        } else {
            /* If the query contains question mark test if the page exists */
            if (substr($link, -1) === '?' && !page_exists($link)) {
                return [$state];
            }
        }

        $attributes = syntax_plugin_fkshelper_images::prepareSelectors($attributes);
        return [$state, $link, $text, $attributes, $type];
    }

    public function render($mode, Doku_Renderer $renderer, $data): bool {
        global $ID;
        if (!$data) {
            return false;
        }
        if ($mode == 'xhtml') {
            [$state, $link, $text, $attributes, $type] = $data;
            switch ($state) {
                case DOKU_LEXER_SPECIAL:
                    $attributesString = ' class="fast_link ' . hsc($attributes['classes']) . '"';
                    $attributesString .= ' id="' . hsc($attributes['id']) . '"';
                    if ($type === 'ml') {
                        $renderer->doc .= '<a href="' . ml($link) . '"' . $attributesString . '>';
                    } elseif (preg_match('|^http[s]?://|', $link)) {
                        $renderer->doc .= '<a href="' . htmlspecialchars($link) . '"' . $attributesString . '>';
                    } else {
                        /** FUCK dokuwiki  */
                        if (preg_match('/([^#]*)(#.*)/', $link, $ms)) {
                            [, $id, $hash] = $ms;
                            $id = $id ?: $ID;
                            $renderer->doc .= '<a href="' . wl(cleanID($id)) . $hash . '"' . $attributesString . '>';
                        } else {
                            $renderer->doc .= '<a href="' . wl(cleanID($link)) . '"' . $attributesString . '>';
                        }
                    }
                    $renderer->doc .= htmlspecialchars(trim($text));
                    $renderer->doc .= '</a>';
                    return true;
            }
        }
        return false;
    }
}
