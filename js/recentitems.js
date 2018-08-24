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

(function($) {
  $(document).ready(function() {
    var $recentMenuItem = $('#civicrm-menu').find('.menumain.crm-Recent');
    var $recentSubMenu = $recentMenuItem.find('> ul');

    var recentItems = CRM.api3('RecentItems', 'get')
      .done(function(result) {
        var $newSubMenu = $(result.values).find('> ul');
        $recentSubMenu.replaceWith($newSubMenu);

        // Re-initialise the navigation menu.
        // Copied from CiviCRM's /templates/CRM/common/navigation.js.tpl
        $('#civicrm-menu').menuBar({arrowSrc: CRM.config.resourceBase + 'packages/jquery/css/images/arrow.png'});

        // Replace icons with shoreditch's replacement.
        // Copied from shoreditch's /js/crm-ui.js
        // TODO: Find a method to do this without copying code.
        $('#root-menu-div .menu-item-arrow').each(function ($element) {
          var $arrow = $(this);

          $arrow.before('<i class="fa fa-caret-right menu-item-arrow"></i>');
          $arrow.remove();
        });

      });
  });
})(cj);
