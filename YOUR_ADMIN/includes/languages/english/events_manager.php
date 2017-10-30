<?php
/**
 * @package Settings Manager for Scheduled Events 2.0.0
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: events_extra_pages_filenames.php 3001 2017-11-01 11:45:06Z dbltoe $
 */

// define the Configuration > Scheduled Events Settings

define('EVENTS_HEADING_TITLE', 'Scheduled Events Manager');

define('TABLE_HEADING_EVENTS', 'Scheduled Events');
define('TABLE_HEADING_EVENTS_START', 'Start Date');
define('TABLE_HEADING_EVENTS_END', 'End Date');
define('TABLE_HEADING_MODIFIED', 'Last Modified');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_ACTION', 'Action');

define('TEXT_EVENTS_TITLE', 'Event Title:');
define('TEXT_EVENTS_CONTENT', 'Event Content:');
define('TEXT__EVENTS_CONTENT', 'Event Content:');

define('TEXT_EVENTS_DATE_ADDED', 'Date Added:');
define('TEXT_EVENTS_DATE_MODIFIED', 'Date Modified:');
define('TEXT_EVENTS_START_DATE', 'Event Will Start:');
define('TEXT_EVENTS_END_DATE', 'Event Will End:');

define('TEXT_EVENT_DELETE_INFO', 'Are you sure you want to delete this Scheduled Event?');

define('ERROR_EVENT_TITLE_CONTENT', 'The <em>Event Title</em> and <em>Event Content</em> must BOTH be non-blank for at least ONE language');
define('ERROR_EVENT_DATE_ISSUES', 'The Event <em>Start Date</em> must be on or before the <em>End Date</em>.');
define('SUCCESS_EVENT_CHANGED', 'The Scheduled Event has been %s.');
define('EVENT_UPDATED', 'updated');
define('NEWS_ARTICLE_CREATED', 'created');

define('TEXT_DISPLAY_NUMBER_OF_EVENTS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> events)');
define('TEXT_EVENTS_MANAGER_INFO', 'Use this tool to create Scheduled Events that are displayed in your store.  Refer to the settings in <em>Configuration-&gt;Events Manager</em> for the various settings.<br /><br />A valid event must have a non-blank &quot;Event Title&quot; AND &quot;Event Content&quot; in at least one of your store\'s languages.');
define('TEXT_EDIT_INSERT_INFO', 'If you leave the <em>Start Date</em> blank, its value will default to today.  Leave the <em>End Date</em> blank for an event that never expires.');