<?php
  namespace LEF\LefResponsiveGrid\Services;
  
  use TYPO3\CMS\Core\Utility\GeneralUtility;
  
  
  
   /**
     * Build up the configuration
     */
    public function __construct()
    {
        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['nc_staticfilecache'])) {
            $extensionConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['nc_staticfilecache']);
            if (is_array($extensionConfig)) {
                $this->configuration = array_merge($this->configuration, $extensionConfig);
            }
        }
        if (isset($GLOBALS['TSFE']->tmpl->setup['tx_ncstaticfilecache.']) && is_array($GLOBALS['TSFE']->tmpl->setup['tx_ncstaticfilecache.'])) {
            $this->configuration = array_merge($this->configuration,
                $GLOBALS['TSFE']->tmpl->setup['tx_ncstaticfilecache.']);
        }
    }

    /**
     * Get the configuration
     *
     * @param string $key
     *
     * @return null|mixed
     */
    public function get($key)
    {
        $result = null;
        if (isset($this->configuration[$key])) {
            $result = $this->configuration[$key];
        } elseif (isset($GLOBALS['TSFE']->config['config']['tx_staticfilecache.'][$key])) {
            $result = $GLOBALS['TSFE']->config['config']['tx_staticfilecache.'][$key];
        }
        return $result;
    }
  
  
  class FrontendConfigurationManager extends AbstractConfigurationManager {
    
    protected $settings;
    
    protected function getTypoScriptSetup(){
      return $GLOBALS['TSFE']->tmpl->setup;
    }
    
    /**
     * getExtensionConfiguration
     * @param string $extensionName   eg : vendor_extension_name (without tx_)
     */
    protected function getExtensionConfiguration($extensionName){
      
      // CamelCaseExtensionName
      $extensionName = str_replace(' ', '', ucwords(str_replace('_', ' ', $extensionName)));
      
      $setup = $this->getTypoScriptSetup();
      
      $extensionConfiguration = array();
      if (is_array($setup['plugin.']['tx_' . strtolower($extensionName) . '.'])) {
        $extensionConfiguration = $this->typoScriptService->convertTypoScriptArrayToPlainArray($setup['plugin.']['tx_' . strtolower($extensionName) . '.']);
      }
      return $extensionConfiguration;
    }
    
  }