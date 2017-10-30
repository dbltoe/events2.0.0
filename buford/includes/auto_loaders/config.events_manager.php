<?php
/**
 * @package Configuration Initialization for Scheduled Events 2.0.0
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: events_extra_pages_filenames.php 3001 2017-11-01 11:45:06Z dbltoe $
 */

// Initialize the Scheduled Events Admin Manager

$autoLoadConfig[200][] = array ('autoType' => 'init_script',
                                'loadFile' => 'init_events_manager_admin.php');