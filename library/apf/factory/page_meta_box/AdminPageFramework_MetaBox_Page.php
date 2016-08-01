<?php 
/**
	Admin Page Framework v3.8.1 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2016, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
abstract class AdminPageFramework_MetaBox_Page extends AdminPageFramework_PageMetaBox {
    function __construct($sMetaBoxID, $sTitle, $asPageSlugs = array(), $sContext = 'normal', $sPriority = 'default', $sCapability = 'manage_options', $sTextDomain = 'admin-page-framework') {
        trigger_error(sprintf(__('The class <code>%1$s</code> is deprecated. Use <code>%2$s</code> instead.', 'admin-page-framework'), __CLASS__, 'AdminPageFramework_PageMetaBox'), E_USER_NOTICE);
        parent::__construct($sMetaBoxID, $sTitle, $asPageSlugs, $sContext, $sPriority, $sCapability, $sTextDomain);
    }
}
