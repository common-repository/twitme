<?php
/*  Copyright 2008  Johnny Mast  email : info@phpvrouwen.nl

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


### Load WP-Config File If This File Is Called Directly
if (!function_exists('set_option')) {
	require_once('../../../wp-config.php');
}

require_once 'twitme.php';


/**
 * This file handles the ajax calls for the manage page.
 */

$oInstance = new twitclass();


if (isset ($_GET['action']) && $_GET['action'] == 'getFavorites')
{
	$aFavorites = $oInstance->getFavorites();
	
	if (sizeof ($aFavorites) > 0)
	{
		foreach ($aFavorites as $i => $aPost)
		{
			$aUser       = $aPost->user;
			$sOldText    = $aPost->text; 
			$aPost->text = $oInstance->massageLinks ($aPost->text, $aUser->screen_name);
			
			?>
			<div id="favorite_<?=$i?>">
			  <div style="float: left;width:397px;" class="timeline_entry"> <img height="48" width="48" src="<?=$aUser->profile_image_url;?>" align="texttop" style="float: left; padding-right: 5px; cursor:hand; cursor: pointer;" onclick="makeAtReply('<?=$aUser->screen_name?>');" />
				<div align="left" style="width: 300px;"><strong><a href="http://www.twitter.com/<?=$aUser->screen_name?>" target="_blank">
				  <?=$aUser->screen_name?>
				  </a></strong>
				  <?=trim ($aPost->text) ?>
				  <br />
				  <span class="post-meta">
				    <?=twitme_gettimedistance($aPost->created_at)?>&nbsp;<?=__("ago from",TWITME_TRANSLATION_DOMAIN)?>&nbsp;<?=$aPost->source?>
				  </span> 
                 </div>
			  </div>
			  <div style="float: left; "> <img src="<?php echo TWITME_PATH;?>/images/icon_star_full.gif"  title="<?=__("unmark as favorite",TWITME_TRANSLATION_DOMAIN)?>" onclick="unMarkFavorite('favorite_<?=$i?>', <?=$aPost->id?>);" style="float:right; ;cursor: pointer;"><br />
				<?php
					if (TWITME_USER == $aUser->name) 
					{
					 ?><img src="<?php echo TWITME_PATH;?>/images/trash.png"  title="<?=__("Delete this message",TWITME_TRANSLATION_DOMAIN)?>" onclick="deleteMessage(<?=$aPost->id?>);" style="float:right;cursor: pointer;"><br /><?
					} 
				?>
			  </div>
			  <div style="clear:both; padding-top: 5px"></div>
			  <hr />
			</div>
		  <?
		}
	} else
	echo '<strong>'.__("You have no favorites yet",TWITME_TRANSLATION_DOMAIN).'</strong>';
	
} else
if (isset ($_GET['action']) && $_GET['action'] == 'getTwitmode')
{
	$iPage = 1;
	
	if (isset ($_GET['page'])) 
	 $iPage = $_GET['page'];
	
	
	/*
	** Litle hack we do TWITME_TWITS_PER_PAGE + 1 to see if we need 
	** a next button because if we get an amount of TWITME_TWITS_PER_PAGE + 1 post in return
	** we know that we could display at least one post on a next page.
	*/
	$iOffset         = ceil ($iPage * TWITME_TWITS_PER_PAGE);
	$aPublicTimeline = $oInstance->getPublicTimeLine(TWITME_TWITS_PER_PAGE+1, $iPage);
	
	if (is_array ($aPublicTimeline)) 
	{
		$aTmp            = $aPublicTimeline;
		$aPublicTimeline = array_slice ($aPublicTimeline, $iOffset, TWITME_TWITS_PER_PAGE + $iOffset);
		$bshowNext       = ((TWITME_TWITS_PER_PAGE +1) - sizeof ($aPublicTimeline)) > 0;
		$bshowPrev       = $iPage > 1;
		// 36 pages
		$aPublicTimeline = $aTmp;
		
		if (sizeof ($aPublicTimeline) > 0)
		{
			
			foreach ($aPublicTimeline as $aPost)
			{
				$aUser       = $aPost->user;
				$sOldText    = $aPost->text; 
				$aPost->text = $oInstance->massageLinks ($aPost->text, $aUser->screen_name);
		
				?>
				   <div style="float: left;width:397px;" class="timeline_entry">
						<img height="48" width="48" src="<?=$aUser->profile_image_url;?>" align="texttop" style="float: left; padding-right: 5px; cursor:hand; cursor: pointer;" onclick="makeAtReply('<?=$aUser->screen_name?>');" />
					   <div align="left" ><strong><a href="http://www.twitter.com/<?=$aUser->screen_name?>" target="_blank"><?=$aUser->screen_name?></a></strong>
						 <?=trim ($aPost->text)?> <br /><span class="post-meta"><?=twitme_gettimedistance($aPost->created_at)?>&nbsp;<?=__("ago from",TWITME_TRANSLATION_DOMAIN)?>&nbsp; <?=$aPost->source?></span>
					   </div>
				   </div>
					<div style="float: left; " class="timeline_entry">
					<?
						
						if ($aPost->favorited == true) {
						 ?><img src="<?php echo TWITME_PATH;?>/images/icon_star_full.gif"  title="<?=__("Unmark this message as Favorite",TWITME_TRANSLATION_DOMAIN)?>" onclick="unMarkFavorite('favorite_<?=$i?>', <?=$aPost->id?>);" style="float:right;cursor: pointer;"><br /><?
						} else
						{
						 ?><img src="<?php echo TWITME_PATH;?>/images/icon_star_empty.gif"  title="<?=__("Mark this message as Favorite",TWITME_TRANSLATION_DOMAIN)?>" onclick="markFavorite(<?=$aPost->id?>);" style="float:right;cursor: pointer;"><br /><?								
						}
				
						if (TWITME_USER == $aUser->name) {
						 ?><img src="<?php echo TWITME_PATH;?>/images/trash.png"  title="<?=__("Delete this message",TWITME_TRANSLATION_DOMAIN)?>" onclick="deleteMessage(<?=$aPost->id?>);" style="float:right; cursor: pointer;"><br /><?
						} else
						{
						  ?><img src="<?php echo TWITME_PATH;?>/images/reply.png"  title="<?=__("Reply to this message",TWITME_TRANSLATION_DOMAIN)?>" onclick="makeAtReply('<?=$aUser->screen_name?>'); document.location.href='#Twitmode_update';" style="float:right;cursor: pointer;"><br /><?
						}
					 ?>			  
					</div>
				   <div style="clear:both; padding-top: 5px"></div>
				   <hr />						
				     <?
			} 
			if ($bshowPrev)
			{
			  ?>
			  <img src="<?php echo TWITME_PATH;?>/images/prevlabel.gif"  title="<?=__("Show newer posts",TWITME_TRANSLATION_DOMAIN)?>" onclick="loadTwitmode(<?=$iPage-1?>)" />
		      <?	
			}
			if ($bshowNext) {
			  ?>
				     <img src="<?php echo TWITME_PATH;?>/images/nextlabel.gif"  title="<?=__("Show older posts",TWITME_TRANSLATION_DOMAIN)?>" style="cursor: pointer" onclick="loadTwitmode(<?=$iPage+1?>)" />
			  <?				
			}
		
		}else
		echo '<strong>'.__("There are no public messages",TWITME_TRANSLATION_DOMAIN).'</strong><br /><br /><br />';
	}else
	echo '<strong>'.__("There are no public messages",TWITME_TRANSLATION_DOMAIN).'</strong><br /><br /><br />';
	
}else
if (isset ($_GET['action']) && $_GET['action'] == 'getDirectMessages')
{

	$iPage = 1;
	
	if (isset ($_GET['page'])) 
	 $iPage = $_GET['page'];
	
	
	 
	/*
	** Litle hack we do TWITME_TWITS_PER_PAGE + 1 to see if we need 
	** a next button because if we get an amount of TWITME_TWITS_PER_PAGE + 1 post in return
	** we know that we could display at least one post on a next page.
	*/

	$iOffset         = ceil ($iPage * TWITME_TWITS_PER_PAGE);
	$aIncomming      = $oInstance->getDirectMessages(TWITME_TWITS_PER_PAGE+1, $iPage);
	$aTmp            = $aIncomming;
	$iNumPms         = TWITME_NUM_PMS;
		
	if (is_array ($aIncomming))
	{
		$aIncomming      = array_slice ($aIncomming, $iOffset, TWITME_TWITS_PER_PAGE + $iOffset);
		$bshowNext       = ((TWITME_TWITS_PER_PAGE +1) - sizeof ($aIncomming)) > 0;
		$bshowPrev       = $iPage > 1;
		
		$aIncomming      = $aTmp;
		if (sizeof ($aIncomming) > 0)
		{			
			foreach ($aIncomming as $aPost)
			{

				$aUser       = $aPost->sender;
				$sOldText    = $aPost->text; 
				$aPost->text = $oInstance->massageLinks ($aPost->text, $aUser->screen_name);
			    $bTimeStamp  = strtotime( $aPost->created_at );
			    
			  
			    if ( $bTimeStamp > (int)get_option('twitme_lastpm')) {
			      update_option('twitme_lastpm', $bTimeStamp);
			      $iNumPms ++;
			    }
				?>
				   <div style="float: left;width:397px;" class="timeline_entry">
						<img height="48" width="48" src="<?=$aUser->profile_image_url;?>" align="texttop" style="float: left; padding-right: 5px; cursor:hand; cursor: pointer;" onclick="makeReply('<?=$aUser->id?>');" />
					   <div align="left"><strong><a href="http://www.twitter.com/<?=$aUser->screen_name?>" target="_blank"><?=$aUser->screen_name?></a></strong>
						 <?=trim ($aPost->text)?>  <br /><span class="post-meta"><?=twitme_gettimedistance($aPost->created_at)?>&nbsp;<?=__("ago",TWITME_TRANSLATION_DOMAIN)?></span>
					   </div>
				   </div>
					<div style="float: left;">
					<img src="<?php echo TWITME_PATH;?>/images/icon_direct_reply.gif"  title="<?=__("Reply directly to this message",TWITME_TRANSLATION_DOMAIN)?>" onclick="makeReply(<?=$aUser->id?>);" style="float:right; cursor: pointer;"><br />								
					<img src="<?php echo TWITME_PATH;?>/images/trash.png"  title="<?=__("Delete this direct message",TWITME_TRANSLATION_DOMAIN)?>" onclick="deleteDirectMessage(<?=$aPost->id?>);" style="float:right;cursor: pointer;"><br />
					</div>
				   <div style="clear:both; padding-top: 5px"></div>
				   <hr />						
				   <p>
				     <?
			} 			
			echo '<br /><br />';
		
		}else
		echo '<strong>'.__("There are no inbound messages",TWITME_TRANSLATION_DOMAIN).'</strong><br /><br /><br />';
	}else
	echo '<strong>'.__("There are no inbound messages",TWITME_TRANSLATION_DOMAIN).'</strong><br /><br /><br />';
	
	  
	  update_option('twitme_num_pms', $iNumPms);
} else
if (isset ($_GET['action']) && $_GET['action'] == 'getDirectOutgoingMessages')
{

	$iPage = 1;
	
	if (isset ($_GET['page'])) 
	 $iPage = $_GET['page'];
	
	
	/*
	** Litle hack we do TWITME_TWITS_PER_PAGE + 1 to see if we need 
	** a next button because if we get an amount of TWITME_TWITS_PER_PAGE + 1 post in return
	** we know that we could display at least one post on a next page.
	*/

	$iOffset         = ceil ($iPage * TWITME_TWITS_PER_PAGE);
	$aIncomming      = $oInstance->getDirectOutgoingMessages(TWITME_TWITS_PER_PAGE+1, $iPage);
	$aTmp            = $aIncomming;
	
	if (is_array ($aIncomming))
	{
		$aIncomming      = array_slice ($aIncomming, $iOffset, TWITME_TWITS_PER_PAGE + $iOffset);
		$bshowNext       = ((TWITME_TWITS_PER_PAGE +1) - sizeof ($aIncomming)) > 0;
		$bshowPrev       = $iPage > 1;
		
		$aIncomming = $aTmp;
		
		if (sizeof ($aIncomming) > 0)
		{
			
			foreach ($aIncomming as $aPost)
			{
				$aUser       = $aPost->sender;
				$aRecipient  = $aPost->recipient;
				$sOldText    = $aPost->text; 
				$aPost->text = $oInstance->massageLinks ($aPost->text, $aUser->screen_name);
				
				?>
				   <div style="float: left;width:397px;" class="timeline_entry">
						<img height="48" width="48" src="<?=$aRecipient->profile_image_url;?>" align="texttop" style="float: left; padding-right: 5px; cursor:hand; cursor: pointer;" onclick="makeReply('<?=$aRecipient->id?>');" />
					   <div align="left"><strong><a href="http://www.twitter.com/<?=$aUser->screen_name?>" target="_blank"><?=$aUser->screen_name?></a>&nbsp;>>&nbsp;<a href="http://www.twitter.com/<?=$aPost->recipient_screen_nam?>"><?=$aRecipient->screen_name?></a></strong>
						 <?=trim ($aPost->text)?> <br /><span class="post-meta"><?=twitme_gettimedistance($aPost->created_at)?>&nbsp;<?=__("ago",TWITME_TRANSLATION_DOMAIN)?>.</span>
					   </div>
				   </div>
				   <div style="clear:both; padding-top: 5px"></div>
				   <hr />						
				   <p>
				     <?
			} 
			echo '<br /><br />';
		
		}else
		echo '<strong>'.__("There are no inbound messages",TWITME_TRANSLATION_DOMAIN).'</strong><br /><br /><br />';
	}else
	echo '<strong>'.__("There are no inbound messages",TWITME_TRANSLATION_DOMAIN).'</strong><br /><br /><br />';
	
	
	
}
else
if (isset ($_POST['action']) && isset ($_POST['favorite']) &&  $_POST['action'] == 'unMarkfavorite')
{
	$oInstance ->destoryFavorite ($_POST['favorite']);
} else
if (isset ($_POST['action']) && isset ($_POST['favorite']) &&  $_POST['action'] == 'Markfavorite')
{
	$oInstance ->createFavorite ($_POST['favorite']);
} else
if (isset ($_POST['action']) && isset ($_POST['messageID']) &&  $_POST['action'] == 'deleteMessage')
{
	print_rn ( $oInstance ->deleteTimelinePost ($_POST['messageID']) );
} else
if (isset ($_POST['action']) && isset ($_POST['messageID']) &&  $_POST['action'] == 'deleteDirectMessage')
{
	print_rn ( $oInstance ->deleteDirectmessage ($_POST['messageID']) );
}else
if (isset ($_GET['action']) &&  $_GET['action'] == 'resetInboxNotification')
{

	/*
	** Delete the num messages value (if set) because we want to stop  displaying the number of messages in a balloon.
	*/
	update_option('twitme_num_pms', 0);
} else
if (isset ($_POST['action']) && isset ($_POST['update']) &&  $_POST['action'] == 'closeUpdate')
{
	update_option('twitme_updateclosed', $_POST['update']);
}
?>