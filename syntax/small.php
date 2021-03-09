<?php

use dokuwiki\Extension\SyntaxPlugin;

/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Michal Červeňák <miso@fykos.cz>
 */
class syntax_plugin_fkshelper_small extends SyntaxPlugin {

    public function getType(): string {
        return 'formatting';
    }

    public function getPType(): string {
        return 'normal';
    }

    public function getAllowedTypes(): array {
        return ['formatting'];
    }

    public function getSort(): int {
        return 10;
    }

    public function connectTo($mode): void {
        $this->Lexer->addEntryPattern('<small>(?=.*?</small>)', $mode, 'plugin_fkshelper_small');
    }

    public function postConnect(): void {
        $this->Lexer->addExitPattern('</small>', 'plugin_fkshelper_small');
    }

    public function handle($match, $state, $pos, Doku_Handler $handler): array {
        if ($state == DOKU_LEXER_UNMATCHED) {
            return [$state, $match];
        }
        return [$state];
    }

    public function render($mode, Doku_Renderer $renderer, $data): bool {
        if ($mode == 'xhtml') {
            [$state, $payload] = $data;
            switch ($state) {
                case DOKU_LEXER_ENTER :
                    $renderer->doc .= '<small>';
                    break;
                case DOKU_LEXER_MATCHED :
                case DOKU_LEXER_SPECIAL :
                    break;
                case DOKU_LEXER_UNMATCHED :
                    $renderer->doc .= $renderer->_xmlEntities($payload);
                    break;
                case DOKU_LEXER_EXIT :
                    $renderer->doc .= '</small>';
                    break;
            }
            return true;
        }
        return false;
    }
}
