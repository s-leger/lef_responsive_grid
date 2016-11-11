<?php
namespace LEF\LefResponsiveGrid\Hooks;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Stephen Leger <stephen@3dservices.ch>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Class/Function which adds the necessary pure JS stuff for the grid.
 *
 * @author         Stephen Leger
 */
class PageRenderer
{

  /**
   * wrapper function called by hook (\TYPO3\CMS\Core\Page\PageRenderer->render-preProcess)
   *
   * method that adds JS files within the page renderer
   *
   * @param    array                             $parameters   : An array of available parameters while adding JS to the page renderer
   * @param    \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer : The parent object that triggered this hook
   *
   * @return    void
   */
  public function addJS($parameters, &$pageRenderer)
  {
      $filename = '';
      if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) < 7000000) {
          $filename = 'ext_onReady_6.js';
      } else {
          $filename = 'ext_onReady_7.js';
      }
      $pageRenderer->addExtOnReadyCode(// add some more JS here
      file_get_contents(ExtensionManagementUtility::extPath('lef_responsive_grid') . 'Resources/Public/Backend/Javascript/' . $filename), true);
  }
}
