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
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function recentitems_civicrm_xmlMenu(&$files) {
  _recentitems_civix_civicrm_xmlMenu($files);
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
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function recentitems_civicrm_postInstall() {
  _recentitems_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function recentitems_civicrm_uninstall() {
  _recentitems_civix_civicrm_uninstall();
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
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function recentitems_civicrm_disable() {
  _recentitems_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function recentitems_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _recentitems_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function recentitems_civicrm_managed(&$entities) {
  _recentitems_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function recentitems_civicrm_caseTypes(&$caseTypes) {
  _recentitems_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function recentitems_civicrm_angularModules(&$angularModules) {
  _recentitems_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function recentitems_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _recentitems_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 */
function recentitems_civicrm_navigationMenu(&$menu) {
  // Find the "Home" menu item for retrieving its weight.
  $menu_item_search = array(
    'name' => 'Home',
  );
  $menu_items = array();
  CRM_Core_BAO_Navigation::retrieve($menu_item_search, $menu_items);

  _recentitems_civix_insert_navigation_menu($menu, NULL, array(
    'label' => E::ts('Recent'),
    'name' => 'recently_viewed',
    'navID' => _recentitems_navhelper_create_unique_nav_id($menu),
    // Place directly after the "Home" item, using its weight.
    // See https://github.com/civicrm/civicrm-core/pull/11772 for weight.
    'weight' => (isset($menu_items['weight']) ? $menu_items['weight'] : 0),
    'icon' => 'fa fa-clock-o',
  ));

  foreach (CRM_Utils_Recent::get() as $i => $item) {
    _recentitems_civix_insert_navigation_menu($menu, 'recently_viewed', array(
      'label' => $item['title'],
      'url' => ltrim($item['url'], '/'),
      'name' => 'recently_viewed_' . $i,
      'icon' => ($item['subtype'] ?: $item['type']) . '-icon icon crm-icon',
      'navID' => _recentitems_navhelper_create_unique_nav_id($menu),
      'class' => 'crm-container',
    ));
    _recentitems_civix_insert_navigation_menu($menu, 'recently_viewed/recently_viewed_' . $i, array(
      'label' => E::ts('View'),
      'url' => ltrim($item['url'], '/'),
      'name' => 'recently_viewed_' . $i . '_view',
      'navID' => _recentitems_navhelper_create_unique_nav_id($menu),
    ));

    if ($item['edit_url']) {
      _recentitems_civix_insert_navigation_menu($menu, 'recently_viewed/recently_viewed_' . $i, array(
        'label' => E::ts('Edit'),
        'url' => ltrim($item['edit_url'], '/'),
        'name' => 'recently_viewed_' . $i . '_edit',
        'navID' => _recentitems_navhelper_create_unique_nav_id($menu),
      ));
    }
    if ($item['delete_url']) {
      _recentitems_civix_insert_navigation_menu($menu, 'recently_viewed/recently_viewed_' . $i, array(
        'label' => E::ts('Delete'),
        'url' => ltrim($item['delete_url'], '/'),
        'name' => 'recently_viewed_' . $i . '_delete',
        'navID' => _recentitems_navhelper_create_unique_nav_id($menu),
      ));
    }
  }

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

///**
// * Hook implementation: Inject JS code adjusting summary view
// */
//function recentitems_civicrm_pageRun(&$page) {
//  CRM_Core_Region::instance('page-header')->add(array(
//    'type' => 'styleUrl',
//    'styleUrl' => E::url('css/recentitems.css'),
//  ));
//}

/**
 * Implements hook_civicrm_buildAsset().
 *
 * Use hook_civicrm_buildAsset() to define the asset 'mycss'
 * It locates the css template in the extension and the required image from core
 * and substitutes the image path into the css template returning the value via
 * the $content parameter.
 */
function recentitems_civicrm_buildAsset($asset, $params, &$mimetype, &$content) {
  // Check for the asset of interest
  if ($asset !== 'recentitems.css') return;

  // Find the path to our template css file
  $path = \Civi::resources()->getPath('de.systopia.recentitems', 'css/recentitems.css');

  // Read in the template
  $raw = file_get_contents($path);

  // Get the URL of the image we want from Core
  // Note that the 'civicrm' string here is a special to refer to the installation location of the core files
  $url = \Civi::resources()->getUrl('civicrm', 'i/item_sprites.png');

  // Replace the LOGO_URL token in the file with the actual url
  // Note that $content is passed by reference to this hook function
  $content = str_replace('ICON_SPRITE_URL', $url, $raw);

  // Set the mimetype appropriately for the type of content
  // Note that $mimetype is passed by reference to this hook function
  $mimetype = 'text/css';
}

/**
 * Implements hook_civicrm_coreResourceList().
 */
function recentitems_civicrm_coreResourceList(&$list, $region) {
  // To include the file without any processing we could use:
  // CRM_Core_Resources::singleton()->addStyleFile('org.example.myextension', 'css/my_css.css');
  // replace that with the following:

  // use the asset_builder service to get the url of an asset labeled 'mycss'
  $url = \Civi::service('asset_builder')->getUrl('recentitems.css');

  // load the processed style on the page
  CRM_Core_Resources::singleton()->addStyleUrl($url);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function recentitems_civicrm_preProcess($formName, &$form) {

} // */
