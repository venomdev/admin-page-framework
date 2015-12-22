<?php
/**
 Admin Page Framework v3.7.6b05 by Michael Uno
 Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
 <http://en.michaeluno.jp/admin-page-framework>
 Copyright (c) 2013-2015, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT>
 */
abstract class AdminPageFramework_UserMeta_Model extends AdminPageFramework_UserMeta_Router {
    public function _replyToGetSavedFormData() {
        $_iUserID = isset($GLOBALS['profileuser']->ID) ? $GLOBALS['profileuser']->ID : 0;
        $_oMetaData = new AdminPageFramework_UserMeta_Model___UserMeta($_iUserID, $this->oForm->aFieldsets);
        $this->oProp->aOptions = $_oMetaData->get();
        return parent::_replyToGetSavedFormData();
    }
    public function _replyToSaveFieldValues($iUserID) {
        if (!current_user_can('edit_user', $iUserID)) {
            return;
        }
        $_aInputs = $this->oForm->getSubmittedData($_POST, true, false);
        $_aInputsRaw = $_aInputs;
        $_aSavedMeta = $this->oUtil->getSavedUserMetaArray($iUserID, array_keys($_aInputs));
        $_aInputs = $this->oUtil->addAndApplyFilters($this, "validation_{$this->oProp->sClassName}", call_user_func_array(array($this, 'validate'), array($_aInputs, $_aSavedMeta, $this)), $_aSavedMeta, $this);
        if ($this->hasFieldError()) {
            $this->setLastInputs($_aInputsRaw);
        }
        $this->oForm->updateMetaDataByType($iUserID, $_aInputs, $this->oForm->dropRepeatableElements($_aSavedMeta), $this->oForm->sStructureType);
    }
}