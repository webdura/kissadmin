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
            <? if(isset($user_search)) { ?>
                <div class="search">
                    <form method="GET" name='searchform'>
                    <b>Client&nbsp;:&nbsp;</b>
                    <select name="userId" id="userId" class="inputbox_green" style="width:;" onchange="document.searchform.submit();">
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
                        <select name="group_id" id="group_id" onchange="document.searchform.submit();" style="width:300px">
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
    </div>
    </form>
    <div class="contents">