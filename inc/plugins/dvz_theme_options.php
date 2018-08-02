<?php
/**
 * Copyright (c) 2018, Tomasz 'Devilshakerz' Mlynski [devilshakerz.com]
 *
 * Permission to use, copy, modify, and/or distribute this software for any purpose with or without fee is hereby
 * granted, provided that the above copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH REGARD TO THIS SOFTWARE INCLUDING ALL
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
 * INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN
 * AN ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
 * PERFORMANCE OF THIS SOFTWARE.
 */

// core files
require MYBB_ROOT . 'inc/plugins/dvz_theme_options/common.php';
require MYBB_ROOT . 'inc/plugins/dvz_theme_options/core.php';

// hook files
require MYBB_ROOT . 'inc/plugins/dvz_theme_options/hooks_frontend.php';

// hooks
\dvzThemeOptions\addHooksNamespace('dvzThemeOptions\Hooks');

// init
define('dvzThemeOptions\DEVELOPMENT_MODE', 1);

// MyBB plugin system
function dvz_theme_options_info()
{
    return [
        'name'          => 'DVZ Theme Options',
        'description'   => 'Allows users to override theme properties using cookies. Toggle activate to reload options cache.',
        'website'       => 'https://devilshakerz.com/',
        'author'        => 'Tomasz \'Devilshakerz\' Mlynski',
        'authorsite'    => 'https://devilshakerz.com/',
        'version'       => 'dev',
        'codename'      => 'dvz_theme_options',
        'compatibility' => '18*',
    ];
}

function dvz_theme_options_install()
{
    global $cache;

    // datacache
    $cache->update('dvz_theme_options', [
        'version' => dvz_theme_options_info()['version'],
        'theme_options' => [],
    ]);

}

function dvz_theme_options_uninstall()
{
    global $cache;

    // datacache
    $cache->delete('dvz_theme_options');
}

function dvz_theme_options_activate()
{
    \dvzThemeOptions\reloadOptionsCache();
}
