<?php
 $sMessage = '';
 $sMesage  = '';
 $sStyle   = '';
 $oUpdateChecker = new TwitmeUpdates();
 $oUpdateRecord  = $oUpdateChecker ->checkForUpdates();
 $bFollowersNext = false;
 $bFollowersPrev = false;
 $sPage          = 1;

 if (isset($_GET['follower_page']))
  $sPage = $_GET['follower_page'];
  
  
  if ($oUpdateRecord != NULL && isset ($oUpdateRecord[0]))
 {
 	$oUpdateRecord  = $oUpdateRecord[0];
 	if ((int)$oUpdateRecord->id >= (int)TWITME_LAST_UPDATE) {
 	  if ($oUpdateRecord->id != TWITME_LAST_UPDATE_CLOSED) 
		 $sMesage = html_entity_decode( $oUpdateRecord->message ).'<br /><small style="float: right">[<a name="'.$oUpdateRecord->id.'" id="doTransfer" href=javascript:void(0)">'.__('close',TWITME_TRANSLATION_DOMAIN).'</a>]</small>';
		 update_option( 'twitme_lastupdate', $oUpdateRecord->id);
 	}	
 } 
?>

<div id="twitme_statusUpdate" align="right">
  <center>
    <span style="float: right">
    <?=$sMesage?>
    </span>
  </center>
</div>
<?php
require_once TWITME_PATH.'/twitSubmitall.php';

$oInstance     = new twitclass();
$aFollowers    = $oInstance->getFollowers($sPage);
$aGeoLocations = array(); 
$mQue            = twitme_openQue();
$sSubmitallStyle = ($mQue== FALSE) ? 'style="display: none"' : '';
$aPostsSubmitted = unserialize (TWITME_SUBMITTED);
$bHighlightMessageSend = false;


if (!is_array ($aPostsSubmitted)) $aPostsSubmitted = array();

if (isset ($_POST['cmd']))
{
	
	switch ($_POST['cmd'])
	{
		
		case 'submit_all':
				
			/*
			** Walk the que and submit the posts that have not been submitted yey 
			*/
			if (is_array ($mQue))
			{
				foreach ($mQue as $post_id)
				{
				  $post = get_post($post_id);
				  twitme_notify_twitter ($post_id, $post, true);
				  $aPostsSubmitted [] = $post_id;
				}
				
				/* Brute force ! something seem to be wrong in twitme_notify_twitter 
				** for updating posts IDs from a que.
				*/
				update_option ('twitme_submitted', serialize($aPostsSubmitted));
				
				$sPostsSubmitted = get_option ('twitme_submitted');
				$aPostsSubmitted = unserialize ($sPostsSubmitted);

				
				
				$mQue            = false;
				$sSubmitallStyle = 'style="display: none"';
			}
	     break;
		 
		 case 'submit_post':
		  	$oInstance = new twitclass();
			$sMessage  =  $_POST['send_data'];
			
			$oInstance ->sendTimelinePost ($sMessage);
			
			/* Now that the message is posted
			** Get a new list of posts to display on this page.
			*/
			$aLastPosts = $oInstance->getLastPosts() ;
			$sMessage   = '';
		 break;
		 
		 case 'followers_message':
		    if (isset ($_POST['followerID']) && $_POST['followerID'] > 0)
		    {
		 
				$aFollowers = $oInstance->getFollowers($sPage);
				$sName      = '';
				
				if (!is_array ($aFollowers)) break;
				
				foreach ($aFollowers as $aPerson) {
				  if ($aPerson->id == $_POST['followerID']) { 
				   $sName = $aPerson->screen_name; 
				   break;
				  }
				}
				
				if (isset ($_POST['send_data']) && $_POST['send_data'])
				{
					$oInstance->sendDirectMessage ($_POST['followerID'],  $_POST['send_data']);
					$sMessage = sprintf (__('Your message has been send to %s',TWITME_TRANSLATION_DOMAIN), $sName);
					$bHighlightMessageSend = true;
				}
		    }	 
		 break;
		 
		 
		 case 'deletefollower':
		  	$oInstance = new twitclass();
			

		    if (isset ($_POST['followerID']) && $_POST['followerID'] > 0)
		    {
				$oInstance->deleteFollower ( $_POST['followerID'] );
				$oInstance->allowFollower  ( $_POST['followerID'] );
				$aFollowers = $oInstance->getFollowers($sPage);
		    }
	   	 break;
	}
	   

}
$aPosts          = get_posts();
$aTwittedPosts   = array ();
$iNumPosted      = 0;
$iPosts          = count ($aPosts);

if ($aFollowers['follower_count'] > 0)
{
	$iFollowers      = $aFollowers['follower_count'];
	$iOffset         = ($sPage * TWITME_FOLLOWERS_PER_PAGE);
	
	unset ($aFollowers['follower_count']);

	$bFollowersPrev =  $sPage > 1;
	$bFollowersNext  = ($iFollowers  - $iOffset) > 0;
 
	$aTmp            = $aFollowers;
	$aTmp			 = array_slice ($aTmp, 0, TWITME_FOLLOWERS_PER_PAGE);
	$aFollowers      = $aTmp;
	
	

} else
unset ($aFollowers['follower_count']);

foreach ($aPosts as $aPost) if (in_array ($aPost->ID, $aPostsSubmitted)) $aTwittedPosts [] = $aPost;
if (is_array ($aTwittedPosts)) $iNumPosted = count ($aTwittedPosts);

/* This fixes an old compliated bug.
** Suppose there are a load of messages send to Twitter BUT the admin desides to delete all the messages from the db and
** start all over again. In that situation we want to reset the stored submitted items as well.
*/
if (sizeof ($aPosts) <= 0)
{
	update_option ('twitme_submitted', serialize(array()));
	
	$aPostsSubmitted = array();
	$iPosts     = 0;
	$iNumPosted = 0;
}
$iCnt = 0;


if ($iNumPosted == 0 && $iPosts > 0)
 $sSubmitallStyle =  'style="display: none"';
 
?>
<script type="text/javascript" src="<?php echo TWITME_PATH;?>/scripts/jquery.js"></script>
<script type="text/javascript" src="<?php echo TWITME_PATH;?>/scripts/ui/ui.core.js" ></script>
<script type="text/javascript" src="<?php echo TWITME_PATH;?>/scripts/ui/effects.core.js" ></script>
<script type="text/javascript" src="<?php echo TWITME_PATH;?>/scripts/ui/ui.draggable.js" ></script>
<script type="text/javascript" src="<?php echo TWITME_PATH;?>/scripts/ui/ui.resizable.js" ></script>
<script type="text/javascript" src="<?php echo TWITME_PATH;?>/scripts/ui/ui.dialog.js" ></script>
<script type="text/javascript" src="<?php echo TWITME_PATH;?>/scripts/ui/ui.sortable.js" ></script>
<script type="text/javascript" src="<?php echo TWITME_PATH;?>/scripts/ui/effects.blind.js" ></script>
<script type="text/javascript" src="<?php echo TWITME_PATH;?>/scripts/ui/effects.highlight.js"></script>
<script type="text/javascript" src="<?php echo TWITME_PATH;?>/scripts/ui/ui.tabs.js" ></script>
<script type="text/javascript" src="<?php echo TWITME_PATH;?>/scripts/json.js" ></script>
<script type="text/javascript" src="<?php echo TWITME_PATH;?>/scripts/twitAjax.js" ></script>
<?php if (TWITME_GOOGLE_KEY <> '') {
?>
	<script type="text/javascript" src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?=TWITME_GOOGLE_KEY?>"></script>
	<script type="text/javascript" src="<?php echo TWITME_PATH;?>/scripts/maps.js" ></script>
<?
}
?>
<link rel="stylesheet" type="text/css" href="<?php echo TWITME_PATH;?>/Twitme-style.css" />
<h2>
  <?=__("Posts that TwitMe has submitted to Twitter", TWITME_TRANSLATION_DOMAIN)?>
</h2>
<br />
<?=__("On this page you will find the names of the posts that TwitMe has send over to Twitter.", TWITME_TRANSLATION_DOMAIN)?>
<br />
<br />
<div id="twit_resultDiv"></div>
<input type="hidden" id="twit_url" value="<?php echo TWITME_PATH;?>" />
<form action="" method="post">
  <span id="twitme_submitall" <?=$sSubmitallStyle?>>
  <input type="submit" class="button" value="<?=__("Submit all",TWITME_TRANSLATION_DOMAIN)?>" />
  <input type="hidden" name="cmd" value="submit_all" />
  <?=sprintf (__("You have (%s) Post(s) that are not yet submitted to Twitter. You can press the <strong>submit all</strong> button to submit the stories that have not been posted to Twitter yet", TWITME_TRANSLATION_DOMAIN), count ($mQue));?>
  <br />
  <br />
  </span>
</form>
<br />
<div id="result_content" style="background:#ffffff; width: 295px;">
  <?=$sMessage?>
</div>
<br />
<?php
   if ($iNumPosted == 0 && $iPosts == 0)
   {
	   
		echo '<table width="280" cellpadding="0" cellspacing="0" class="widefat"> 
			   <tr>
			     <td>'.__("No posts have been send and you have ly no posts to submit to Twitter", TWITME_TRANSLATION_DOMAIN).'</td>
			   </tr>
			   </table>';
	   
   } else if ($iNumPosted == 0 && $iPosts > 0)
   {
   	
		echo '
		  <table cellpadding="0" cellspacing="0" class="widefat" style="border: none;">
			   <tr>
			     <td><span id="twitme_submitall_posts">'.__("No posts have been submitted YET. If you want to submit all available stories press the submit all button", TWITME_TRANSLATION_DOMAIN).'
				 <br />
				 <input type="button" id="twit_submitall" value="submit all" class="button" onclick="twitSubmitall()" />
				 <input type="hidden" id="twit_url" value="'.TWITME_PATH.'" />
				 <input type="hidden" id="twit_resultDiv" value="result_content" />
				 </span>
				 </td>
			   </tr>
			   </table>
	   		
			  ';
	   
   } else
   if ($iNumPosted > 0)
   {
		echo '
			
		    <table cellpadding="0" cellspacing="0" class="widefat" >
		       <thead>
		        <tr>
				   <th scope="Col">'.__("Post ID", TWITME_TRANSLATION_DOMAIN).'</th>
				   <th scope="Col">'.__("Author", TWITME_TRANSLATION_DOMAIN).'</th>
				   <th scope="Col">'.__("Title", TWITME_TRANSLATION_DOMAIN).'</th>
				   <th scope="Col">'.__("Comments", TWITME_TRANSLATION_DOMAIN).'</th>
				   <th scope="Col">'.__("Date", TWITME_TRANSLATION_DOMAIN).'</th>
				   <th scope="Col">'.__("Action", TWITME_TRANSLATION_DOMAIN).'</th>
				</tr>
			   </thead>
			   ';

		foreach ($aTwittedPosts as $key =>$val)
		{
			echo '<tr>
					<td>'.$val->ID.'</td>
					<td ><a href="'.get_author_posts_url($val->post_author).'">'.get_author_name($val->post_author).'</a></td>
					<td ><a href="'.$val->guid.'">'.$val->post_title.'</a></td>
					<td>'.$val->comment_count.'</td>
					<td>'.$val->post_date.'</td>
					<td>
					 <input type="button" class="button" value="'.__("Resend", TWITME_TRANSLATION_DOMAIN).'" onclick="twitResendPost('.$val->ID.')" />
					</td>

				</tr>';
			
		}
		echo '</table>';
   }
 	
   ?>
<br />
<br />
  <?
   if (TWITME_WP_VERSION >= 2.7)
     echo '<div id="wp_27_container" >';
   ?>
  <br />
  <?
  
	echo '
	   <form method="post" action="" name="frmFollowersList">
		<table cellpadding="0" cellspacing="0"  class="widefat" >
		   <thead>
		   <tr>
		   <td scope="Col">'.__('These are you followers', TWITME_TRANSLATION_DOMAIN).' ('.count($aFollowers).')'.'
		   ';
	if ($bFollowersPrev )
	{
	 if (TWITME_WP_VERSION >= 2.7)
	  echo '&nbsp;<a href="admin.php?page=twitme/twitMenus.php&follower_page='.($sPage-1).'">'.__('Prev',TWITME_TRANSLATION_DOMAIN).'</a>&nbsp;';
	   else
	 echo '&nbsp;<a href="edit.php?page=twitmemanage&follower_page='.($sPage-1).'">'.__('Prev',TWITME_TRANSLATION_DOMAIN).'</a>&nbsp;';
	}
    if ($bFollowersNext )
	{
		if (TWITME_WP_VERSION >= 2.7)
		echo '|&nbsp;<a href="admin.php?page=twitme/twitMenus.php&follower_page='.($sPage+1).'">'.__('Next',TWITME_TRANSLATION_DOMAIN).'</a>&nbsp;';
		else
         echo '|&nbsp;<a href="edit.php?page=twitmemanage&follower_page='.($sPage+1).'">'.__('Next',TWITME_TRANSLATION_DOMAIN).'</a>&nbsp;';
	}
	echo  '</td>
		   </tr>
		   </thead>
		   <tr>
		    <td>
			<div style="height: 200px; overflow: auto;">
		   ';

	if (sizeof ($aFollowers) <= 0)
	{
	   echo '<center><strong>'.__('You have no followers yet', TWITME_TRANSLATION_DOMAIN).'</strong></center>';	
	} else
	foreach ($aFollowers as $aFollower)
 	{
		?>
          <table style="float: left;  ">
            <tr valign="top">
            
              <td><img height="48" width="48" src="<?=$aFollower->profile_image_url;?>" style="cursor: hand; cursor: pointer;" onclick="makeAtReply('<?=$aFollower->screen_name?>');" /></td>
              
               <td style="vertical-align:top; border: none; padding-top: 0px">
             
              
              <img src="<?php echo TWITME_PATH;?>/images/trash.png"  title="Delete Follower" onclick="deleteFollower(<?=$aFollower->id?>);" style="float:right; cursor: pointer;"><br />
                <img src="<?php echo TWITME_PATH;?>/images/sendmessage.gif" title="Send direct message" style="float:right;cursor: pointer;"  height="16" width="16" onclick="showFollowerMessagesDialog('<?=$aFollower->id;?>');" align="top" />
               
               
                </td>
            </tr>
            
            <tr>
              <td colspan="2" style="padding: 0; border: none;">
              <a target="_blank" href="http://twitter.com/<?=$aFollower->screen_name;?>">
                <?=$aFollower->screen_name;?>
                </a>
              </td>
            </tr>
          </table>
  		<?
		if ($iCnt == TWITME_MAXFOLLOWERS_PER_ROW) 
		{
		  $iCnt = 0;
		} else
		$iCnt ++;
	}
	echo '</div>';
	echo '</td>';
    echo '</tr>';
	echo '</table>';
	echo '<input type="hidden" name="cmd" value="deletefollower" />';
	echo '<input type="hidden" id="follower_Id" name="followerID" />';
	echo '</form>';
  ?>
  <br>
  <br>
<br>
  <table cellpadding="0" cellspacing="0" class="widefat">
    <thead>
      <tr>
        <td scope="Col"><?=__("Send an update to twitter", TWITME_TRANSLATION_DOMAIN)?></td>
      </tr>
    </thead>
    <tr>
      <td><!-- BEGIN -->
        <div id="twitMode" style="height: 400px;">

          <ul style="height: 27px;">
                <li><a href="#tab-2"><span><?=__('Timeline', TWITME_TRANSLATION_DOMAIN)?></span></a></li>
                <li><a href="#tab-3"><span><?=__('Favorites', TWITME_TRANSLATION_DOMAIN)?></span></a></li>
                <li><a href="#tab-4" id="Twitme_inbox_tab"><span><?=__('Inbox', TWITME_TRANSLATION_DOMAIN)?></span></a></li>
                <li><a href="#tab-5"><span><?=__('Outbox', TWITME_TRANSLATION_DOMAIN)?></span></a></li>
                <?php 
                if (TWITME_GOOGLE_KEY <> '') {
                  ?>
                    <li><a href="#tab-6"><span><?=__('Twit Map', TWITME_TRANSLATION_DOMAIN)?></span></a></li>
                  <?
                 }
                ?>
                <li><a href="#tab-7"><span><?=__('About', TWITME_TRANSLATION_DOMAIN)?></span></a></li>
           </ul>
          
          
          <div id="tab-2">
            <div id="top"></div>
            <br />
            <form action="" method="post" enctype="application/x-www-form-urlencoded">
              <textarea name="send_data" id="Twitmode_update" rows="2" cols="40" style="width:100%" onkeydown="return checkTwitModeRemaining(this)"></textarea>
              <span id="twitme_TwitModemessage_remaining">
              <?=__('140 chars remaining', TWITME_TRANSLATION_DOMAIN)?>
              </span><br />
              <a href="http://www.twitter.com/<?=TWITME_USER?>" target="_blank">
              <?=__("Your Twitter page",TWITME_TRANSLATION_DOMAIN)?>
              </a>
              <input type="submit" value="<?=__("Send update",TWITME_TRANSLATION_DOMAIN)?>"  style="float: right"  class="button" />
              <input type="button" onclick="loadTwitmode()" value="<?=__("Refresh list",TWITME_TRANSLATION_DOMAIN)?>"  style="float: right"  class="button" />
              <input type="hidden" name="cmd" value="submit_post" />
            </form>
            <br />
            <br />
            <br />
            <!-- TWITMODE -->
            <div id="twittermode"> <img src="<?php echo TWITME_PATH;?>/images/loading.gif" height="19" /><br />
              <span>
              <?=__('Loading Twitmode please wait...', TWITME_TRANSLATION_DOMAIN)?>
              </span><br />
              <br />
            </div>
            <!-- EOF TWITMODE -->
            <b><?=__("What are these icons i see ?.", TWITME_TRANSLATION_DOMAIN)?></b><br>
            <p><?=__('Since version 1.6 of the Twitme plugin i have added some new features and icons that bring Twitter closer to you.', TWITME_TRANSLATION_DOMAIN)?><br>
            </p>
            <ol>
              <li><img src="<?php echo TWITME_PATH;?>/images/trash.png" title="<?=__('Delete a message', TWITME_TRANSLATION_DOMAIN)?>" />&nbsp;<?=__('This icon shows up next to your messages. By pressing this icon you can delete that message.', TWITME_TRANSLATION_DOMAIN)?></li>
              <li><img src="<?php echo TWITME_PATH;?>/images/reply.png" title="<?=__('Send a message', TWITME_TRANSLATION_DOMAIN)?>" />&nbsp;<?=__('This icon can be used to reply quickly to some one`s message.', TWITME_TRANSLATION_DOMAIN)?></li>
              <li><img src="<?php echo TWITME_PATH;?>/images/icon_star_empty.gif"  title="<?=__('Add to Favorites icon', TWITME_TRANSLATION_DOMAIN)?>" />&nbsp;<?=__('This icon indicates that you can add this message to your Favorites', TWITME_TRANSLATION_DOMAIN)?></li>
              <li><img src="<?php echo TWITME_PATH;?>/images/icon_star_full.gif"  title="<?=__('This message is marked as Favorite', TWITME_TRANSLATION_DOMAIN)?>" />&nbsp;<?=__('This icon indicates that this message is on your Favorites list', TWITME_TRANSLATION_DOMAIN)?></li>
              <li><img src="<?php echo TWITME_PATH;?>/images/icon_direct_reply.gif"  title="<?=__('Reply directly to this message reply icon', TWITME_TRANSLATION_DOMAIN)?>" />&nbsp;<?=__('Press this icon to reply directly to a incomming message', TWITME_TRANSLATION_DOMAIN)?></li>
            </ol>
          </div>
          
          <!-- Favorites tab -->
          <div id="tab-3"> 
           <br />
            <br />
            <br />
            <br />
            <div id="favorites"> <img src="<?php echo TWITME_PATH;?>/images/loading.gif" height="19" /><br />
              <span><?=__('Loading favorites please wait...',TWITME_TRANSLATION_DOMAIN)?></span>
             </div>
           </div>
           <!-- End of Favorites tab -->
           
          <!-- INBOX tab --> 
          <div id="tab-4">
            <?php
			  $sOptions = '';
			  if (!is_array ($aFollowers))
			   $sOptions = '<option value="">'.__('No Followers', TWITME_TRANSLATION_DOMAIN).'</option>';
			  else
			  foreach ($aFollowers as $aFollower) { 
				$sOptions .= '<option value="'.$aFollower->id.'">'.$aFollower->screen_name.'</option>';
			  }
			   
			  $sSelectBox=<<<EOF
			    <select name="followerID" id="twitme_followers_Selectbox">
			     {$sOptions}
			  </select>
EOF;
?>
           
            <form  method="post" action="">
              <?='<span style="line-height: 25px; font-weight: bold">'.sprintf (__('Send %s a Message',TWITME_TRANSLATION_DOMAIN), $sSelectBox).'</span>'?>
              <div>
                <textarea onkeydown="return checkRemainingFollowerTabMessage(this)" cols="43" rows="2" name="send_data" style="width: 100%;" ></textarea>
                <span style="line-height: 30px;" id="twitme_followertabmessage_remaining">140 chars remaining</span><br/>
                <input type="submit" style="float: right;" class="button" value="Send Message"/>
                <input type="hidden" value="followers_message" name="cmd"/>
              </div>
            </form>
           
            <br />
            <br />
            <br />
            
            <div id="twitme_incomming"> </div>
          </div>
          <!-- End of INBOX tab -->
          
          
          <!-- OUTBOX -->
          <div id="tab-5"> <br>
            <br>
            <br>
            <br>
            <br>
            <div id="twitme_outgoing"> </div>
          </div>
          
          
          <!-- EOF OUTBOX -->
          <?php if (TWITME_GOOGLE_KEY <> '') 
		  {
			   ?>
			  <div id="tab-6"> 
				<div id="map" style="width: 779px; height: 220px;"></div>
			  </div>
			  <?
		  }
	      ?>



          <!-- ABout TAB -->
          <div id="tab-7">
          <br>
            <br>
            <div style="text-align:center">
              <img src="<?php echo TWITME_PATH;?>/images/logo.png" /> <br>
              <?=sprintf(__("Twitme version %s running on Wordpress %s", TWITME_TRANSLATION_DOMAIN), TWITME_VERSION, TWITME_WP_REAL_VERSION);?>
              <br>
              <br>
              <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="222195">
				<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="" title="<?=__("Donate to Twitme",TWITME_TRANSLATION_DOMAIN)?>">
				<img alt="" border="0" src="https://www.paypal.com/nl_NL/i/scr/pixel.gif" width="1" height="1">
			  </form>
			  <br />
			  <br />
              <b><?=__('Development',TWITME_TRANSLATION_DOMAIN)?></b><br>
              Johnny Mast<br>
              <br>
              <br>
              <b><?=__('Design',TWITME_TRANSLATION_DOMAIN)?></b><br>
              TwItCh<br>
              <br>
              <br>
              <b><?=__('Translation',TWITME_TRANSLATION_DOMAIN)?></b><br>
              Johnny Mast  - <?=__('Dutch',TWITME_TRANSLATION_DOMAIN)?><br>
              Andre Kemena - <?=__('German',TWITME_TRANSLATION_DOMAIN)?><br>
              Sasa         - <?=__('Italian',TWITME_TRANSLATION_DOMAIN)?><br>
              nv1962       - <?=__('English',TWITME_TRANSLATION_DOMAIN)?><br>
              nv1962       - <?=__('Spanish',TWITME_TRANSLATION_DOMAIN)?><br>
              <br>
              <br>
              <b><?=__('Bug Reports and ideas',TWITME_TRANSLATION_DOMAIN)?></b><br>
              nv1962<br>
              miketw<br>
              Robert<br>
              cvc505<br>
            </div>
          </div>
          <!-- EOF TAB-7 ABOUT -->
          
          
          
        </div>
        
        
        
       </td>
    </tr>
  </table>

<div style="display: none; height: 30px;" id="twitme_loading">
  <span><img src="<?php echo TWITME_PATH;?>/images/loading.gif"  />&nbsp;<?=__('Loading please wait...',TWITME_TRANSLATION_DOMAIN);?></span>
  <br /><br />
</div>
<?php
   if (TWITME_WP_VERSION >= 2.7)
     echo '</div>';
?>





<div id="message_followers" style="visibility:hidden; width: 490px;">
 <strong><?=__("Message:",TWITME_TRANSLATION_DOMAIN)?></strong>
 
  <form action="" method="post" unsubmit="return false">
    <div style="width: 374px;">
      <textarea name="send_data" rows="2" cols="43"  onkeydown="return checkRemainingFollowerMessage(this)"></textarea><br />
      <span id="twitme_followermessage_remaining" style="line-height: 30px;"><?=__('140 chars remaining', TWITME_TRANSLATION_DOMAIN)?></span>
      
      <br />
      <input type="submit" value="<?=__("Send Message",TWITME_TRANSLATION_DOMAIN)?>"class="button" style="float:right" />
      <input type="hidden" name="followerID" id="followerID" value="" />
      <input type="hidden" name="cmd" value="followers_message"  />
      <small>
        <?=__("you can close this window by pressing the close icon or press escape on your keyboard.",TWITME_TRANSLATION_DOMAIN)?>
      </small>
     </div>
  </form>
</div>


</span>
<script type="text/javascript">
  $(document).ready(function ()
  { 
	   loadTwitmode();
	   loadFavorites();
	   loadIncomming();
	   loadOutgoing();
		
	   <?php
	   if (sizeof($aFollowers) > 0 && TWITME_GOOGLE_KEY <> '')
	   {
	   	?>var aLocations = new Array();<?
		foreach($aFollowers as $index => $aFollower)
		{
			
			$aFollower ->screen_name = urlencode ($aFollower->screen_name);
			$aFollower ->profile_image_url = urlencode ($aFollower->profile_image_url);
			$aFollower ->location = urlencode ($aFollower->location);
			
			if (!empty ($aFollower ->location)) {
				$aLocationRecord =  array ('name'     => addcslashes ( $aFollower ->screen_name,  '"' ),
										   'image'    => addcslashes ( $aFollower ->profile_image_url,  '"' ),
										   'location' => addcslashes ( $aFollower ->location,  '"')
										   );
				
				echo 'aLocations ['.$index.'] = \''.json_encode ( $aLocationRecord ).'\';';
				echo "\n";
			}
		}
		echo 'load(aLocations);';
	   }
	  ?>
	  
	  
	  $("#twitMode > ul").tabs();
	   $("#doTransfer").click(function() { 
	   	 closeUpdate ($(this).attr ('name'));
	  });

  });
</script>