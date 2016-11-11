<?php
  namespace LEF\LefResponsiveGrid\UserFuncs;

use TYPO3\CMS\Core\Utility\GeneralUtility;

  class AddFieldToFlexform
  {
      protected $settings;

      public function __construct()
      {
          $this->settings = GeneralUtility::makeInstance('LEF\\LefResponsiveGrid\\Services\\BackendTyposcriptConfigurationService', 'tx_lefresponsivegrid');
      }

      public function addFields($config)
      {

      /*
      # available : {prefix}{modifier}{breakpoint}{column}{columns}
      # you may add separators within each variable eg {prefix-} or {prefix_}

      template{
        column = col-{breakpoint}-{column}
        offset = col-{breakpoint}-offset-{column}
        pull   = col-{breakpoint}-pull-{column}
        push   = col-{breakpoint}-push-{column}
        hideup   = hidden-{breakpoint}-up
        hidedown = hidden-{breakpoint}-down
        hidden   = hidden-{breakpoint}
        visible  = visible-{breakpoint}
        hiddenprint = hidden-print
      }

      breakpoints{
        0=xs
        1=sm
        2=md
        3=lg
        4=xl
      }
      columns{
        0=12
        1=5
      }

      */
      $grids = $this->settings->get('settings.columns');
          $optionList = array();
          $optionList[0] = array(0 => 'not set', 1 => 0);
          foreach ($grids as $g=>$grid) {
              $cols = intval($grid);
              if ($cols<1) {
                  continue;
              }
              for ($c = 0; $c<$cols; $c++) {
                  $label = ($c + 1) . '/' . $grid;
                  $value = ($c + 1) . '.' . $grid;
                  $optionList[] = array(0 => $label, 1 => $value);
              }
          }
          $config['items'] = array_merge($config['items'], $optionList);
          return $config;
      }
  }
