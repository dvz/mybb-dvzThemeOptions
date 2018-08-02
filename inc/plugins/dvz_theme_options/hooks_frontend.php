<?php

namespace dvzThemeOptions\Hooks;

function global_intermediate()
{
    global $mybb, $stylesheets, $theme, $themeOptionCases, $themeOptionCasesJson;

    $themeOptionCases = [];

    $themeNameNormalized = \dvzThemeOptions\normalizeThemeName($theme['name']);

    if (isset($mybb->cookies['theme_options'][$themeNameNormalized]) && is_array($mybb->cookies['theme_options'][$themeNameNormalized])) {
        $userOptions = $mybb->cookies['theme_options'][$themeNameNormalized];
    } else {
        $userOptions = [];
    }

    $options = \dvzThemeOptions\getOptionsForTheme($theme['name']);
    $properties = \dvzThemeOptions\getCaseProperties($options, $userOptions, $themeOptionCases);

    $themeOptionCasesJson = json_encode($themeOptionCases);

    // appending to $stylesheets
    $stylesheets .= \dvzThemeOptions\renderStylesheetTags($properties['stylesheets'], $theme);

    // overwriting $theme['editortheme']
    if ($properties['editortheme'] !== null) {
        $theme['editortheme'] = $properties['editortheme'];
    }
}

function xmlhttp()
{
    global $mybb, $theme, $charset;

    if ($mybb->get_input('action') == 'theme_options_get_properties') {
        header('Content-type: text/plain; charset=' . $charset);
        header('Cache-Control: no-store');

        $themeNameNormalized = strtolower($theme['name']);

        $userOptions = &$mybb->cookies['theme_options'][$themeNameNormalized];

        $stylesheets = null;
        $editortheme = null;
        $themeOptionCases = [];

        $options = \dvzThemeOptions\getOptionsForTheme($theme['name']);
        $properties = \dvzThemeOptions\getCaseProperties($options, $userOptions, $themeOptionCases);

        // appending to $stylesheets
        $stylesheetsRendered = \dvzThemeOptions\renderStylesheetTags($properties['stylesheets'], $theme);

        // overwriting $theme['editortheme']
        if ($properties['editortheme'] !== null) {
            $editortheme = $properties['editortheme'];
        }

        echo json_encode([
            'stylesheets' => $properties['stylesheets'],
            'stylesheetsRendered' => $stylesheetsRendered,
            'editortheme' => $editortheme,
            'optionCases' => $themeOptionCases,
        ]);

        exit;
    }
}
