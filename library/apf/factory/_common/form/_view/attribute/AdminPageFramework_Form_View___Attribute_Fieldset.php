<?php 
/**
	Admin Page Framework v3.8.7b01 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2016, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AdminPageFramework_Form_View___Attribute_Fieldset extends AdminPageFramework_Form_View___Attribute_FieldContainer_Base {
    public $sContext = 'fieldset';
    protected function _getAttributes() {
        return array('id' => $this->sContext . '-' . $this->aArguments['tag_id'], 'class' => implode(' ', array('admin-page-framework-' . $this->sContext, $this->_getSelectorForChildFieldset())), 'data-field_id' => $this->aArguments['tag_id'],);
    }
    private function _getSelectorForChildFieldset() {
        if ($this->aArguments['_nested_depth'] == 0) {
            return '';
        }
        if ($this->aArguments['_nested_depth'] == 1) {
            return 'child-fieldset nested-depth-' . $this->aArguments['_nested_depth'];
        }
        return 'child-fieldset multiple-nesting nested-depth-' . $this->aArguments['_nested_depth'];
    }
}
