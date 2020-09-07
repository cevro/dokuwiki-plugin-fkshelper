<?php

use dokuwiki\Extension\SyntaxPlugin;

/**
 * Class syntax_plugin_fksvlna
 * @author Michal Červeňák <miso@fykos.cz>
 */
class syntax_plugin_fkshelper_vlna extends SyntaxPlugin {

    public function getType(): string {
        return 'substition';
    }

    public function getPType(): string {
        return 'normal';
    }

    public function getAllowedTypes(): array {
        return [];
    }

    public function getSort(): int {
        return 99999;
    }

    public function connectTo($mode): void {
        $this->Lexer->addSpecialPattern('~', $mode, 'plugin_fkshelper_vlna');
    }

    public function handle($match, $state, $pos, Doku_Handler $handler) {
        return [$state];
    }

    public function render($mode, Doku_Renderer $renderer, $match) {
        if ($mode == 'xhtml') {
            [$state] = $match;
            if ($state == DOKU_LEXER_SPECIAL) {
                $renderer->doc .= '&nbsp;';
                return true;
            }
        }
        return false;
    }
}
