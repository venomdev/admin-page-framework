<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed MIT
 *
 */

/**
 * Deals with redirecting function calls and instantiating classes.
 *
 * @abstract
 * @since           3.0.0
 * @since           3.3.1       Changed from `AdminPageFramework_Base`.
 * @package         AdminPageFramework/Factory/AdminPage
 * @internal
 * @method          _renderSectionDescription( $sMethodName )           defined in AdminPageFramework_Setting
 * @method          _renderSettingField( $_mFirstArg, $_sPageSlug )     defined in AdminPageFramework_Setting
 * @method          load()
 */
abstract class AdminPageFramework_Router extends AdminPageFramework_Factory {

    /**'
     * Sets up hooks and properties.
     *
     * @since       3.3.0
     */
    public function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {

        $_sPropertyClassName = isset( $this->aSubClassNames[ 'oProp' ] )
            ? $this->aSubClassNames[ 'oProp' ]
            : 'AdminPageFramework_Property_' . $this->_sStructureType;

        $this->oProp = new $_sPropertyClassName(
            $this,
            $sCallerPath,
            get_class( $this ),
            $sOptionKey,
            $sCapability,
            $sTextDomain
        );

        parent::__construct( $this->oProp );

// @deprecated      3.8.14
//        if ( $this->oProp->bIsAdminAjax ) {
//            return;
//        }
        if ( ! $this->oProp->bIsAdmin ) {
            return;
        }

        add_action( 'wp_loaded', array( $this, '_replyToDetermineToLoad' ) );
        add_action( 'set_up_' . $this->oProp->sClassName, array( $this, '_replyToLoadComponentsForAjax' ), 100 );

    }

    /**
     * Loads page components for ajax calls.
     *
     * @since           3.8.14
     * @remark          It is assumed that the `setUp()` method is already called.
     * @callback        add_action      wp_loaded
     */
    public function _replyToLoadComponentsForAjax() {
        if ( ! $this->oProp->bIsAdminAjax ) {
            return;
        }
        new AdminPageFramework_Model_Menu__RegisterMenu( $this, 'pseudo_admin_menu' );
        do_action( 'pseudo_admin_menu', '' );
        do_action( 'pseudo_current_screen' );

    }

    /**
     * Instantiates a link object based on the type.
     *
     * @since       3.7.10
     * @internal
     * @return      null|object
     */
    protected function _getLinkObject() {
        $_sClassName = $this->aSubClassNames[ 'oLink' ];
        return new $_sClassName( $this->oProp, $this->oMsg );
    }

    /**
     * Instantiates a link object based on the type.
     *
     * @since       3.7.10
     * @internal
     * @return      null|object
     */
    protected function _getPageLoadObject() {
        $_sClassName = $this->aSubClassNames[ 'oPageLoadInfo' ];
        return new $_sClassName( $this->oProp, $this->oMsg );
    }

    /**
     * Handles undefined function calls.
     *
     * This method redirects callback-function calls with the pre-defined prefixes for hooks to the appropriate methods.
     *
     * @access      public
     * @remark      the users do not need to call or extend this method unless they know what they are doing.
     * @param       string      the called method name.
     * @param       array       the argument array. The first element holds the parameters passed to the called method.
     * @return      mixed       depends on the called method. If the method name matches one of the hook prefixes, the redirected methods return value will be returned. Otherwise, none.
     * @since       2.0.0
     * @since       3.3.1       Moved from `AdminPageFramework_Base`.
     * @internal
     */
    public function __call( $sMethodName, $aArgs=null ) {

        $_sPageSlug             = $this->oProp->getCurrentPageSlug();
        $_sTabSlug              = $this->oProp->getCurrentTabSlug( $_sPageSlug );
        $_mFirstArg             = $this->oUtil->getElement( $aArgs, 0 );
        $_aKnownMethodPrefixes  = array(
            'section_pre_',
            'field_pre_',
            'load_pre_',
        );

        switch( $this->_getCallbackName( $sMethodName, $_sPageSlug, $_aKnownMethodPrefixes ) ) {

            // add_settings_section() callback
            case 'section_pre_':
                return $this->_renderSectionDescription( $sMethodName );    // defined in AdminPageFramework_Setting

            // add_settings_field() callback
            case 'field_pre_':
                return $this->_renderSettingField( $_mFirstArg, $_sPageSlug );  // defined in AdminPageFramework_Setting

            // load-{page} callback
            case 'load_pre_':
                return $this->_doPageLoadCall( $sMethodName, $_sPageSlug, $_sTabSlug, $_mFirstArg );

            default:
                return parent::__call( $sMethodName, $aArgs );
        }

    }
        /**
         * Attempts to find the factory class callback method for the given method name.
         *
         * @since       3.5.3
         * @return      string      The found callback method name or the prefix of a known callback method name. An empty string if not found.
         * @internal
         */
        private function _getCallbackName( $sMethodName, $sPageSlug, array $aKnownMethodPrefixes=array() ) {

            foreach( $aKnownMethodPrefixes as $_sMethodPrefix ) {
                if ( $this->oUtil->hasPrefix( $_sMethodPrefix, $sMethodName ) ) {
                    return $_sMethodPrefix;
                }
            }
            return '';

        }

        /**
         * Redirects the callback of the load-{page} action hook to the framework's callback.
         *
         * @since       2.1.0
         * @since       3.3.1       Moved from `AdminPageFramework_Base`.
         * @since       3.5.3       Added the $sMethodName parameter.
         *
         * @access      protected
         * @internal
         * @remark      This method will be triggered before the header gets sent.
         * @return      void
         * @internal
         */
        protected function _doPageLoadCall( $sMethodName, $sPageSlug, $sTabSlug, $oScreen ) {

            if ( ! $this->_isPageLoadCall( $sMethodName, $sPageSlug, $oScreen ) ) {
                return;
            }

            // [3.4.6+] Set the page and tab slugs to the default form section so that added form fields without a section will appear in different pages and tabs.
            $this->_setPageAndTabSlugsForForm( $sPageSlug, $sTabSlug );

            $this->_setShowDebugInfoProperty( $sPageSlug ); // 3.8.8+

            // Do actions in this order, class ->  page -> in-page tab. This order is important as some methods rely on it.
            $this->_load(
                array(
                    "load_{$this->oProp->sClassName}",
                    "load_{$sPageSlug}",
                )
            );

            // * Note that the in-page tabs handling method `_replyToFinalizeInPageTabs()` is called in the above action hook.

            // Re-retrieve the current tab slug as in-page tabs may be added during the above `load_{...}`  hooks.
            // Note that if the tab is the first item and the user arrives the page by clicking on the sidebar menu,
            // the tab slug will be empty unless an in-page tab is added.
            $sTabSlug = $this->oProp->getCurrentTabSlug( $sPageSlug );
            if ( strlen( $sTabSlug ) ) {
                $this->_setShowDebugInfoProperty( $sPageSlug, $sTabSlug );  // 3.8.8+
                $this->oUtil->addAndDoActions(
                    $this, // the caller object
                    array( "load_{$sPageSlug}_" . $sTabSlug ),
                    $this // the admin page object - this lets third-party scripts use the framework methods.
                );
            }

            $this->oUtil->addAndDoActions(
                $this, // the caller object
                array(
                    "load_after_{$this->oProp->sClassName}",
                    "load_after_{$sPageSlug}", // 3.6.3+
                ),
                $this // the admin page object - this lets third-party scripts use the framework methods.
            );

        }
            /**
             * Updates the `bShowDebugInfo` property based on the current page and in-page-tab arguments.
             *
             * If the `$sTabSlug` parameter is not set, it is considered for the current page. Otherwise, it is for the current tab.
             *
             * @remark      This must be called before calling the `load()` method as which page to load is already determined at this point.
             * And if the user wants to modify the property value manually, they can do so in the `load()` method.
             * @since       3.8.8
             * @return      void
             */
            private function _setShowDebugInfoProperty( $sPageSlug, $sTabSlug='' ) {

                // For the page,
                if ( ! strlen( $sTabSlug ) ) {
                    $this->oProp->bShowDebugInfo = $this->oUtil->getElement(
                        $this->oProp->aPages,
                        array( $sPageSlug, 'show_debug_info' ),
                        $this->oProp->bShowDebugInfo
                    );
                    return;
                }
                // For the in-page tab.
                $this->oProp->bShowDebugInfo = $this->oUtil->getElement(
                    $this->oProp->aInPageTabs,
                    array( $sPageSlug, $sTabSlug, 'show_debug_info' ),
                    $this->oProp->bShowDebugInfo
                );

            }

            /**
             * Sets the page and tab slugs to the default form section
             * so that added form fields without a section will appear in different pages and tabs.
             *
             * @internal
             * @since       3.8.8
             * @todo        The `oForm` object will get instantiated even the user does not use a form.
             * So look for a way to avoid calling `$oForm` unless the user uses a form.
             */
            private function _setPageAndTabSlugsForForm( $sPageSlug, $sTabSlug ) {
                $this->oForm->aSections[ '_default' ][ 'page_slug' ]  = $sPageSlug ? $sPageSlug : null;
                $this->oForm->aSections[ '_default' ][ 'tab_slug' ]   = $sTabSlug ? $sTabSlug : null;
            }

            /**
             * Determines whether the function call is of a page load.
             * @since       3.5.3
             * @internal
             * @return      boolean         True if it is a page load call; othwrwise, false.
             * @param       string          $sMethodName        The undefined method name that is passed to the __call() overload method.
             * @param       string          $sPageSlug          The currently loading page slug.
             * @param       object|string   $osScreenORPageHook The screen ID that the WordPress screen object gives.
             */
            private function _isPageLoadCall( $sMethodName, $sPageSlug, $osScreenORPageHook ) {

                if ( substr( $sMethodName, strlen( 'load_pre_' ) ) !== $sPageSlug ) {
                    return false;
                }
                if ( ! isset( $this->oProp->aPageHooks[ $sPageSlug ] ) ) {
                    return false;
                }
                $_sPageHook = is_object( $osScreenORPageHook )
                    ? $osScreenORPageHook->id
                    : $sPageSlug; // for ajax calls
                return $_sPageHook === $this->oProp->aPageHooks[ $sPageSlug ];

            }

    /* Shared methods */

    /**
     * Checks whether the class should be instantiated.
     *
     * @since       3.1.0
     * @since       3.3.1       Moved from `AdminPageFramework_Base`.
     * @internal
     */
    protected function _isInstantiatable() {

        if ( $this->_isWordPressCoreAjaxRequest() ) {
            return false;
        }

        // Nothing to do in the network admin area.
        return ! is_network_admin();

    }

    /**
     * Checks whether the currently loading page is of the given pages.
     *
     * @since       3.0.2
     * @since       3.2.0       Changed the scope to `public` from `protected` as the head tag object will access it.
     * @since       3.3.1       Moved from `AdminPageFramework_Base`.
     * @since       3.8.14      Changed the visibility scope to `protected` from `public` as there is the `isInThePage()` public method.
     * @internal
     */
    protected function _isInThePage() {

        if ( ! $this->oProp->bIsAdmin ) {
            return false;
        }

        // If the `setUp()` method is not loaded yet, nothing can be checked
        // as there is not page is added.
        if ( ! did_action( 'set_up_' . $this->oProp->sClassName ) ) {
            return true;
        }

        return $this->oProp->isPageAdded();

    }

    /**
     * Loads factory specific components.
     *
     * @internal
     * @callback    action      current_screen
     * @return      void
     * @since       3.7.0
     */
    public function _replyToLoadComponents( /* $oScreen */ ) {

        if ( 'plugins.php' === $this->oProp->sPageNow ) {
            $this->oLink = $this->_replyTpSetAndGetInstance_oLink();
        }
        parent::_replyToLoadComponents();

    }

}
