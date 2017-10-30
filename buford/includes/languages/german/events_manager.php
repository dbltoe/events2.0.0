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

define('TABLE_HEADING_EVENTS', 'Events');
define('TABLE_HEADING_EVENTS_START', 'Startdatum');
define('TABLE_HEADING_EVENTS_END', 'Enddatum');
define('TABLE_HEADING_PUBLISHED', 'Veröffentlicht');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_ACTION', 'Aktion');

define('TEXT_EVENTS_TITLE', 'Events Titel:');
define('TEXT_EVENTS_CONTENT', 'Events Inhalt:');
define('TEXT_MORE_EVENTS_PAGE', 'Link zur "Events" Seite:');
define('TEXT__EVENTS_CONTENT', 'Events Inhalt:');

define('TEXT_EVENTS_DATE_ADDED', 'Hinzugefügt am:');
define('TEXT_EVENTS_DATE_MODIFIED', 'Verändert am:');
define('TEXT_EVENTS_START_DATE', 'News startet am:');
define('TEXT_EVENTS_END_DATE', 'News endet am:');
define('TEXT_EVENTS_PUBLISHED_DATE', 'Veröffentlicht am:');

define('TEXT_EVENTS_DELETE_INFO', 'Are you sure you want to delete this Scheduled Event?');

define('ERROR_EVENTS_TITLE_CONTENT', 'The event title and content must be set for at least one language');

define('TEXT_DISPLAY_NUMBER_OF_EVENTS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> events)');
define('TEXT_EVENTS_MANAGER_INFO', 'The Scheduled Events is set to display up to <b>%s</b> bytes (characters) not including HTML tags before link to "Events" page is automatically shown. The # can be configured in Layout Settings.<br />To edit or delete an Event entry, it needs to be in unpublished mode.');