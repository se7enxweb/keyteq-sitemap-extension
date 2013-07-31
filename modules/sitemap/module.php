<?php
/**
 * @author Kristian Blom
 * @since 2011-02-05
 */
ezote\Autoloader::register();
if (isset($Params) && isset($Params['FunctionName'])) {
    $router = new \ezote\lib\Router;
    $Result = $router->legacyHandle('sitemap', 'sitemap', $Params['FunctionName'], $Params['Parameters'])->run();
}
else
    list($Module, $FunctionList, $ViewList) = sitemap\modules\sitemap\Sitemap::getDefinition();
