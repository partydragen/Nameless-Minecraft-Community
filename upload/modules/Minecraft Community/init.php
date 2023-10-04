<?php 
/*
 *  Made by Partydragen
 *  https://partydragen.com
 *
 *  Minecraft Community module initialisation file
 */
 
// Initialise minecraft community language
$mccommunity_language = new Language(ROOT_PATH . '/modules/Minecraft Community/language', LANGUAGE);

// Load classes
spl_autoload_register(function ($class) {
    $path = join(DIRECTORY_SEPARATOR, [ROOT_PATH, 'modules', 'Minecraft Community', 'classes', $class . '.php']);
    if (file_exists($path)) {
        require_once($path);
    }
});


// Load classes
spl_autoload_register(function ($class) {
    $path = join(DIRECTORY_SEPARATOR, [ROOT_PATH, 'modules', 'Minecraft Community', 'classes', 'Provider', $class . '.php']);
    if (file_exists($path)) {
        require_once($path);
    }
});

// Initialise module
require_once(ROOT_PATH . '/modules/Minecraft Community/module.php');
$module = new Minecraft_Community_Module($language, $mccommunity_language, $pages, $endpoints);