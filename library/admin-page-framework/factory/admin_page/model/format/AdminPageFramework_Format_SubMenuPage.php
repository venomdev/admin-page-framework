<?php
class AdminPageFramework_Format_SubMenuPage extends AdminPageFramework_Format_Base {
    static public $aStructure = array('page_slug' => null, 'type' => 'page', 'title' => null, 'page_title' => null, 'menu_title' => null, 'screen_icon' => null, 'capability' => null, 'order' => null, 'show_page_heading_tab' => true, 'show_in_menu' => true, 'href_icon_32x32' => null, 'screen_icon_id' => null, 'show_page_title' => null, 'show_page_heading_tabs' => null, 'show_in_page_tabs' => null, 'in_page_tab_tag' => null, 'page_heading_tab_tag' => null, 'disabled' => null, 'attributes' => null,);
    static public $aScreenIconIDs = array('edit', 'post', 'index', 'media', 'upload', 'link-manager', 'link', 'link-category', 'edit-pages', 'page', 'edit-comments', 'themes', 'plugins', 'users', 'profile', 'user-edit', 'tools', 'admin', 'options-general', 'ms-admin', 'generic',);
    public $aSubMenuPage = array();
    public $oFactory;
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aSubMenuPage, $this->oFactory,);
        $this->aSubMenuPage = $_aParameters[0];
        $this->oFactory = $_aParameters[1];
    }
    public function get() {
        return $this->_getFormattedSubMenuPageArray($this->aSubMenuPage);
    }
    protected function _getFormattedSubMenuPageArray(array $aSubMenuPage) {
        $aSubMenuPage = $aSubMenuPage + array('show_page_title' => $this->oFactory->oProp->bShowPageTitle, 'show_page_heading_tabs' => $this->oFactory->oProp->bShowPageHeadingTabs, 'show_in_page_tabs' => $this->oFactory->oProp->bShowInPageTabs, 'in_page_tab_tag' => $this->oFactory->oProp->sInPageTabTag, 'page_heading_tab_tag' => $this->oFactory->oProp->sPageHeadingTabTag, 'capability' => $this->oFactory->oProp->sCapability,) + self::$aStructure;
        $aSubMenuPage['page_slug'] = $this->sanitizeSlug($aSubMenuPage['page_slug']);
        $aSubMenuPage['screen_icon_id'] = trim($aSubMenuPage['screen_icon_id']);
        return array('href_icon_32x32' => $this->getResolvedSRC($aSubMenuPage['screen_icon'], true), 'screen_icon_id' => $this->getAOrB(in_array($aSubMenuPage['screen_icon'], self::$aScreenIconIDs), $aSubMenuPage['screen_icon'], 'generic'), 'capability' => $this->getElement($aSubMenuPage, 'capability', $this->oFactory->oProp->sCapability), 'order' => $this->getAOrB(is_numeric($aSubMenuPage['order']), $aSubMenuPage['order'], count($this->oFactory->oProp->aPages) + 10),) + $aSubMenuPage;
    }
}