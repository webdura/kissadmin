<?php
include("functions.php");

$task = (isset($_REQUEST['task'])) ? $_REQUEST['task'] : '';

switch ($task)
{
    case 'checkUserName':
        $user_id  = (isset($_REQUEST['user_id'])) ? $_REQUEST['user_id'] : 0;
        $userName = (isset($_REQUEST['userName'])) ? $_REQUEST['userName'] : '';
        
        $user_id  = GetSQLValueString($user_id, 'int');
        $userName = GetSQLValueString($userName, 'text');
        
        $user_sql = "SELECT * FROM gma_logins WHERE userName=$userName AND userId!=$user_id";
        $total_entries = mysql_num_rows(mysql_query($user_sql));
        echo ($total_entries>0) ? 'false' : 'true';
        break;
        
    case 'checkEmail':
        $user_id = (isset($_REQUEST['user_id'])) ? $_REQUEST['user_id'] : 0;
        $email   = (isset($_REQUEST['email'])) ? $_REQUEST['email'] : '';
        
        $user_id = GetSQLValueString($user_id, 'int');
        $email   = GetSQLValueString($email, 'text');
        
        $user_sql = "SELECT * FROM gma_logins WHERE email=$email AND userId!=$user_id";
        $total_entries = mysql_num_rows(mysql_query($user_sql));
        echo ($total_entries>0) ? 'true' : 'true';
        //echo ($total_entries>0) ? 'false' : 'true';
        break;
        
    case 'serviceorder':
        $service_id    = (isset($_REQUEST['id']) && $_REQUEST['id']>0) ? $_REQUEST['id'] : 0;
        $service_order = (isset($_REQUEST['order']) && $_REQUEST['order']>0) ? $_REQUEST['order'] : 0;
        
        $service_id    = GetSQLValueString($service_id, 'int');
        $service_order = GetSQLValueString($service_order, 'int');
        
        $service_sql = "UPDATE gma_services SET `order`=$service_order WHERE `id`=$service_id";
        mysql_query($service_sql);
        break;
        
    case 'grouporder':
        $group_id    = (isset($_REQUEST['id']) && $_REQUEST['id']>0) ? $_REQUEST['id'] : 0;
        $group_order = (isset($_REQUEST['order']) && $_REQUEST['order']>0) ? $_REQUEST['order'] : 0;
        
        $group_id    = GetSQLValueString($group_id, 'int');
        $group_order = GetSQLValueString($group_order, 'int');
        
        $group_sql = "UPDATE gma_groups SET `order`=$group_order WHERE `id`=$group_id";
        mysql_query($group_sql);
        break;
        
    case 'checkAmount':
        $request    = (isset($_REQUEST['service_id']) && $_REQUEST['service_id']!='') ? $_REQUEST['service_id'] : 0;
        $request    = explode('_', $request);
        $service_id = GetSQLValueString($request[0], 'int');
        
        $group_sql = "SELECT * FROM gma_services WHERE `id`=$service_id";
        $group_rs  = mysql_query($group_sql);
        $group_row = mysql_fetch_assoc($group_rs);
        
        echo $group_row['amount'];
        break;
        
    case 'checkDiscount':
        $userId = (isset($_REQUEST['userId']) && $_REQUEST['userId']>0) ? $_REQUEST['userId'] : 0;
        $userId = GetSQLValueString($userId, 'int');
        
        $user_discount = array();
        $discount_sql  = "SELECT * FROM gma_user_discount WHERE userId='$userId'";
        $discount_rs   = mysql_query($discount_sql);
        while($discount_row = mysql_fetch_assoc($discount_rs))
        {
            $user_discount[] = $discount_row['group_id'].'~~~'.$discount_row['discount'];
        }
        
        echo implode('~!~', $user_discount);
        break;
        
    case 'permission':
        $per_id  = (isset($_REQUEST['id']) && $_REQUEST['id']!='') ? $_REQUEST['id'] : '';
        $checked = (isset($_REQUEST['checked']) && $_REQUEST['checked']==1) ? 1 : 0;
        $per_id  = explode('_', $per_id);
        
        $admins_id = $module_id = 0;
        $admins_id = $per_id[0];
        if(isset($per_id[1]))
            $module_id = $per_id[1];
        
        if($admins_id>0 && $module_id>0)
        {         
            if($checked==1)
                $sql = "INSERT INTO gma_admins_permission SET companyId='$ses_companyId', admins_id='$admins_id', module_id='$module_id';";
            else 
                $sql = "DELETE FROM gma_admins_permission WHERE companyId='$ses_companyId' AND admins_id='$admins_id' AND module_id='$module_id';";
            mysql_query($sql);
        }
        break;
        
    case 'changeTheme':
        $theme_id = (isset($_REQUEST['theme_id']) && $_REQUEST['theme_id']>0) ? $_REQUEST['theme_id'] : 0;
        $theme_id = GetSQLValueString($theme_id, 'int');
        
        $theme_sql = "SELECT * FROM gma_theme WHERE id=$theme_id";
        $theme_rs  = mysql_query($theme_sql);
        $theme_row = mysql_fetch_assoc($theme_rs);
        echo json_encode($theme_row);
        break;
        
    case 'userOrders':
        $user_id    = (isset($_REQUEST['user_id']) && $_REQUEST['user_id']>0) ? $_REQUEST['user_id'] : 0;
        $payment_id = (isset($_REQUEST['payment_id']) && $_REQUEST['payment_id']>0) ? $_REQUEST['payment_id'] : 0;
        $user_id    = GetSQLValueString($user_id, 'int');
        
        $results        = array();
        $orders         = '';
        $pending_amount = 0;
        
        if($user_id>0) {
            $order_sql = "SELECT * FROM gma_order WHERE userId=$user_id AND (orderStatus!=1 OR id IN (SELECT orderId FROM gma_payment_order WHERE paymentId='$payment_id'))";
            //echo $order_sql.' == ';
            $order_rs  = mysql_query($order_sql);
            if(mysql_num_rows($order_rs)>0) {
                while ($order_row = mysql_fetch_assoc($order_rs)) {
                    $selected = ($order_row['orderStatus']==1) ? 'checked' : '';
                    $orders  .= "<div align='left'><input type='checkbox' id='orderId' name='orderId[]' value='{$order_row['id']}' {$selected}>#{$order_row['invoiceId']} - R ".formatMoney($order_row['invoice_amount'], true)."</div>";;
                }
            } else  {
                $orders = 'No Orders Found';
            }
        } else {
            $orders = 'Please select any client';
        }
        
        $order_sql = "SELECT SUM(amount) AS amount FROM gma_payments WHERE userId=$user_id AND paymentId!='$payment_id'";
        $order_rs  = mysql_query($order_sql);
        $order_row = mysql_fetch_assoc($order_rs);
        $pending_amount += $order_row['amount'];
        // echo "$order_sql == $pending_amount<br>";
        
        $order_sql = "SELECT SUM(invoice_amount) AS invoice_amount FROM gma_order WHERE userId=$user_id AND orderStatus=1";
        $order_rs  = mysql_query($order_sql);
        $order_row = mysql_fetch_assoc($order_rs);
        $pending_amount -= $order_row['invoice_amount'];
        // echo "$order_sql == $pending_amount<br>";
        
        $results = array('orders'=>$orders, 'pending_amount'=>$pending_amount, 'pending_amount_div'=>'R '.formatMoney($pending_amount, true));
        echo json_encode($results);
        break;
        
    case 'checkOrderAmount':
        $user_id     = (isset($_REQUEST['user_id']) && $_REQUEST['user_id']>0) ? $_REQUEST['user_id'] : 0;
        $orderId     = (isset($_REQUEST['orderId']) && $_REQUEST['orderId']!='') ? $_REQUEST['orderId'] : 0;
        $amount      = (isset($_REQUEST['amount']) && $_REQUEST['amount']>0) ? $_REQUEST['amount'] : 0;
        $orderAmount = 0;
        
        $order_sql = "SELECT * FROM gma_order WHERE id IN ($orderId)";
        $order_rs  = mysql_query($order_sql);
        if(mysql_num_rows($order_rs)>0) {
            while ($order_row = mysql_fetch_assoc($order_rs)) {
                $orderAmount += $order_row['invoice_amount'];
            }
        }
        $result = array('result'=>'success'); //, 'message'=>$order_sql.' == '.$orderAmount.' == '.$amount);
        if($orderAmount>$amount) {
            $result = array('result'=>'error', 'message'=>'Payment And Invoice amount mismatch !');// == '.$order_sql.' == '.$orderAmount.' == '.$amount);
        }
        echo json_encode($result);
        break;
}
exit;