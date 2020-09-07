<?php

use dokuwiki\Extension\SyntaxPlugin;

/**
 * Class syntax_plugin_fkshelper_clearer
 * @author Michal Červeňák <miso@fykos.cz>
 */
class syntax_plugin_fkshelper_clearer extends SyntaxPlugin {

    public function getType(): string {
        return 'formatting';
    }

    public function getPType(): string {
        return 'normal';
    }

    public function getAllowedTypes(): array {
        return [];
    }

    public function getSort(): int {
        return 1000;
    }

    public function connectTo($mode): void {
        $this->Lexer->addSpecialPattern('~~clear~~', $mode, 'plugin_fkshelper_clearer');
    }

    public function handle($match, $state, $pos, Doku_Handler $handler): array {
        return [$state];
    }

    public function render($mode, Doku_Renderer $renderer, $data): bool {
        if ($mode == 'xhtml') {
            [$state] = $data;
            switch ($state) {
                case DOKU_LEXER_SPECIAL :
                    $renderer->doc .= '<div style="clear:both"></div>';
                    break;
            }
            return true;
        } else {
            return false;
        }
    }
}
