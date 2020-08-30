<?php

use dokuwiki\Extension\SyntaxPlugin;

class syntax_plugin_fkshelper_person extends SyntaxPlugin {

    public function getType(): string {
        return 'substition';
    }

    public function getPType(): string {
        return 'normal';
    }

    public function getAllowedTypes(): array {
        return ['substition'];
    }

    public function getSort(): int {
        return 226;
    }

    public function connectTo($mode): void {
        $this->Lexer->addEntryPattern('<person\b.*?>(?=.*?</person>)', $mode, 'plugin_fkshelper_person');
    }

    public function postConnect(): void {
        $this->Lexer->addExitPattern('</person>', 'plugin_fkshelper_person');
    }

    public function handle($match, $state, $pos, Doku_Handler $handler): array {
        switch ($state) {
            case DOKU_LEXER_ENTER:
                preg_match('|<person\s+id="(.+)">|', $match, $matches);
                [, $id] = $matches;
                return [$state, ['id' => $id]];
                break;
            case DOKU_LEXER_UNMATCHED:
                return [$state, $match];
            default:
                return [$state];
        }
    }

    public function render($mode, Doku_Renderer $renderer, $data): bool {

        [$state, $payload] = $data;
        if ($mode == 'xhtml') {
            switch ($state) {
                case DOKU_LEXER_ENTER:
                    [, $personInfo] = $data;
                    $link = wl($this->getConf('person-page-link'), null, true) . '#' . $personInfo['id'];
                    $imgSrc = ml(str_replace('@id@', $personInfo['id'], $this->getConf('person-image-src')),
                        ['w' => 140]);
                    $renderer->doc .= '<a href="' . $link . '" ><span class="person" data-src="' . $imgSrc . '">';
                    break;
                case DOKU_LEXER_UNMATCHED:
                    $renderer->doc .= $renderer->_xmlEntities($payload);
                    break;
                case DOKU_LEXER_EXIT:
                    $renderer->doc .= '</span></a>';
                    break;
            }
            return true;
        } elseif ($mode == 'metadata') {
            return true;
        }
        return false;
    }
}
