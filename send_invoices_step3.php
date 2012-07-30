<form method="POST" id="uploadCsv" name='uploadCsv' enctype="multipart/form-data">
<input type="hidden" name="action" id="action" value="step3">
<input type="hidden" name="task" id="task" value="update">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="list addedit">
    <tr><th colspan="2"><b>STEP 3 :- Select Clients</b></th></tr>
    <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
        <td colspan="2">
        
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="list addedit">
    <tr>
        <th width="5%">Send</th>  
        <th width="30%">Email Address</th>
        <th width="30%">File in CSV</th>  
        <th>File Status</th>  
        <th width="10%">Send File</th>  
    </tr>
    <? $row = 0; while ($csv_row = fgetcsv($csv_file)) { $file_status=false; ?>
        <tr class="<?=(($row_flag++)%2==1 ? '' : 'altrow')?>">
            <td><input type="checkbox" name="send[<?=$row?>]" value="1" <?=((isset($_SESSION['invoice_users']['send'][$row]) && $_SESSION['invoice_users']['send'][$row]==1) || !isset($_SESSION['invoice_users']['send'][$row])) ? 'checked' : ''?>></td>  
            <td>
                <!--<input type="text" name="email[<?=$row?>]" id="email_<?=$row?>" value="<?=$csv_row[0]?>">-->
                <select name="userId[<?=$row?>]" id="userId_<?=$row?>" class="selectbox">
                    <option value="">Select any user</option>
                    <? foreach ($company_users as $user) { ?>
                        <? $selected = ((isset($_SESSION['invoice_users']['userId'][$row]) && $user['userId']==$_SESSION['invoice_users']['userId'][$row]) || (!isset($_SESSION['invoice_users']['userId'][$row]) && $user['email']==trim($csv_row[0]))) ? 'selected' : ''; ?>
                        <option value="<?=$user['userId']?>" <?=$selected?>><?=$user['businessName']?></option>
                    <? } ?>
                </select>
            </td>
            <td>
                <!--<input type="text" name="filename[<?=$row?>]" id="filename_<?=$row?>" value="<?=$csv_row[1]?>">-->
                <select name="filename[<?=$row?>]" id="filename_<?=$row?>" class="selectbox">
                    <option value="">Select any file</option>
                    <? foreach ($pdf_files as $file) { ?>
                        <? $selected = ((isset($_SESSION['invoice_users']['filename'][$row]) && $file==$_SESSION['invoice_users']['filename'][$row]) || (!isset($_SESSION['invoice_users']['filename'][$row]) && $file==trim($csv_row[1]))) ? 'selected' : ''; ?>
                        <? $file_status = ($selected=='selected') ? true : $file_status; ?>
                        <option value="<?=$file?>" <?=$selected?>><?=$file?></option>
                    <? } ?>
                </select>
            </td>  
            <td><?=($file_status ? '<span>File found</span>' : '<span class="norecords">File not found</span>')?></td>  
            <td><input type="checkbox" name="file[<?=$row?>]" id="file_<?=$row?>" value="1" <?=((isset($_SESSION['invoice_users']['file'][$row]) && $_SESSION['invoice_users']['file'][$row]==1) || !isset($_SESSION['invoice_users']['file'][$row]) && $file_status) ? 'checked' : ''?>></td>  
        </tr>    	
    <? $row++; } ?>
</table>

        </td>  
    </tr>
</table>
<input type="hidden" name="rows" value="<?=$row?>">
<div class="addedit_btn">
    <input type="button" name="back" id="back" value="Go To Previous Step" class="btn_style" onclick="window.location='<?=$default_file?>?action=step2';" />
    <input type="submit" name="sbmt" id="sbmt" value="Proceed To Next Step" class="btn_style" />
</div>
</form>