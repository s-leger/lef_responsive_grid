<?php
namespace LEF\LefResponsiveGrid\ContentObject;

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

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class LefResponsiveGridMenuContentObject extends ContentObjectRenderer
{
    protected $children = array();
    protected $childs   = array();
    protected $index    = array();

    private function get_child_nodes($parent_id)
    {
        $parent_id = $parent_id === null ? 0 : $parent_id;
        if (isset($this->index[$parent_id])) {
            foreach ($this->index[$parent_id] as $id) {
                $this->children[] = $this->childs[$id];
                $this->get_child_nodes($id);
            }
        }
    }

    public function sectionIndex($content='', $conf)
    {
        $configuration  = array();
        $skipHeaderCheck       = true;
        $skipSectionIndexCheck = false;
        $type = 'all';
        $menu = array();
        if (isset($this->cObj->data['doktype'])) {
            // called from a page
      $pid = $this->cObj->data['uid'];
        } else {
            // called from a content object
      $pid = $this->cObj->data['pid'];
        }
        $useColPos = -1;

        if (is_array($conf['sectionIndex.'])) {
            $configuration = $conf['sectionIndex.'];
            if (trim($configuration['useColPos']) !== '' || is_array($configuration['useColPos.'])) {
                $useColPos = $GLOBALS['TSFE']->cObj->stdWrap($configuration['useColPos'], $configuration['useColPos.']);
                $useColPos = (int)$useColPos;
            }

            if (isset($configuration['type'])) {
                $type = $configuration['type'];
            };

            $skipHeaderCheck   = strpos($type, 'header') === false;    // skip header checking if header not found
      $skipSectionIndexCheck   = strpos($type, 'all')    !== false;        // skip sectionIndex checking only if all found
        }

    /*
   $content .= '<pre>pid=' . print_r($this->cObj->data, TRUE)."</pre>\n";
   $content .= '<pre>type=' . print_r($type, TRUE)."</pre>\n";
    $content .= '<pre>skipHeaderCheck=' . print_r($skipHeaderCheck, TRUE)."</pre>\n";
    $content .= '<pre>skipSectionIndexCheck=' . print_r($skipSectionIndexCheck, TRUE)."</pre>\n";
    $menuItem = array();
          $menuItem['title'] .= $content;
    $menu[] = $menuItem;
    */
    $where = $useColPos >= 0 ? 'colPos=' . $useColPos : 'colPos != -2 
      AND pid IN (-1,' . $pid . ') 
      AND hidden<>1 
      AND CType!=\'div\'
      ' . $this->cObj->enableFields('tt_content') . '
      ';
        if (isset($conf['additionalWhere'])) {
            $where .= '
      ' . $conf['additionalWhere'] . '
      ';
        }

        if ($GLOBALS['TSFE']->sys_language_uid > 0) {
            if ($GLOBALS['TSFE']->sys_language_contentOL) {
                if ($element) {
                    $where .= '  
                AND sys_language_uid IN (-1,' . $GLOBALS['TSFE']->sys_language_uid . ')
              AND l18n_parent = 0
          ';
                }
            } else {
                if ($element) {
                    $where .= ' 
                AND sys_language_uid IN (-1,' . $GLOBALS['TSFE']->sys_language_uid . ')
          ';
                }
            }
        } else {
            $where .= ' 
                AND sys_language_uid IN (-1,0)
          ';
        }

        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tt_content', $where, '', 'colPos ASC, sorting ASC, tx_gridelements_columns ASC');
        if (!$GLOBALS['TYPO3_DB']->sql_error()) {
            while ($child = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
                $isValidHeader        = $skipHeaderCheck       || (((int)$child['header_layout'] !== 100 || !empty($configuration['includeHiddenHeaders'])) && trim($child['header']) !== '');
                $doIncludeInSectionIndex = $skipSectionIndexCheck || $child['sectionIndex'] >= 1;

                $sorting = $child['sorting'];

                $GLOBALS['TSFE']->sys_page->versionOL('tt_content', $child, true);

          // Language overlay:
          if (is_array($child)) {
              if ($GLOBALS['TSFE']->sys_language_contentOL) {
                  $child = $GLOBALS['TSFE']->sys_page->getRecordOverlay('tt_content', $child, $GLOBALS['TSFE']->sys_language_content, $GLOBALS['TSFE']->sys_language_contentOL);
              }

              if ($child !== false) {
                  if ($GLOBALS['TYPO3_CONF_VARS']['FE']['activateContentAdapter']) {
                      FrontendContentAdapterService::modifyDBRow($child, 'tt_content');
                  }
                  $child['is_valid'] = $isValidHeader && $doIncludeInSectionIndex;
                  $child['sorting'] = $sorting;
            //  $child['header'] .= ' doInclude='.($doIncludeInSectionIndex ? 1:0) .' res='. (($includeItem) ? 1:0) .' isValidHeader='.($isValidHeader ? 1:0) ;
              $id = $child['uid'];
                  $parent_id = $child['tx_gridelements_container'] === 0 ? 0 : $child['tx_gridelements_container'];
                  $this->childs[$id] = $child;
                  $this->index[$parent_id][] = $id;

                  unset($child);
              }
          }
            }

            $GLOBALS['TYPO3_DB']->sql_free_result($res);

        /*
        $content .= '<pre>'.print_r($this->index, TRUE).'</pre>';
        $menuItem = array();
        $menuItem['title'] = $content;
         $menu[] = $menuItem;
        */
        // sort by columns
        $compareFunction = function ($a, $b) {
            $child_a = $this->childs[$a]['tx_gridelements_columns'];
            $child_b = $this->childs[$b]['tx_gridelements_columns'];
            if ($child_a > $child_b) {
                return 1;
            } elseif ($child_a === $child_b) {
                if ($this->childs[$a]['sorting'] > $this->childs[$b]['sorting']) {
                    return 1;
                } elseif ($this->childs[$a]['sorting'] < $this->childs[$b]['sorting']) {
                    return -1;
                } else {
                    return 0;
                }
            } else {
                return -1;
            }
        };
        // sort by columns
        foreach ($this->index as $parent_id=>$index) {
            $len = count($index);
            if ($parent_id !== 0 && $len > 1) {
                usort($this->index[$parent_id], $compareFunction);
            }
        }

        // flatten by parent
        $this->get_child_nodes(null);

        // build menu array
        foreach ($this->children as $k=>$child) {
            if ($child['is_valid']) {
                $menuItem = array();
                $menuItem['title']         = $child['header'];
                $menuItem['subtitle']      = $child['subheader'];
                $menuItem['starttime']      = $child['starttime'];
                $menuItem['endtime']      = $child['endtime'];
                $menuItem['fe_group']      = $child['fe_group'];
                $menuItem['media']        = $child['media'];
                $menuItem['header_layout']    = $child['header_layout'];
                $menuItem['bodytext']      = $child['bodytext'];
                $menuItem['image']        = $child['image'];
                $menuItem['sectionIndex_uid']   = $child['uid'];
          //  $menuItem['_OVERRIDE_HREF']     = '#c'.$child['uid'];
          //  $menuItem['ITEM_STATE']     = 'NO';
            $menu[] = $menuItem;
            }
        }
        }

        return $menu;
    }

    public function prev($menu)
    {
        $index = 0;
    // $id   = $GLOBALS['TSFE']->id;
    foreach ($menu as $k=>$menuitem) {
        if ($menuitem['uid'] == $GLOBALS['TSFE']->id) {
            $index = $k;
        }
        $last = $k;
    }
        if ($index == 0) {
            $index = count($menu)-1;
        } else {
            $index -= 1;
        }
        $res = array();
        $res[] = $menu[$index];
    /*
    $content = '<pre>index:'.$index.' pid:'.$id.' '.print_r($menu[$last], TRUE).'</pre>';
    $menuitem = array();
    $menuitem['title'] = $content;
    $res[] = $menuitem;
    */
    return $res;
    }

    public function next($menu)
    {
        $index = 0;
        foreach ($menu as $k=>$menuitem) {
            if ($menuitem['uid'] == $GLOBALS['TSFE']->id) {
                $index = $k;
            }
        }
        $len = count($menu)-1;
        if ($index == $len) {
            $index = 0;
        } else {
            $index +=1;
        }
        $res = array();
        $res[] = $menu[$index];
        return $res;
    }
}
