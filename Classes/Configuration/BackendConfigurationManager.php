<?php
  namespace LEF\LefResponsiveGrid\Services;

use TYPO3\CMS\Backend\Utility\BackendUtility;
  use TYPO3\CMS\Core\Utility\GeneralUtility;

  class BackendConfigurationManager
  {
      protected $settings;

      protected function loadTypoScript($extKey)
      {
          list($page) = BackendUtility::getRecordsByField('pages', 'pid', 0);
          $pageUid = intval($page['uid']);
          $sysPageObj = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Page\\PageRepository');
          $rootLine = $sysPageObj->getRootLine($pageUid);
          $TSObj = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\TypoScript\\ExtendedTemplateService');
          $TSObj->tt_track = 0;
          $TSObj->init();
          $TSObj->runThroughTemplates($rootLine);
          $TSObj->generateConfig();
          return $TSObj->setup['plugin.'][$extKey . '.'];
      }

      public function __construct($extkey)
      {
          $this->settings = $this->loadTypoScript($extkey);
      }

      public function get($path)
      {
          $res = $this->settings;
          $keys = explode('.', $path);
          foreach ($keys as $k=>$key) {
              if (is_array($res[$key . '.'])) {
                  $res = $res[$key . '.'];
              } else {
                  return $res[$key];
              }
          }
          return $res;
      }
  }
