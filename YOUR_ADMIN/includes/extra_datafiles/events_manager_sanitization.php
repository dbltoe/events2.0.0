<?php
/**
 * @package Sanitization Manager for Scheduled Events 2.0.0
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: events_manager_sanitization.php 3001 2017-11-01 11:45:06Z dbltoe $
 */

// define the Configuration > Scheduled Events Settings

if (class_exists ('AdminRequestSanitizer') && method_exists ('AdminRequestSanitizer', 'getInstance')) {
    $events_mgr_sanitizer = AdminRequestSanitizer::getInstance();
    $events_mgr_sanitizer->addSimpleSanitization ('PRODUCT_DESC_REGEX', array ( 'events_title', 'events_content' ));
}