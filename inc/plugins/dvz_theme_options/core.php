<?php

namespace dvzThemeOptions;

function getOptionsForTheme(string $themeName, bool $bypassCache = false): array
{
    $options = [];

    $themeNameNormalized = \dvzThemeOptions\normalizeThemeName($themeName);

    if ($bypassCache || \dvzThemeOptions\DEVELOPMENT_MODE) {
        $optionsFilePath = MYBB_ROOT . 'inc/plugins/dvz_theme_options/themes/' . $themeNameNormalized . '.json';

        if (file_exists($optionsFilePath)) {
            $content = file_get_contents($optionsFilePath);

            if ($content) {
                $jsonData = json_decode($content, true);

                if ($jsonData !== null) {
                    $options = $jsonData;
                }
            }
        }
    } else {
        $cacheContent = \dvzThemeOptions\getCacheValue('theme_options')[$themeNameNormalized] ?? [];

        if ($cacheContent) {
            $options = $cacheContent;
        }
    }

    return $options;
}

function reloadOptionsCache()
{
    $themeOptions = [];

    $directory = new \DirectoryIterator(MYBB_ROOT . 'inc/plugins/dvz_theme_options/themes');

    foreach ($directory as $file) {
        if (!$file->isDot() && !$file->isDir()) {
            $name = basename($file->getPathname(), '.json');

            $content = file_get_contents($file->getPathname());

            if ($content) {
                $jsonData = json_decode($content, true);

                if ($jsonData !== null) {
                    $themeOptions[$name] = $jsonData;
                }
            }
        }
    }

    // datacache
    \dvzThemeOptions\updateCache([
        'theme_options' => $themeOptions,
    ]);
}

function getCaseProperties(array $options, array $userOptions, array &$triggeredCases = []): array
{
    $properties = [
        'stylesheets' => [],
        'editortheme' => null,
    ];

    foreach ($options as $option => $cases) {
        if (isset($userOptions[$option]) && isset($cases[ $userOptions[$option] ])) {
            $case = $userOptions[$option];
        } elseif (!empty($cases)) {
            $case = array_keys($cases)[0];
        } else {
            continue;
        }

        $triggeredCases[$option] = $case;

        if (isset($cases[$case]['stylesheets']) && is_array($cases[$case]['stylesheets'])) {
            $properties['stylesheets'] = array_unique(
                array_merge($properties['stylesheets'], $cases[$case]['stylesheets'])
            );
        }

        if (isset($cases[$case]['editortheme'])) {
            $properties['editortheme'] = $cases[$case]['editortheme'];
        }
    }

    return $properties;
}

function renderStylesheetTags(array $stylesheets, array $theme): string
{
    global $mybb;

    $output = '';

    foreach ($stylesheets as $file) {
        $stylesheet_url = 'cache/themes/theme' . $theme['tid'] . '/' . $file;
        $stylesheet_url = $mybb->get_asset_url($stylesheet_url);

        if ($mybb->settings['minifycss']) {
            $stylesheet_url = str_replace('.css', '.min.css', $stylesheet_url);
        }

        $output .= '<link type="text/css" rel="stylesheet" href="' . $stylesheet_url . '" data-theme-options="true" />' . PHP_EOL;
    }

    return $output;
}

function normalizeThemeName(string $name): string
{
    return strtolower($name);
}