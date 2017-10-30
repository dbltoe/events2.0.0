<?php
/**
 * @package Settings Manager for Scheduled Events 2.0.0
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: events_extra_pages_filenames.php 3001 2017-11-01 11:45:06Z dbltoe $
 */

// define the Configuration > Scheduled Events Settings

function zen_get_events_title ($events_id, $language_id = '')
{
    global $db;
    if ($language_id == '') {
        $language_id = $_SESSION['languages_id'];
    }
    $events = $db->Execute ("SELECT events_title FROM " . TABLE_SCHEDULED_EVENTS_CONTENT . "  WHERE events_id = " . (int)$events_id . " AND languages_id = " . (int)$language_id . " LIMIT 1");
  
    return ($events->EOF) ? '' : $events->fields['events_title'];
}

//function zen_get_events_content ($events_id, $language_id = '')
//{
//    global $db;
//    if ($language_id == '') {
//        $language_id = $_SESSION['languages_id'];
//    }
//    $events = $db->Execute ("SELECT events_content FROM " . TABLE_SCHEDULED_EVENTS_CONTENT . " WHERE events_id = " . (int)$events_id . " AND languages_id = " . (int)$language_id . " LIMIT 1");
//  
//    return ($events->EOF) ? '' : $events->fields['events_content'];
//}

require('includes/application_top.php');

$languages = zen_get_languages (); 

$action = (isset ($_GET['action']) ? $_GET['action'] : '');
$page_link = (isset ($_GET['page'])) ? ('&page=' . $_GET['page']) : '';
switch ($action) {
    case 'insert':
    case 'update': 
        $events_title = $_POST['events_title'];
        $events_content = $_POST['events_content'];
        $events_start_date = (($_POST['events_start_date'] == '') ? date ('Y-m-d') : zen_db_prepare_input ($_POST['events_start_date'])) . ' 00:00:00';
        $events_end_date = ($_POST['events_end_date'] == '') ? 'NULL' : (zen_db_prepare_input ($_POST['events_end_date']) . ' 23:59:59');
        if (isset ($_POST['nID'])) {
            $nID = (int)$_POST['nID'];
        }
        
        // -----
        // For the Scheduled Event to be saved, it must have both a title and content ** IN AT LEAST ONE OF THE STORE'S LANGUAGES **
        //
        $events_error = array ();
        foreach ($languages as $current_language) {
            $language_id = $current_language['id'];
            if (empty ($events_title[$language_id]) || empty ($events_content[$language_id])) {
                $events_error[$language_id] = true;
            }
        }
        if (count ($events_error) != 0 && count ($events_error) == count ($languages)) {
            $action = 'new';
            $messageStack->add (ERROR_EVENTS_TITLE_CONTENT, 'error');
        } elseif ($events_end_date != 'NULL' && $events_start_date > $events_end_date) {
            $action = 'new';
            $messageStack->add (ERROR_EVENTS_DATE_ISSUES, 'error');
        } else {
            $sql_data_array = array (
                'events_start_date' => $events_start_date,
                'events_end_date' => $events_end_date,
            );

            if ($action == 'insert') {
                $sql_data_array['events_added_date'] = 'now()';
                $sql_data_array['events_status'] = 0;
                zen_db_perform (TABLE_SCHEDULED_EVENTS, $sql_data_array);
                $nID = zen_db_insert_id();
            } else {
                $sql_data_array['events_modified_date'] = 'now()';
                zen_db_perform (TABLE_SCHEDULED_EVENTS, $sql_data_array, 'update', "events_id = " . (int)$nID);
            }
    
            foreach ($languages as $current_language) {
                $language_id = $current_language['id'];
                if (zen_not_null ($events_title[$language_id]) && zen_not_null ($events_content[$language_id])) {
                    $sql_data_array = array (
                        'events_title' => $events_title[$language_id],
                        'events_content' => $events_content[$language_id]
                    );

                    if ($action == 'insert') {
                        $sql_data_array['events_id'] = $nID;
                        $sql_data_array['languages_id'] = $language_id;
                        zen_db_perform (TABLE_SCHEDULED_EVENTS_CONTENT, $sql_data_array);
                        $change_type = SCHED_EVENT_CREATED;
                    } else {
                        zen_db_perform (TABLE_SCHEDULED_EVENTS_CONTENT, $sql_data_array, 'update', "events_id = " . (int)$nID . " AND languages_id = $language_id");
                        $change_type = SCHED_EVENT_UPDATED; 
                    }
                }
            }
            $messageStack->add_session (sprintf (SUCCESS_SCHED_EVENT_CHANGED, $change_type), 'success');
            zen_redirect (zen_href_link (FILENAME_EVENTS_MANAGER, "nID=$nID$page_link"));
        }
        break;

    case 'deleteconfirm':
        $nID = (int)$_GET['nID'];
        $db->Execute ("DELETE FROM " . TABLE_SCHEDULED_EVENTS . " WHERE events_id = $nID");
        $db->Execute ("DELETE FROM " . TABLE_SCHEDULED_EVENTS_CONTENT . " WHERE events_id = $nID");
        zen_redirect (zen_href_link (FILENAME_EVENTS_MANAGER, (isset ($_GET['page']) ? ('page=' . $_GET['page']) : '')));
        break;
        
    case 'status':
        $nID = (int)$_GET['nID'];
        $events = $db->Execute ("SELECT events_status FROM " . TABLE_SCHEDULED_EVENTS . " WHERE events_id = $nID LIMIT 1");
        if (!$events->EOF) {
            $events_status = ($events->fields['events_status'] == 0) ? 1 : 0;
            $db->Execute ("UPDATE " . TABLE_SCHEDULED_EVENTS . " SET events_status = $events_status, events_modified_date = now() WHERE events_id = $nID LIMIT 1");

        }
        zen_redirect (zen_href_link (FILENAME_EVENTS_MANAGER, "nID=$nID$page_link"));
        break;

    case 'set_editor':
        // Reset will be done by init_html_editor.php. Now we simply redirect to refresh page properly.
        $params = '';
        $separator = '';
        if (isset ($_GET['nID'])) {
            $params = 'nID=' . (int)$_GET['nID'];
            $separator = '&';
        }
        if (isset ($_GET['page'])) {
            $params .= $separator . 'page=' . (int)$_GET['page'];
        }
        zen_redirect (zen_href_link (FILENAME_EVENTS_MANAGER, $params));
        break;

    default:
        break;
}
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<style type="text/css">
<!--
.green { color: green; }
.red { color: red; }
-->
</style>
<script type="text/javascript" src="includes/menu.js"></script>
<script type="text/javascript" src="includes/general.js"></script>
<script type="text/javascript">
  <!--
  function init()
  {
    cssjsmenu('navbar');
    if (document.getElementById)
    {
      var kill = document.getElementById('hoverJS');
      kill.disabled = true;
    }
  if (typeof _editor_url == "string") HTMLArea.replaceAll();
  }
  // -->
</script>
<?php
if ($editor_handler != '') {
  include ($editor_handler);

}
?>
</head>
<body onload="init();">
  <!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table border="0" width="100%" cellspacing="2" cellpadding="2">
    <tr>
      <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td class="pageHeading"><?php echo EVENTS_HEADING_TITLE; ?></td>
              <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
             </tr>
          </table></td>
        </tr>

        <tr>
         <td><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
<?php
if ($action == 'new') {
    $form_action = 'insert';
    $parameters = array ( 
        'events_title' => '',
        'events_content' => '',
        'events_added_date' => '',
        'events_modified_date' => '',
        'events_start_date' => '',
        'events_end_date' => ''
    );
    $nInfo = new objectInfo ($parameters);
    if (isset ($_GET['nID']) || isset ($_POST['nID'])) {
        $form_action = 'update';
        $nID = (int)(isset ($_POST['nID'])) ? $_POST['nID'] : ((isset ($_GET['nID'])) ? $_GET['nID'] : 0);
        $events = $db->Execute ("SELECT nc.events_title, date_format(n.events_added_date, '%Y-%m-%d') as events_added_date, date_format(n.events_modified_date, '%Y-%m-%d') as events_modified_date,       date_format(n.events_start_date, '%Y-%m-%d') as events_start_date, date_format(n.events_end_date, '%Y-%m-%d') as events_end_date
                             FROM " . TABLE_SCHEDULED_EVENTS_CONTENT . " nc, " . TABLE_SCHEDULED_EVENTS . " n 
                            WHERE n.events_id = $nID LIMIT 1");
        if (!$events->EOF) {
            $nInfo->objectInfo ($events->fields);
        }
    } else {             
        $nInfo->objectInfo ($_POST);
    }
    if ($nInfo->events_end_date == '0000-00-00') {
        $nInfo->events_end_date = '';
    
    }
?>
        <tr>
          <td><?php echo TEXT_EDIT_INSERT_INFO; ?></td>
        </tr>
        
        <tr>
          <td><?php echo zen_draw_form('events', FILENAME_EVENTS_MANAGER, 'action=' . $form_action . $page_link); if ($form_action == 'update') echo zen_draw_hidden_field ('nID', $nID); ?>
            <div id="spiffycalendar" class="text"></div>
            <link rel="stylesheet" type="text/css" href="includes/javascript/spiffyCal/spiffyCal_v2_1.css">
            <script type="text/javascript" src="includes/javascript/spiffyCal/spiffyCal_v2_1.js"></script>
            <script type="text/javascript">
            <!--
              var dateEventsStart = new ctlSpiffyCalendarBox("dateEventsStart", "events", "events_start_date", "btnDate1", "<?php echo $nInfo->events_start_date; ?>",scBTNMODE_CUSTOMBLUE);
              var dateEventsEnd = new ctlSpiffyCalendarBox("dateEventsEnd", "events", "events_end_date", "btnDate2", "<?php echo $nInfo->events_end_date; ?>",scBTNMODE_CUSTOMBLUE);
            //-->
            </script>
          <table border="0" cellspacing="0" cellpadding="2">
            <tr>
              <td colspan="2"><?php echo zen_draw_separator ('pixel_trans.gif', '1', '10'); ?></td>
            </tr>
            <tr>
              <td class="main"><?php echo TEXT_EVENTS_START_DATE; ?><br /><small>(YYYY-MM-DD)</small></td>
              <td class="main"><?php echo zen_draw_separator ('pixel_trans.gif', '24', '15') . '&nbsp;'; ?><script language="javascript">dateEventsStart.writeControl(); dateEventsStart.dateFormat="yyyy-MM-dd";</script></td>
            </tr>
            <tr>
              <td class="main"><?php echo TEXT_EVENTS_END_DATE; ?><br /><small>(YYYY-MM-DD)</small></td>
              <td class="main"><?php echo zen_draw_separator ('pixel_trans.gif', '24', '15') . '&nbsp;'; ?><script language="javascript">dateEventsEnd.writeControl(); dateEventsEnd.dateFormat="yyyy-MM-dd";</script></td>
            </tr>
            <tr>
              <td class="main"><?php echo zen_draw_separator ('pixel_trans.gif', '1', '35'); ?></td>
            </tr>
<?php 
    $languages = zen_get_languages();
    $first_language = true;
    foreach ($languages as $current_language){
?>
            <tr>
              <td class="main">
<?php
        echo ($first_language) ? TEXT_EVENTS_TITLE : '&nbsp;';
        $first_language = false;
?>
              </td>
              <td class="main"><?php echo zen_image (DIR_WS_CATALOG_LANGUAGES . $current_language['directory'] . '/images/' . $current_language['image'], $current_language['name']) . '&nbsp;' . zen_draw_input_field ('events_title[' . $current_language['id'] . ']', (isset ($events_title[$current_language['id']]) ? stripslashes ($events_title[$current_language['id']]) : zen_get_events_title ($_GET['nID'], $current_language['id'])), zen_set_field_length (TABLE_PRODUCTS_DESCRIPTION, 'products_name')); ?></td>
            </tr>
<?php
    }
?>
            <tr>
              <td colspan="2"><?php echo zen_draw_separator ('pixel_trans.gif', '1', '35'); ?></td>
            </tr>
<?php
    $first_language = true;
    foreach ($languages as $current_language){
?>
            <tr>
              <td class="main" valign="top">
<?php
//        echo ($first_language) ? TEXT_EVENTS_CONTENT : '&nbsp;';
        $first_language = false;
?>
              </td>
              <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                  <td class="main" valign="top"><?php echo zen_image (DIR_WS_CATALOG_LANGUAGES . $current_language['directory'] . '/images/' . $current_language['image'], $current_language['name']); ?>&nbsp;</td>
                  <td class="main"><?php echo zen_draw_textarea_field ('events_content[' . $current_language['id'] . ']', 'soft', '100%', '20', (isset ($events_content[$current_language['id']]) ? stripslashes ($events_content[$current_language['id']]) : zen_get_events_content ($_GET['nID'], $current_language['id'])), 'id="ta' . $current_language['id'] . '"'); ?></td>
                </tr>
              </table></td>
            </tr>
<?php
    }
?>
            <tr>
             <td><?php echo zen_draw_separator ('pixel_trans.gif', '1', '10'); ?></td>
            </tr>
            
            <tr>
             <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
               <tr>
                 <td class="main" align="right"><?php echo (($form_action == 'insert') ? zen_image_submit ('button_save.gif', IMAGE_SAVE) : zen_image_submit ('button_update.gif', IMAGE_UPDATE)). '&nbsp;&nbsp;<a href="' . zen_href_link (FILENAME_EVENTS_MANAGER, (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . (isset($_GET['nID']) ? 'nID=' . $_GET['nID'] : '')) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td>
               </tr>
             </table></td>
            </tr>
            
          </table></form></td>
        </tr>
<?php
} elseif ($action == 'preview') {
    $events_title = TEXT_EVENTS_TITLE;
    foreach ($languages as $current_language){
?>
        <tr>
          <td class="main" colspan="2"><?php echo $events_title; ?></td>
        </tr>
        <tr>
          <td class="main" colspan="2"><?php echo zen_image (DIR_WS_CATALOG_LANGUAGES . $current_language['directory'] . '/images/' . $current_language['image'], $current_language['name']) . '&nbsp;' .  zen_get_events_title ($_GET['nID'], $current_language['id']); ?></td>
        </tr>
        <tr>
          <td class="main" style="width:<?php echo BOX_WIDTH_LEFT; ?>;">&nbsp;</td>
          <td class="main"><div style="height:100%; width:100%; overflow:visible; border:1px solid #ccc;"><?php echo nl2br (zen_get_events_title ($_GET['nID'], $current_language['id'])) . '<br /><br />' . nl2br (zen_get_events_content ($_GET['nID'], $current_language['id'])); ?></div></td>
          <td class="main" style="width:<?php echo BOX_WIDTH_RIGHT; ?>;">&nbsp;</td>
        </tr>
<?php
        $events_title = '&nbsp;';
    }
?>
        <tr>
          <td class="main" colspan="3" align="right"><?php echo '<a href="' . zen_href_link (FILENAME_EVENTS_MANAGER, 'nID=' . $_GET['nID']) . $page_link . '">' . zen_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
        </tr>
<?php
} elseif ($action == 'confirm') {
    $nID = (int)zen_db_prepare_input ($_GET['nID']);
    $events = $db->Execute ("SELECT events_title, events_content FROM " . TABLE_SCHEDULED_EVENTS_CONTENT . " WHERE events_id = $nID LIMIT 1");
    $nInfo = new objectInfo ($events->fields);
} else {
?>
        <tr>
          <td class="main" colspan="2"><?php echo TEXT_EVENTS_MANAGER_INFO; ?></td>
        </tr>
        
        <tr>
          <td class="smallText" width="100%" align="right"><?php echo TEXT_EDITOR_INFO . zen_draw_form ('set_editor_form', FILENAME_EVENTS_MANAGER, '', 'get') . '&nbsp;&nbsp;' . zen_draw_pull_down_menu ('reset_editor', $editors_pulldown, $current_editor_key, 'onchange="this.form.submit();"') . zen_hide_session_id() . ((isset ($_GET['nID'])) ? zen_draw_hidden_field ('nID', (int)$_GET['nID']) : '') . ((isset ($_GET['page'])) ? zen_draw_hidden_field ('page', $_GET['page']) : '') . zen_draw_hidden_field ('action', 'set_editor') . '</form>'; ?></td>
        </tr>
        
        <tr>
          <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr class="dataTableHeadingRow">
                  <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_EVENTS; ?></td>
                  <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_EVENTS_START; ?></td>
                  <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_EVENTS_END; ?></td>
                  <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_MODIFIED; ?></td>
                  <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_STATUS; ?></td>
                  <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
                </tr>
<?php
    $events_query_raw = "select n.events_id, nc.events_title, n.events_added_date, n.events_modified_date, date_format(n.events_start_date, '%Y-%m-%d') as events_start_date, date_format(n.events_end_date, '%Y-%m-%d') as events_end_date, n.events_status from " . TABLE_SCHEDULED_EVENTS . " n, " . TABLE_SCHEDULED_EVENTS_CONTENT . " nc where n.events_id = nc.events_id and nc.languages_id = '" . (int)$_SESSION['languages_id'] . "' order by events_start_date DESC";
    $events_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $events_query_raw, $events_query_numrows);
    $events = $db->Execute($events_query_raw);
    while (!$events->EOF){
        if((!isset($_GET['nID']) || (isset($_GET['nID']) && ($_GET['nID'] == $events->fields['events_id']))) &&   !isset($nInfo) && (substr($action, 0, 3) != 'new')){
            $nInfo = new objectInfo($events->fields);
        }
        if (isset($nInfo) && is_object($nInfo) && ($events->fields['events_id'] == $nInfo->events_id) ){
            echo '<tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(FILENAME_EVENTS_MANAGER, 'nID=' . $nInfo->events_id . $page_link . '&action=preview') . '\'">' . "\n";
        } else {
            echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(FILENAME_EVENTS_MANAGER, '&nID=' . $events->fields['events_id'] . $page_link) . '\'">' . "\n";
        }
        $start_date_class = ($events->fields['events_start_date'] <= date('Y-m-d')) ? 'green' : 'red';
        $end_date_class = ($events->fields['events_end_date'] == '0000-00-00' || $events->fields['events_end_date'] >= date ('Y-m-d')) ? 'green' : 'red';
        $events_end_date = ($events->fields['events_end_date'] == '0000-00-00') ? TEXT_NONE : zen_date_short ($events->fields['events_end_date']);
?>
                  <td class="dataTableContent"><?php echo '<a href="' . zen_href_link (FILENAME_EVENTS_MANAGER, 'nID=' . $events->fields['events_id'] . '&action=preview' . $page_link) . '">' . zen_image (DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . $events->fields['events_title']; ?></td>
                  <td class="dataTableContent" align="right"><span class="<?php echo $start_date_class; ?>"><?php echo zen_date_short ($events->fields['events_start_date']); ?></span></td>
                  <td class="dataTableContent" align="right"><span class="<?php echo $end_date_class; ?>"><?php echo $events_end_date; ?></span></td>
                  <td class="dateTableContent" align="right"><?php echo (($events->fields['events_modified_date'] == NULL) ? $events->fields['events_date_added'] : $events->fields['events_modified_date']); ?></td>
                  <td class="dataTableContent" align="center">
<?php
        echo zen_draw_form ('setstatus', FILENAME_EVENTS_MANAGER, 'action=status&nID=' . $events->fields['events_id'] . $page_link);
        if ($events->fields['events_status'] == 0) {
            $icon_image = 'icon_red_on.gif';
            $icon_title = IMAGE_ICON_STATUS_OFF;
        } else {
            $icon_image = 'icon_green_on.gif';
            $icon_title = IMAGE_ICON_STATUS_ON;
        }
?>
                      <input type="image" src="<?php echo DIR_WS_IMAGES . $icon_image; ?>" alt="<?php echo $icon_title; ?>" />
                    </form>
                  </td>
                  <td class="dataTableContent" align="right"><?php if (isset ($nInfo) && is_object ($nInfo) && ($events->fields['events_id'] == $nInfo->events_id) ) { echo zen_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . zen_href_link (FILENAME_EVENTS_MANAGER, 'nID=' . $events->fields['events_id']) . $page_link . '">' . zen_image (DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
                </tr>
<?php
        $events->MoveNext();
    }
?>
                <tr>
                  <td colspan="6"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                    <tr>
                      <td class="smallText" valign="top"><?php echo $events_split->display_count ($events_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_EVENTS); ?></td>
                      <td class="smallText" align="right"><?php echo $events_split->display_links ($events_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                    </tr>
                    <tr>
                      <td align="right" colspan="2"><?php echo '<a href="' . zen_href_link (FILENAME_EVENTS_MANAGER, 'action=new') . '">' . zen_image_button ('button_insert.gif', IMAGE_INSERT) . '</a>'; ?></td>
                    </tr>
                  </table></td>
                </tr>
               
              </table></td>
<?php
    $heading = array();
    $contents = array();
    switch ($action){
        case 'delete':
            $heading[] = array('text' => '<b>' . $nInfo->events_title . '</b>');
            $contents = array('form' => zen_draw_form('events', FILENAME_EVENTS_MANAGER, 'nID=' . $nInfo->events_id . $page_link . '&action=deleteconfirm'));
            $contents[] = array('text' => TEXT_EVENTS_DELETE_INFO);
            $contents[] = array('text' => '<br><b>' . $nInfo->events_title . '</b>');
            $contents[] = array('align' => 'center', 'text' => '<br>' . zen_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . zen_href_link(FILENAME_EVENTS_MANAGER, 'nID=' . $_GET['nID'] . $page_link) . '">' . zen_image_button ('button_cancel.gif', IMAGE_CANCEL) . '</a>');
            break;

        default:
            if (is_object ($nInfo)) {
                $heading[] = array('text' => '<b>' . $nInfo->events_title . '</b>');
                $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link (FILENAME_EVENTS_MANAGER, 'nID=' . $nInfo->events_id . $page_link . '&action=new') . '">' . zen_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . zen_href_link (FILENAME_EVENTS_MANAGER, 'nID=' . $nInfo->events_id . $page_link . '&action=delete') . '">' . zen_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');
                $contents[] = array('text' => '<br>' . TEXT_EVENTS_DATE_ADDED . ' ' . $nInfo->events_added_date);
                if ($nInfo->events_modified_date != NULL) {
                    $contents[] = array('text' => TEXT_EVENTS_DATE_MODIFIED . ' ' . $nInfo->events_modified_date);
          
                }
            }
            break;
    }
    if (zen_not_null ($heading) && zen_not_null ($contents)) {
        $box = new box;
?>
              <td width="25%" valign="top"><?php echo $box->infoBox ($heading, $contents); ?></td>
            </tr>
          </table></td>
        </tr>
<?php
    }
}
?>
      </table></td>
   <!-- body_text_eof //-->
    </tr>
  </table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>