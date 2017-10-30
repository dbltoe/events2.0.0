<?php
/**
 * @package Page Template for Scheduled Events 2.0.0
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_events_default.php 3001 2017-11-01 11:45:06Z dbltoe $
 */

?>
<div class="centerColumn" id="events">
<h1 id="eventsDefaultHeading"><?php echo HEADING_TITLE; ?></h1>
<hr />
<?php
  // Display the text of each event in a paragraph
  foreach($eventData as $eventInfo) {
    echo "<h5>" . $eventInfo["events_title"] . "</h5><hr />";
    echo "<blockquote>";
    if ($eventInfo['events_location'] != '') echo "<strong>Place:  </strong>" . $eventInfo["events_location"] . "<br />";
    if ($eventInfo['events_start'] != '') echo "<strong>Start Date:  </strong>" . zen_date_long($eventInfo["events_start"]) .  "<br />";
    if ($eventInfo['events_stop'] != '') echo "<strong>Stop Date:  </strong>" . zen_date_long($eventInfo["events_stop"]) .  "<br />";
    if ($eventInfo['events_comments'] != '') echo "<strong>Comments:  </strong>" . $eventInfo["events_comments"] .   "<br />";
    if ($eventInfo['events_booth_location'] != '') echo "<strong>Booth Location:  </strong>" . $eventInfo["events_booth_location"] .  "<br />";
    if ($eventInfo['events_special_text'] != '') echo "<strong>Special:  </strong>" . $eventInfo["events_special_text"] . "<br />";
    if ($eventInfo['events_map'] != '') echo "<strong><a href=" . $eventInfo["events_map"] . ' target="_blank">Driving Directions</a></strong>';
    echo "</blockquote><hr />";
  } 
?>
<div class="buttonRow back"><?php echo zen_back_link() . zen_image_button(BUTTON_IMAGE_BACK, BUTTON_BACK_ALT) . '</a>'; ?></div>
</div>