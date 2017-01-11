<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * If accessed from a console, include the registry class to laod 'AdminPageFramework_Registry_Base'.
 */
if ( php_sapi_name() === 'cli' ) {
    $_sFrameworkFilePath = dirname( dirname( __FILE__ ) ) . '/admin-page-framework.php';
    if ( file_exists( $_sFrameworkFilePath ) ) {
        include_once( $_sFrameworkFilePath );
    }
}

/**
 * Provides header information of the framework for the minifed version.
 * 
 * The minifier script will include this file ( but it does not include WordPress ) to use the reflection class to generate the header comment section.
 * 
 * @since       3.1.3
 * @package     AdminPageFramework
 * @subpackage  Property
 * @internal
 */
final class AdminPageFramework_MinifiedVersionHeader extends AdminPageFramework_Registry_Base {
    
    const NAME          = 'Admin Page Framework - Minified Version';
    const DESCRIPTION   = 'Generated by PHP Class Minifier <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>';
    
}
