<?php
/*************************************************************************
    tasks.php

    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/

require('staff.inc.php');
require_once(INCLUDE_DIR.'class.task.php');
$page = '';

$task = null; //clean start.
    $inc = 'calendar.inc.php';

    if ($_REQUEST['id']) {
    	if (!($task=Task::lookup($_REQUEST['id'])))
    		$errors['err'] = sprintf(__('%s: Unknown or invalid ID.'), __('task'));
    	elseif (!$task->checkStaffPerm($thisstaff)) {
    		$errors['err'] = __('Access denied. Contact admin if you believe this is in error');
    		$task = null;
    	}
    }
    

    
require_once(STAFFINC_DIR.'header.inc.php');
require_once(STAFFINC_DIR.$inc);
require_once(STAFFINC_DIR.'footer.inc.php');

?>