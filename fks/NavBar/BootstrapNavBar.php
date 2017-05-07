<?php

namespace fks\NavBar;

class BootstrapNavBar {

    private $data = [];
    private $html = '';
    private $className;
    private $id;

    const USER_TOOLS_CONTAINER = 'div class="tools"';

    public function __construct($id) {
        $this->id = $id;
    }

    public function setClassName($className) {
        $this->className = $className;
    }

    public function addTools($class = '') {

        global $INFO;
        global $lang;
        $data = [];
        $data[] = [
            'id' => '',
            'type' => 'd',
            'level' => 1,
            'open' => 1,
            'title' => '<span class="nav-item fa fa-cogs"></span>'
        ];
        $userName = (($INFO['userinfo']['name'] != null) ? ($lang['loggedinas'] . $INFO['userinfo']['name']) : tpl_getLang('nologin'));
        $data[] = [
            'id' => '',
            'type' => 'f',
            'level' => 2,
            'open' => 1,
            'title' => '<div class="dropdown-item"><span class="fa fa-user"></span>
' . $userName . '</div>'
        ];
        $data = array_merge($data, $this->getUserTools(), $this->getPageTools(), $this->getSiteTools());
        $this->data[] = [
            'class' => 'nav ' . $class,
            'data' => $data,
        ];
    }

    public function mainMenu() {

        $this->html .= '
    <nav class="navbar navbar-toggleable-md ' . $this->className . '" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->';

        $this->html .= '        
    <button 
    class="navbar-toggler" 
    type="button" 
    data-toggle="collapse" 
    data-target="#mainNavbar' . $this->id . '" 
    aria-controls="navbarSupportedContent" 
    aria-expanded="false" 
    aria-label="Toggle navigation">
    
    <span class="navbar-toggler-icon"></span>
  </button>   
         <div class="collapse navbar-collapse" id="mainNavbar' . $this->id . '">
      ';
        foreach ($this->data as $item) {
            $this->renderNavBar($item['data'], $item['class']);
        }
        $this->html .= '</nav>';
    }


    public function render() {
        $this->mainMenu();
        echo $this->html;

    }

    private function parseMenuFile($filename) {
        $filePath = wikiFN($filename);
        $data = [];
        if (file_exists($filePath)) {
// read only lines formatted as wiki lists
            $lines = array_filter(file($filePath), function ($line) {
                return preg_match('/^\s+\*/', $line);
            });

            $numLines = count($lines);
            $oldLevel = 0;
// Array is read back to forth so pages with children can be found easier
// you do not have to go back in the array if a child entry is found
            for ($i = 0; $i < $numLines; $i++) {
                if (!$lines[$i]) {
                    continue;
                }
                list ($prefix, $content) = explode('*', $lines[$i]);
                $level = (int)strlen($prefix) / 2;
                $level = ($level > 2) ? 2 : $level;

                if (!preg_match('/\s*\[\[[^\]]+\]\]/', $content)) {
                    continue;
                }
                $content = str_replace([']', '['], '', trim($content));
                list($id, $title, $icon) = explode('|', $content);
                $item = [
                    'id' => $id,
                    'icon' => $icon,
                    'type' => 'f',
                    'level' => $level,
                    'open' => 1,
                    'title' => $title
                ];
                if ($oldLevel > $level) {
                    $item['type'] = 'd';
                }
                $oldLevel = $level;
                $data[] = $item;
            }
        }
        return $data;
    }

    public function addMenuText($file = 'menu', $class = '') {
        global $conf;
        $pageLang = $conf['lang'];
        $menuFileName = 'system/' . $file . '_' . $pageLang;

        $this->data[] = [
            'class' => 'nav ' . $class,
            'data' => $this->parseMenuFile($menuFileName),
        ];
    }

    public function addLangSelect($class = '') {
        global $conf;
        $data = [];
        if (count($conf['available_lang']) == 0) return;
        $data[] = [
            'id' => '',
            'ns' => '',
            'type' => 'f',
            'level' => 1,
            'open' => 1,
            'title' => '<span class="fa fa-language"></span>'
        ];

        foreach ($conf['available_lang'] as $currentLang) {

            $data[] = [
                'id' => '',
                'ns' => '',
                'type' => 'f',
                'level' => 2,
                'open' => 1,
                'title' => '<a 
                href="' . $currentLang['content']['url'] . '" 
                class="dropdown-item ' . $currentLang['content']['class'] . ' ' . ($currentLang['code'] == $conf['lang'] ? 'active' : '') . '"
                ' . $currentLang['content']['more'] . '
                >' . $currentLang['content']['text'] . ' </a> '
            ];
        }
        $this->data[] = [
            'class' => 'nav ' . $class,
            'data' => $data,
        ];

    }

    private function getUserTools() {
        global $lang;
        $data = [];
        $data[] = [
            'id' => '',
            'ns' => '',
            'type' => 'f',
            'level' => 2,
            'open' => 1,
            'title' => ' <div class="dropdown-header" ><span class="glyphicon glyphicon-user" ></span> ' . $lang['user_tools'] . ' .</div> '
        ];

        $userTools = [
            'view' => 'main',
            'items' => [
                'admin' => tpl_action('admin', true, self::USER_TOOLS_CONTAINER, 1),
                //   'userpage' => tpl_action('userpage',1,'li',1),
                'profile' => tpl_action('profile', true, self::USER_TOOLS_CONTAINER, 1),
                'register' => tpl_action('register', true, self::USER_TOOLS_CONTAINER, 1),
                'login' => tpl_action('login', true, self::USER_TOOLS_CONTAINER, 1)
            ]
        ];
        $evt = new \Doku_Event('TEMPLATE_USERTOOLS_DISPLAY', $userTools);

        if ($evt->advise_before()) {
            foreach ($evt->data['items'] as $k => $html) {
                $data[] = [
                    'id' => '',
                    'ns' => '',
                    'type' => 'f',
                    'level' => 2,
                    'open' => 1,
                    'title' => $html
                ];
            }
        }
        $evt->advise_after();
        return $data;
    }

    private function getSiteTools() {
        global $lang;
        $data = [];
        $data[] = [
            'id' => '',
            'ns' => '',
            'type' => 'f',
            'level' => 2,
            'open' => 1,
            'title' => ' <div class="dropdown-header" ><span class="glyphicon glyphicon-user" ></span> ' . $lang['site_tools'] . ' .</div > '
        ];

        ob_start();
        tpl_searchform();
        $search_form = ob_get_contents();
        ob_end_clean();

        $siteTools = [
            'view' => 'main',
            'items' => [
                'recent' => tpl_action('recent', 1, self::USER_TOOLS_CONTAINER, 1),
                'media' => tpl_action('media', 1, self::USER_TOOLS_CONTAINER, 1),
                'index' => tpl_action('index', 1, self::USER_TOOLS_CONTAINER, 1),
                'search' => $search_form
            ]
        ];

        $event = new \Doku_Event('TEMPLATE_USERTOOLS_DISPLAY', $siteTools);

        if ($event->advise_before()) {
            foreach ($event->data['items'] as $k => $html) {
                $data[] = [
                    'id' => '',
                    'type' => 'f',
                    'level' => 2,
                    'open' => 1,
                    'title' => $html
                ];
            }
        }

        $event->advise_after();
        return $data;
    }

    private function getPageTools() {
        global $lang;
        $data = [];
        $data[] = [
            'id' => '',
            'type' => 'f',
            'level' => 2,
            'open' => 1,
            'title' => ' <div class="dropdown-header" ><span class="fa fa-user-o" ></span > ' . $lang['page_tools'] . ' </div > '
        ];
        $pageTools = [
            'view' => 'main',
            'items' => [
                'edit' => tpl_action('edit', 1, self::USER_TOOLS_CONTAINER, 1),
                'revert' => tpl_action('revert', 1, self::USER_TOOLS_CONTAINER, 1),
                'revisions' => tpl_action('revisions', 1, self::USER_TOOLS_CONTAINER, 1),
                'backlink' => tpl_action('backlink', 1, self::USER_TOOLS_CONTAINER, 1),
                'subscribe' => tpl_action('subscribe', 1, self::USER_TOOLS_CONTAINER, 1)
            ]
        ];
        $event = new \Doku_Event('TEMPLATE_PAGETOOLS_DISPLAY', $pageTools);

        if ($event->advise_before()) {
            foreach ($event->data['items'] as $k => $html) {
                $data[] = [
                    'id' => '',
                    'type' => 'f',
                    'level' => 2,
                    'open' => 1,
                    'title' => $html
                ];
            }
        }

        $event->advise_after();
        return $data;
    }

    public function renderNavBar($data, $class = '') {
        $inLI = false;
        $inUL = false;

        $this->html .= ' <ul class="nav navbar-nav ' . $class . '" > ';
        foreach ($data as $k => $v) {
            $icon = $v['icon'] ? (' <span class="' . $v['icon'] . '" ></span > ') : '';
            $link = preg_match('#https?://#', $v['id']) ? htmlspecialchars($v['id']) : wl(cleanID($v['id']));
            $title = $icon . $v['title'];
            if ($v['level'] == 1) {
                if ($inUL) {
                    $inUL = false;
                    $this->html .= '</div>' . "\n";
                }
                if ($inLI) {
                    $inLI = false;
                    $this->html .= '</li>' . "\n";
                }
                /* is next level 2? */
                if ($data[$k + 1]['level'] == 2) {
                    $inLI = true;
                    $this->html .= '<li class="dropdown nav-item">
          <a href="' . $link . '" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >' . $title . '<span class="caret"></span></a>';
                } else {
                    $this->html .= '<a class="nav-item nav-link" href="' . $link . '">' . $title . '</a>' . "\n";
                }
            } elseif ($v['level'] == 2) {
                if (!$inUL) {
                    $inUL = true;
                    $this->html .= '<div class="dropdown-menu" role="menu">' . "\n";
                }

                if (!empty($v['id'])) {
                    $this->html .= '<a class="dropdown-item" href="' . $link . '">' . $title . '</a>' . "\n";
                } else {
                    $this->html .= $v['title'] . "\n";
                }
            }
        }
        if ($inUL) {
            $this->html .= '</div>';
        }
        if ($inLI) {
            $this->html .= '</li>';
        }
        $this->html .= '</ul>';
        //var_dump($this->html);
    }

}