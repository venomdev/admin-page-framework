<?php
/**
 Admin Page Framework v3.7.2b03 by Michael Uno
 Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
 <http://en.michaeluno.jp/admin-page-framework>
 Copyright (c) 2013-2015, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT>
 */
class AdminPageFramework_Format_SubMenuLink extends AdminPageFramework_Format_SubMenuPage {
    static public $aStructure = array('type' => 'link', 'title' => null, 'href' => null, 'capability' => null, 'order' => null, 'show_page_heading_tab' => true, 'show_in_menu' => true,);
    public $aSubMenuLink = array();
    public $oFactory;
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aSubMenuLink, $this->oFactory,);
        $this->aSubMenuLink = $_aParameters[0];
        $this->oFactory = $_aParameters[1];
    }
    public function get() {
        return $this->_getFormattedSubMenuLinkArray($this->aSubMenuLink);
    }
    protected function _getFormattedSubMenuLinkArray(array $aSubMenuLink) {
        if (!filter_var($aSubMenuLink['href'], FILTER_VALIDATE_URL)) {
            return array();
        }
        return array('capability' => $this->getElement($aSubMenuLink, 'capability', $this->oFactory->oProp->sCapability), 'order' => isset($aSubMenuLink['order']) && is_numeric($aSubMenuLink['order']) ? $aSubMenuLink['order'] : count($this->oFactory->oProp->aPages) + 10,) + $aSubMenuLink + self::$aStructure;
    }
}