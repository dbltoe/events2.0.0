<?php
/**
 * @package Initialize Settings Manager for Scheduled Events 2.0.0
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: init_events_manager_admin.php 3001 2017-11-01 11:45:06Z dbltoe $
 */

// Initialize the Scheduled Events Settings

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

define('EVENTS_CURRENT_VERSION', '2.0.0');
define('EVENTS_CURRENT_UPDATE_DATE', '2017-11-01');
define('EVENTS_CURRENT_VERSION_DATE', EVENTS_CURRENT_VERSION . ' (' . EVENTS_CURRENT_UPDATE_DATE . ')');

function init_nbm_next_sort ($menu_key) {
  global $db;
  $next_sort = $db->Execute('SELECT MAX(sort_order) as max_sort FROM ' . TABLE_ADMIN_PAGES . " WHERE menu_key='$menu_key'");
  return $next_sort->fields['max_sort'] + 1;
}

$configurationGroupTitle = 'Scheduled Events Manager';
$configuration = $db->Execute ("SELECT configuration_group_id FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_title = '$configurationGroupTitle' LIMIT 1");
if ($configuration->EOF) {
  $db->Execute("INSERT INTO " . TABLE_CONFIGURATION_GROUP . " 
                 (configuration_group_title, configuration_group_description, sort_order, visible) 
                 VALUES ('$configurationGroupTitle', '$configurationGroupTitle Settings', '1', '1');");
  $cgi = $db->Insert_ID(); 
  $db->Execute("UPDATE " . TABLE_CONFIGURATION_GROUP . " SET sort_order = $cgi WHERE configuration_group_id = $cgi;");
  
} else {
  $cgi = $configuration->fields['configuration_group_id'];
  
}

// ----
// Record the configuration's current version in the database.
//
if (!defined ('EVENTS_MODULE_VERSION')) {
  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function) VALUES ('Scheduled Events Manager Version', 'EVENTS_MODULE_VERSION', '" . EVENTS_CURRENT_VERSION_DATE . "', 'The Scheduled Events Manager version number and installation date.', $cgi, 10, now(), 'trim(')");
   
  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function ) VALUES ( 'Items to Show on Events Page', 'EVENTS_SHOW_NEWS', '5', 'Set the maximum number of the latest-events titles to show in the &quot;Scheduled Events&quot; page.', $cgi, 40, now(), NULL, NULL)");
   
//  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function ) VALUES ( 'Items to Show on Home Page', 'EVENTS_SHOW_CENTERBOX', '0', 'Set the maximum number of the latest-events titles to show in the &quot;Latest Events&quot; section at the bottom of your home page.  Set the value to 0 to disable the Events display.', $cgi, 45, now(), NULL, NULL)");
   
//  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function ) VALUES ( 'Events Archive: Items to Display', 'EVENTS_SHOW_ARCHIVE', '10', 'Set the maximum number of the latest-events titles to show on the split-page view of the &quot;Events Archive&quot; page.', $cgi, 47, now(), NULL, NULL)");
  
//  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function ) VALUES ( 'Events Archive: Date Format', 'EVENTS_DATE_FORMAT', 'short', 'Choose the style of dates to be displayed for an event\'s start/end dates on the &quot;Events Archive&quot; page.  Choose <em>short</em> to have dates displayed similar to <b>03/02/2017</b> or <em>long</em> to display the date like <b>Monday 02 March, 2017</b>.<br /><br />The date-related settings you have made in your primary language files are honoured using the built-in functions <code>zen_date_short</code> and <code>zen_date_long</code>, respectively.', $cgi, 50, now(), NULL, 'zen_cfg_select_option(array(\'short\', \'long\'),')");
  
  define ('EVENTS_MODULE_VERSION', EVENTS_CURRENT_VERSION_DATE);
  
}
//if (!defined ('EVENTS_CONTENT_LENGTH_CENTERBOX')) {
//  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function ) VALUES ( 'Home Page Events Content Length', 'EVENTS_CONTENT_LENGTH_CENTERBOX', '0', 'Set the maximum number of characters (an integer value) of each events\'s content to display within the home-page center-box.  Set the value to <em>0</em> to disable the content display or to <em>-1</em> to display each events\'s entire content (no HTML will be stripped).', $cgi, 46, now(), NULL, NULL)");
  
//  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function ) VALUES ( 'Events Archive: Event Content Length', 'EVENTS_CONTENT_LENGTH_ARCHIVE', '0', 'Set the maximum number of characters (an integer value) of each event\'s content to display within the &quot;Events Archive&quot; page.  Set the value to <em>0</em> to disable the content display or to <em>-1</em> to display each event\'s entire content (no HTML will be stripped).', $cgi, 48, now(), NULL, NULL)");
  
//}

// -----
// Update the configuration table to reflect the current version, if it's not already set.
//
if (EVENTS_MODULE_VERSION != EVENTS_CURRENT_VERSION_DATE) {
  $db->Execute ("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '" . EVENTS_CURRENT_VERSION_DATE . "' WHERE configuration_key = 'EVENTS_MODULE_VERSION'");
  
}

// ----
// Create each of the database tables for the event records.
//
$sql = "CREATE TABLE IF NOT EXISTS " . TABLE_SCHEDULED_EVENTS . " (
  `events_id` int(11) NOT NULL auto_increment,
  `events_added_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `events_modified_date` datetime default NULL,
  `events_start_date` datetime default NULL,
  `events_end_date` datetime default NULL,
  `events_status` tinyint(1) default '0',
  PRIMARY KEY  (`events_id`)
) ENGINE=MyISAM";
$db->Execute($sql);

$sql = "CREATE TABLE IF NOT EXISTS " . TABLE_SCHEDULED_EVENTS_CONTENT . " (
  `events_id` int(11) NOT NULL default '0',
  `languages_id` int(11) NOT NULL default '1',
  `events_title` varchar(255) NOT NULL default '',
  `events_location` varchar(75) NULL,
  `events_comments` varchar(255) NULL,
  `events_special_text` varchar(200) NULL,
  `events_booth_location` varchar(255) NULL,
  `events_map` varchar(255) NULL,
  PRIMARY KEY  (`languages_id`,`events_id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET " . DB_CHARSET;
$db->Execute($sql);

// -----
// Register the admin-level pages for use.
//
if (!zen_page_key_exists ('localizationEvents')) {
  zen_register_admin_page('localizationEvents', 'BOX_EVENTS_MANAGER', 'FILENAME_EVENTS_MANAGER', '', 'localization', 'Y', init_nbm_next_sort('localization'));
  
}
if (!zen_page_key_exists ('configEvents')) {
  zen_register_admin_page('configEvents', 'BOX_EVENTS_MANAGER', 'FILENAME_CONFIGURATION', "gID=$cgi", 'configuration', 'Y', init_nbm_next_sort('configuration'));
  
}
