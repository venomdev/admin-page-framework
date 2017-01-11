<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * {@inheritdoc}
 * 
 * {@inheritdoc}
 * 
 * This is for pages that have page-meta-box fields added by the framework.
 * 
 * @since       3.0.0
 * @since       3.3.0       Changed the name from AdminPageFramework_HeadTag_MetaBox_Page.
 * @use         AdminPageFramework_Utility
 * @package     AdminPageFramework
 * @extends     AdminPageFramework_Resource_admin_page
 * @subpackage  Factory/PageMetaBox/Resource
 * @internal
 * @remark      Note that this class extends the resource class of the page factory while the property class extends the meta box factory.
 */
class AdminPageFramework_Resource_page_meta_box extends AdminPageFramework_Resource_admin_page {}
