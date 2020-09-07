<?php

use dokuwiki\Extension\SyntaxPlugin;

/**
 * Class syntax_plugin_fkshelper_sidebar
 * @author Michal Červeňák <miso@fykos.cz>
 */
class syntax_plugin_fkshelper_sidebar extends SyntaxPlugin {

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
        $this->Lexer->addSpecialPattern('~~SIDEBAR\|.*?~~', $mode, 'plugin_fkshelper_sidebar');
    }

    public function handle($match, $state, $pos, \Doku_Handler $dokuHandler): array {
        preg_match('/~~SIDEBAR\|(.*?)~~/', $match, $matches);
        [, $pageId] = $matches;
        $pageId = trim($pageId);
        return [$state, $pageId];
    }

    public function render($mode, Doku_Renderer $renderer, $data): bool {
        if ($mode === 'metadata') {
            [, $pageId] = $data;
            $renderer->meta['sidebar'] = $pageId;
            return true;
        } else {
            return false;
        }
    }
}
