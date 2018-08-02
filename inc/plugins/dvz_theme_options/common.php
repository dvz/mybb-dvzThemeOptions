<?php

namespace dvzThemeOptions;

function addHooks(array $hooks, string $namespace = null)
{
    global $plugins;

    if ($namespace) {
        $prefix = $namespace . '\\';
    } else {
        $prefix = null;
    }

    foreach ($hooks as $hook) {
        $plugins->add_hook($hook, $prefix . $hook);
    }
}

function addHooksNamespace(string $namespace)
{
    global $plugins;

    $namespaceLowercase = strtolower($namespace);
    $definedUserFunctions = get_defined_functions()['user'];

    foreach ($definedUserFunctions as $callable) {
        $namespaceWithPrefixLength = strlen($namespaceLowercase) + 1;
        if (substr($callable, 0, $namespaceWithPrefixLength) == $namespaceLowercase . '\\') {
            $hookName = substr_replace($callable, null, 0, $namespaceWithPrefixLength);

            $plugins->add_hook($hookName, $namespace . '\\' . $hookName);
        }
    }
}

function getCacheValue(string $key)
{
    global $cache;

    return $cache->read('dvz_theme_options')[$key] ?? null;
}

function updateCache(array $values, bool $overwrite = false)
{
    global $cache;

    if ($overwrite) {
        $cacheContent = $values;
    } else {
        $cacheContent = $cache->read('dvz_theme_options');
        $cacheContent = array_merge($cacheContent, $values);
    }

    $cache->update('dvz_theme_options', $cacheContent);
}
