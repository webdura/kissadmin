<div id="subcontent">
    <div class="page_header">
        <div class="fleft padding_top">
            <h2 class=""><?=$page_title?></h2>
            <? if(isset($add_url) && $add_url!='') { ?>
                <a class="addnew fleft" href="<?=$add_url?>"><div class="fleft btn_style">+</div><span>&nbsp;&nbsp;Add New</span></a>
            <? } ?>
            <? if(isset($del_url) && $del_url!='') { ?>
                <a class="addnew delete fleft" href="<?=$del_url?>" onclick="<?=$del_click?>"><div class="fleft btn_style">+</div><span>&nbsp;&nbsp;Delete</span></a>
            <? } ?>
            
            <? if(isset($other_urls) && count($other_urls)>0) { ?>
                <? foreach ($other_urls as $other_url) { ?>
                     <a class="addnew delete fleft" href="<?=$other_url['url']?>" onclick="<?=$other_url['click']?>"><div class="fleft btn_style"><?=$other_url['sign']?></div><span>&nbsp;&nbsp;<?=$other_url['text']?></span></a>
                <? } ?>
            <? } ?>
            
            <? if(isset($search_box)) { ?>
                <div class="search"><form method="GET">
                    <input type="text" class="inputbox" name="srchtxt" id="srchtxt" value="<?=$srchtxt?>">&nbsp;&nbsp;<input type="submit" class="btn_style" value="Search">
                </form></div>
            <? } ?>
            <? if(isset($date_search)) { ?>
                <div class="search"><form method="GET">
                    <b>Date Range : </b>
                    <input type="hidden" name="userId" id="userId" value="<?=$userId?>">
                    <input type="hidden" name="date" id="date" value="daterange">
                    <input type="text" name="startdate" id="startdate" value="<?=($startdate)?>" style="width:66px" readonly>-
                    <input type="text" name="enddate" id="enddate" value="<?=($enddate)?>" style="width:66px" readonly>
                    &nbsp;&nbsp;<input type="submit" class="btn_style" value="Search">
                </form></div>
            <? } ?>
            <? if(isset($user_search)) { ?>
                <div class="search">
                    <form method="GET" name='searchform'>
                    <b>Client&nbsp;:&nbsp;</b>
                    <select name="userId" id="userId" class="inputbox_green" style="width:120px;" onchange="document.searchform.submit();">
                        <option value="">Select All</option>
                        <? foreach ($company_users as $user) {
                            $user_Id  = $user['userId'];
                            $name     = $user['businessName'].' - '.$user['userName'];
                            $selected = ($userId==$user_Id) ? 'selected' : '';
                            
                            echo "<option value='$user_Id' $selected>$name</option>";
                        }
                        ?>
                    </select>
                    </form>
                </div>
            <? } ?>
            <? if(isset($group_search)) { ?>
                <div class="search">
                    <form method="GET" name='searchform'>
                        <b>Group&nbsp;:&nbsp;</b>
                        <select name="group_id" id="group_id" onchange="document.searchform.submit();" style="width:120px">
                            <option value="0">Select all</option>
                            <? foreach ($group_rows as $group) {
                                $selected = ($group['id']==$group_id) ? 'selected' : '';
                                
                                echo "<option value='".$group['id']."' $selected>".$group['name']."</option>";
                            }
                            ?>
                        </select>
                    </form>
                </div>
            <? } ?>
        </div>
        <? if(isset($chars)) { ?>
            <div class="padding_top fcenter text_links"><?=$chars?></div>
        <? } ?>
        <? if(isset($pagination) && $pagination!='') { ?>
            <div class="fright pagination"><?=$pagination?></div>
        <? } ?>
        <? if(isset($right_urls) && count($right_urls)>0) { ?>
            <div class="fright padding_top">
                <? foreach ($right_urls as $other_url) { ?>
                     <a class="addnew delete fleft <?=$other_url['class']?>" href="<?=$other_url['url']?>" onclick="<?=$other_url['click']?>" target="<?=$other_url['target']?>"><div class="fleft btn_style"><?=$other_url['sign']?></div><span>&nbsp;&nbsp;<?=$other_url['text']?></span></a>
                <? } ?>
            </div>
        <? } ?>
    </div>
    </form>
    <div class="contents">