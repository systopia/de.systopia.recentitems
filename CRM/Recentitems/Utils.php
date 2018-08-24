<?php

use CRM_Recentitems_ExtensionUtil as E;

/**
 * Class CRM_Recentitems_Utils
 */
class CRM_Recentitems_Utils {

  /**
   * Build the "Recent items" navigation menu item.
   *
   * @param $menu
   */
  public static function buildMenuItem(&$menu) {
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
  }

  /**
   * Build the "Recent items" sub menu.
   *
   * @param $menu
   */
  public static function buildSubMenu(&$menu) {
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
  }

}
