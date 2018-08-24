<?php
/*-------------------------------------------------------+
| SYSTOPIA Recently viewed items extension               |
| Copyright (C) 2018 SYSTOPIA                            |
| Author: J. Schuppe (schuppe@systopia.de)               |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+-------------------------------------------------------*/

use CRM_Recentitems_ExtensionUtil as E;

/**
 * API callback for "get" call on "RecentItems" entity.
 *
 * @param $params
 *
 * @return array
 */
function civicrm_api3_recent_items_get($params) {
  $navigations = array();
  CRM_Recentitems_Utils::buildMenuItem($navigations);
  CRM_Recentitems_Utils::buildSubMenu($navigations);

  $navigationString = '';
  //skip children menu item if user don't have access to parent menu item
  $skipMenuItems = array();
  foreach ($navigations as $key => $value) {
    // Home is a special case
    if ($value['attributes']['name'] != 'Home') {
      $name = CRM_Core_BAO_Navigation::getMenuName($value, $skipMenuItems);
      if ($name) {
        //separator before
        if (isset($value['attributes']['separator']) && $value['attributes']['separator'] == 2) {
          $navigationString .= '<li class="menu-separator"></li>';
        }
        $removeCharacters = array('/', '!', '&', '*', ' ', '(', ')', '.');
        $navigationString .= '<li class="menumain crm-' . str_replace($removeCharacters, '_', $value['attributes']['label']) . '">' . $name;
      }
    }
    CRM_Core_BAO_Navigation::recurseNavigation($value, $navigationString, $skipMenuItems);
  }

  // clean up - Need to remove empty <ul>'s, this happens when user don't have
  // permission to access parent
  $navigationString = str_replace('<ul></ul></li>', '', $navigationString);

  return civicrm_api3_create_success($navigationString);
}

/**
 * API specification for "get" call on "RecentItems" entity.
 *
 * @param $params
 */
function _civicrm_api3_recent_items_get_spec(&$params) {

}
