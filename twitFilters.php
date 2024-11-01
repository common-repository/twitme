<?php
/*  Copyright 2008  Johnny Mast  (email : info@phpvrouwen.nl)

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
if (TWITME_AUTOPOST == 'on') {
 add_action ('wp_insert_post', 'twitme_notify_twitter', 1,2);
 add_action ('publish_future_post', 'tiwtme_proccess_scheduled_post',10,1);
}

function tiwtme_proccess_scheduled_post($postID)
{
	$post = get_post($postID);
	twitme_notify_twitter($postID, $post, true);
}

function twitme_notify_twitter ($postID, $aData, $force = false)
{
	$sExclude = unserialize(TWITME_EXCLUDE_CATS);
	$aExclude = explode(' ', $sExclude);
	
		
	foreach($aExclude as $key => $val)
	{
		if ($val > 0) {
		   if (in_category($val, $aData->ID))
			return false;
		}
	}
	
	/*
	** New since version 1.9.6.3
	*/
	if ($aData->post_type == 'page') 
	 return false;

	if (!empty($aData->post_password))
	 return false;
	 
	
	if ($aData->post_status == 'draft' || $aData->post_status == 'inherit')
	 return false;
	
	if (TWITME_SEND_POST_UPDATE == 'no') {
		
		if ($aData->post_status != 'publish' && $force==false)
		  return false;

		if ($aData->post_date != $aData->post_modified && $force == false)
		  return false; /* This post was published already */
	}
	
	if ($aData) 
	{
		$aData ->post_content =  strip_tags ($aData ->post_content);
		
		if (TWITME_HAVEUSER)
		{
			$oInstance = new twitclass();
	
			switch (TWITME_METHOD)
			{
				case 'template':
				 $sMessage  = TWITME_POSTMESSAGE;
				 $sBlogname = get_bloginfo(false);
				 $sTitle    = 'New Blog post "'.$aData->post_title.'" ';
				 $sBlogUrl  = get_bloginfo ( 'wpurl' );
				 
				 if (TWITME_SHORTURLS == 'on')
				   $sUrl  	= get_short_link (get_permalink($aData->ID));
				 else
				   $sUrl    = get_permalink($aData->ID);
				 
			 
				 $sMessage  = str_replace ('%BLOGURL%',   $sBlogUrl, $sMessage);			 	 
				 $sMessage  = str_replace ('%BLOGNAME%',  $sBlogname, $sMessage);
				 $sMessage  = str_replace ('%POSTURL%',   $sUrl, $sMessage);
				 $sMessage  = str_replace ('%POSTTITLE%', $aData->post_title, $sMessage);
				 
			     if (strlen ($sMessage) > TWITME_MAXLENGTH)
				 {
					 $iOldLength = strlen ($sMessage); 
					 $sMessage   = substr ($sMessage, 0, TWITME_SHORTEDTO);
					 $sTitle     =  sprintf (__(' New Blog post %s%s', TWITME_TRANSLATION_DOMAIN), $aData->post_title, ' ');
                     $sMessage   = $sTitle.' '.$sUrl;
					 
 					 if ($iOldLength > TWITME_SHORTEDTO)
					  $sMessage .= ' ...';					
				 }
				break;
				
				default: /* summary */
				 $sMessage   = $aData->post_content;
				 $iOldLength = strlen ($sMessage); 
				 $sMessage   = substr ($sMessage, 0, TWITME_SHORTEDTO);
				
				 
				 if (TWITME_SHORTURLS == 'on')
				   $sUrl  	= get_short_link (get_permalink($aData->ID));
				 else
				   $sUrl    = get_permalink($aData->ID);				
				
				 $sTitle    = sprintf (__(' New Blog post %s %s', TWITME_TRANSLATION_DOMAIN), $aData->post_title, ' ');
			     $sMessage  .= $sTitle;
				
				 if (strlen ($sMessage) > TWITME_SHORTEDTO)
				 {
				   $sMessage = substr ($sMessage, 0, TWITME_SHORTEDTO-(3 + strlen ($sUrl)));
				   $sMessage .= '... ';
				 }
				 
				 $sMessage .= ' '.$sUrl;
				break;
			}
			
			$aPostsSubmitted = unserialize (TWITME_SUBMITTED);
			if (!is_array ($aPostsSubmitted)) $aPostsSubmitted = array();
			
			if (TWITME_RELEASE)
			{
			   if (!$force)
			   {
				 /*
				 ** This ifixes the bug prior to version 1.4 where 
				 ** some posts where double posted to Twitter.
				 */ 
				 if (!in_array ($postID, $aPostsSubmitted))
			        $oInstance ->sendTimelinePost ($sMessage);
			   } else
			   $oInstance ->sendTimelinePost ($sMessage);
			 
		//	 twitme_notify_followers ();
			}
			
			/*
			** Safe the postes submitted to Twitter for later use in the Manage section
			*/
			array_push ($aPostsSubmitted, $postID);
			update_option ('twitme_submitted', serialize($aPostsSubmitted));
		}
	}
}


function twitme_notify_followers()
{
	
	if (TWITME_NEWFOLLOWER_NOTIFY == 'off') 
	 return false;
	 
	
	/* Create a bridge between twitter and us and request the followers*/
	$oInstance  = new twitclass();
	$aFollowers = $oInstance->getFollowers();
	
	$aNotifiedFollowers = unserialize (TWITME_NOTIFIED_FOLLOWERS);
	$aNotifyThese       = array();
	
	$aNotifiedFollowers = ($aNotifiedFollowers == FALSE) ?  array() : $aNotifiedFollowers;
	
	if (sizeof ($aFollowers) > 0 && is_array ($aNotifiedFollowers))
	{
		foreach ($aFollowers as $aFollower)
		{
			if (!in_array ($aFollower->id,  $aNotifiedFollowers))
			  $aNotifyThese [] = $aFollower->id;
		}
	}

	if (!empty ($aNotifyThese) > 0)
	{
		foreach ($aNotifyThese as $iFollower)
		{
			$oInstance->sendDirectMessage ($iFollower, TWITME_NEWFOLLOWER_MESSAGE);
			$aNotifiedFollowers [] = $iFollower;
		}
	}
	update_option ('twitme_notified_followers', serialize($aNotifiedFollowers));
}
?>