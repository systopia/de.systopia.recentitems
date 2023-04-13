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

require_once 'recentitems.civix.php';
use CRM_Recentitems_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function recentitems_civicrm_config(&$config) {
  _recentitems_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function recentitems_civicrm_install() {
  _recentitems_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function recentitems_civicrm_enable() {
  _recentitems_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 */
function recentitems_civicrm_navigationMenu(&$menu) {
  // Insert the "Recent items" menu item.
  CRM_Recentitems_Utils::buildMenuItem($menu);

  // Do not build the recent items sub menu here, only include a "Loading"
  // indicator, which will be replaced with the actual sub menu via Ajax.
  _recentitems_civix_insert_navigation_menu($menu, 'recently_viewed', array(
    'label' => E::ts('Loading ...'),
    'name' => 'recently_viewed_loading',
    'icon' => 'fa fa-spinner',
    'navID' => _recentitems_navhelper_create_unique_nav_id($menu),
    'class' => 'crm-container',
  ));

  _recentitems_civix_navigationMenu($menu);
}

/**
 * Helper function for civicrm_navigationMenu
 *
 * Will create a new, unique ID for the navigation menu
 */
function _recentitems_navhelper_create_unique_nav_id($menu) {
  $max_stored_navId = CRM_Core_DAO::singleValueQuery("SELECT max(id) FROM civicrm_navigation");
  $max_current_navId = _recentitems_navhelper_get_max_nav_id($menu);
  return max($max_stored_navId, $max_current_navId) + 1;
}

/**
 * Helper function for civicrm_navigationMenu
 *
 * Will find the (currently) highest nav_item ID
 */
function _recentitems_navhelper_get_max_nav_id($menu) {
  $max_id = 1;
  foreach ($menu as $entry) {
    $max_id = (isset($entry['attributes']['navID']) ? max($max_id, $entry['attributes']['navID']) : 0);
    if (!empty($entry['child'])) {
      $max_id_children = _recentitems_navhelper_get_max_nav_id($entry['child']);
      $max_id = max($max_id, $max_id_children);
    }
  }
  return $max_id;
}

/**
 * Implements hook_civicrm_buildAsset().
 *
 * Use hook_civicrm_buildAsset() to define the asset 'recentitems.css'.
 * It locates the css template in the extension and the required image from core
 * and substitutes the image path into the css template returning the value via
 * the $content parameter.
 */
function recentitems_civicrm_buildAsset($asset, $params, &$mimetype, &$content) {
  // Check for the asset of interest
  if ($asset !== 'recentitems.css') return;

  // Find the path to our template css file.
  $path = \Civi::resources()->getPath('de.systopia.recentitems', 'css/recentitems.css');

  // Read in the template.
  $raw = file_get_contents($path);

  // Get the URL of the image we want from Core. Note that the 'civicrm' string
  // here is a special to refer to the installation location of the core files.
  $url = \Civi::resources()->getUrl('civicrm', 'i/item_sprites.png');

  // Replace the ICON_SPRITE_URL token in the file with the actual url. Note
  // that $content is passed by reference to this hook function.
  $content = str_replace('ICON_SPRITE_URL', $url, $raw);

  // Set the mimetype appropriately for the type of content. Note that $mimetype
  // is passed by reference to this hook function.
  $mimetype = 'text/css';
}

/**
 * Implements hook_civicrm_coreResourceList().
 */
function recentitems_civicrm_coreResourceList(&$list, $region) {
  // To include the file without any processing we could use:
  // CRM_Core_Resources::singleton()->addStyleFile('de.systopia.recentitems', 'css/recentitems.css');
  // replace that with the following:

  // Use the asset_builder service to get the url of an asset labeled
  // 'recentitems.css'.
  $url = \Civi::service('asset_builder')->getUrl('recentitems.css');

  // Load the processed style on the page.
  CRM_Core_Resources::singleton()->addStyleUrl($url);

  // Add JavaScript file on every page.
  Civi::resources()->addScriptFile(E::LONG_NAME, 'js/recentitems.js', 0, $region);

  // Add a setting for whether shoreditch styles are active.
  $custom_css_url = CRM_Core_BAO_Setting::getItem(NULL, 'customCSSURL');
  $shoreditch_active = (strpos($custom_css_url, 'org.civicrm.shoreditch/css/custom-civicrm.css') !== FALSE);
  Civi::resources()->addSetting(array('recentitems' => array('shoreditch' => $shoreditch_active)));
}

/**
 * Implements hook_civicrm_alterAPIPermissions().
 */
function recentitems_civicrm_alterAPIPermissions($entity, $action, &$params, &$permissions)
{
  $permissions['recent_items']['get'] = array('access CiviCRM');
}
