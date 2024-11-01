/*
** As from version 1.5.1 Twitme entirly changed from the prototype
** framework to the jquery framework. If you Find any javascript bugs 
** then this case could be why.
**
** Johnny Mast
*/

/*
** This function resends a Twitter that you 
** already have send before.
*/
function twitResendPost(iPostID)
{

	$.post ( $('#twit_url').attr ('value') + 'twitOptions.php', { 'postid':iPostID, 'cmd':'resend' },
	  function(data)  {
		  $('#result_content').html ( data );
	  }  
	);

	/* Add a nice highlight effect to the save results */
	$("#result_content").effect("highlight", {}, 1200); 
	return false;		
};



/*
** Test the connection to Twitter.
*/
function twitCheckConnection()
{
	
	var sUserName = encodeURIComponent($('#twit_username').attr('value'));
	var sPassword = encodeURIComponent($('#twit_password').attr('value'));
	
	if (sUserName && sPassword)
	{
	  $.get ( $('#twit_url').attr ('value') + 'twitOptions.php', {'username':sUserName, 'password':sPassword },
	    function(data)  {
		  $('#result_content').html ( data );
	    }  
	  );
	} 
	
	/* Jump to that message */
	document.location.href='#result_content';
	return false;	
}


/* Save the settings and display the "settings saved" message.
*/
function twitSaveSettings()
{
	/*
	** Send over this data back to the dashboard.
	*/
	var aData = {
		'username'         : encodeURIComponent($('#twit_username').attr ('value')),
		'password'         : encodeURIComponent($('#twit_password').attr('value')),
		'message'          : encodeURIComponent($('#postmessage').attr ('value')),
		'notifyfollowers'  : $('#notify_followers_0').attr('checked') ? 'on' : 'off',
		'followersmessage' : encodeURIComponent($('#followers_message').attr('value')),
		'autopost'         : $('#twitme_autosend_0').attr('checked') ? 'on' : 'off',
		'shorturls'        : $('#twitme_shorturls_0').attr('checked') ? 'on' : 'off',
		'twitme_publish_edits' : $('#twitme_publish_edits_0').attr('checked') ? 'yes' : 'no',
		'twitme_google_key' : (typeof $('#twitme_google_key').attr ('value') =='undefined') ? '' : $('#twitme_google_key').attr ('value') ,
		'cmd'              : $('#twit_cmd').attr('value'),
		'method'           : $('#useMethod_0').attr('checked') ? 'template' : 'summary'
	}
	
	
	
	var excludeCats = new String();
	
	$('.twitme_exclude_category').each(function(obj)
	{
		
		if (this.checked) {
		  excludeCats += this.name + ' ';
		}
	});
	
	aData['twitme_exclude_cats'] = excludeCats;
	
	if (aData['username'] && aData['password'])
	{
		
	  $.post ( $('#twit_url').attr ('value') + 'twitOptions.php', aData,
	    function(data)  {
		  $('#result_content').html ( data );
	    }  
	  );
	} 


	/* Jump to that message */
	document.location.href='#result_content';
	return false;
}



/*
** This function sends all messages to Twitter that are not send yet.
*/
function twitSubmitall()
{
	
	$('#twitme_submitall_posts').html ($('<img src="../wp-content/plugins/twitme/images/loading.gif" height="19" /><br /><span>  Sending please wait...</span>'));
	
	$.post ( $('#twit_url').attr ('value') + 'twitOptions.php', { 'cmd': 'submit_all' },
	    function(data)  {
		  $('#twit_resultDiv').html ( data );
		  $('#twitme_submitall_posts').fadeOut ('slow');
		  
		  
		  document.location.href = document.location.href;
	    }  
	);
	
	
			
	/* Add a nice highlight effect to the save results */
	$("#twit_resultDiv").effect("highlight", {}, 700); 
		
	return false;	
}


/*
** Check how many chars there are remaining 
** in the messages to the followers.
*/
function checkRemainingFollowerMessage (obj)
{
  if (!obj) return false;
  var counterBlock = document.getElementById( 'twitme_followermessage_remaining' );
  var iLenght = obj.value.length;
  
  counterBlock.innerHTML = (140 - iLenght) + ' chars remaining';
  
  if (iLenght > 140)
   obj.value = obj.value.substr (0, 140);
}

/*
** Check how many chars there are remaining 
** in the messages to the followers (Followers tab).
*/
function checkRemainingFollowerTabMessage (obj)
{
  var counterBlock = document.getElementById( 'twitme_followertabmessage_remaining' );
  
  if (!obj) return false;
 
  var iLenght = obj.value.length;
  
  counterBlock.innerHTML = (140 - iLenght) + ' chars remaining';
  
  if (iLenght >= 140)
   obj.value = obj.value.substr (0, 140);
}


function checkRemaining(obj)
{
  if (!obj) return false;
  var counterBlock = document.getElementById( 'twitme_message_remaining' );
  var iLenght = obj.value.length;
  
  counterBlock.innerHTML = (140 - iLenght) + ' chars remaining';
  
  if (iLenght > 140)
   obj.value = obj.value.substr (0, 140);
}

/*
** Show the dialog to send a direct message to one of your followers. 
*/   
function showFollowerMessagesDialog (sFollowerID)
{
	$('#message_followers').dialog(		
	{
		modal:true, 
		height: 200,
		width:  400,
		title: 'Contact your followers', 
		overlay: { opacity: 0.5,  background: "black" }
	}).css ('visibility', 'visible');
	
	$('#followerID').attr ('value', sFollowerID);	
}

/*
** Twit mode related functions 
*/
function loadTwitmode(p_iPage)
{
	var pageNo = 1;
	
	if (typeof p_iPage != 'undefined') 
     pageNo = p_iPage;
	 
     
	$('#twittermode').html ($('#twitme_loading').html() );
	
	$.get ( $('#twit_url').attr ('value') + 'twitAjax.php?action=getTwitmode&page=' + pageNo,
	function (data) {
		
	    $('#twittermode').css ('display', 'none');
	     
		$('#twittermode').html (data);
		$('#twittermode').fadeIn  ('quick');
	});	
};



/*
** Incomming directmessages 
*/
function loadIncomming(p_iPage)
{
	var pageNo = 1;
	
	if (p_iPage) 
     pageNo = p_iPage;
	
	$('#twitme_incomming').html ($('#twitme_loading').html() );
	
	$.get ( $('#twit_url').attr ('value') + 'twitAjax.php?action=getDirectMessages&page=' + pageNo,
	function (data) {
		$('#twitme_incomming').html (data);
		$('#twitme_incomming').fadeIn  ('slow');
	});	
}

/*
** Directmessages that where sent to others
*/
function loadOutgoing (p_iPage)
{
	var pageNo = 1;
	
	if (p_iPage) 
     pageNo = p_iPage;
	 
     
	$('#twitme_outgoing').html ($('#twitme_loading').html() );
	
	$.get ( $('#twit_url').attr ('value') + 'twitAjax.php?action=getDirectOutgoingMessages&page=' + pageNo,
	function (data) {
		$('#twitme_outgoing').html (data);
		$('#twitme_outgoing').fadeIn  ('slow');
	});	
}

/* 
** Add as post to favorittes 
*/
function loadFavorites ()
{
	$('#favorites').html ($('#twitme_loading').html() );
	
	$.get ( $('#twit_url').attr ('value') + 'twitAjax.php?action=getFavorites',
	function (data) {
		$('#favorites').html (data).fadeIn ('quick');
	});
}




// ---------- SPECIAL FUNCTION 

/* 
** Mark a post as favorite 
*/
function markFavorite (iPostID)
{	
	$.post ($('#twit_url').attr ('value') + 'twitAjax.php', { 'action' : 'Markfavorite', 'favorite': iPostID }, 
	function (data) {	
		loadIncomming();
	    loadTwitmode();
		loadFavorites();			
	});
}

/* 
** Remove Favorite from the Favorite list 
*/
function unMarkFavorite (sFavoriteID, iPostID)
{
	if ($('#'+sFavoriteID))
	 $('#' + sFavoriteID).fadeOut ('quick');
	
	$.post ($('#twit_url').attr ('value') + 'twitAjax.php', { 'action' : 'unMarkfavorite', 'favorite': iPostID }, 
	function (data) {
		loadTwitmode();
		loadFavorites();			
	});	
}


/* 
** Remove Favorite from the Favorite list 
*/
function closeUpdate (iUpdateID)
{
	$.post ($('#twit_url').attr ('value') + 'twitAjax.php', { 'action' : 'closeUpdate', 'update': iUpdateID }, 
	function (data) {
	
	    $("#twitme_statusUpdate").toggle("blind", { 
	        direction: "vertical" 
	    }, 
	    800); 
	});	
}


function checkTwitModeRemaining(obj)
{
  if (!obj) return false;
  var counterBlock = document.getElementById( 'twitme_TwitModemessage_remaining' );

  var iLenght = obj.value.length;
  
  counterBlock.innerHTML = (140 - iLenght) + ' chars remaining';
  
  if (iLenght >= 140)
    obj.value = obj.value.substr (0, 140);
}


/*
** Delete a follower 
*/
function deleteFollower (followerID)
{
	if (confirm ('Are you sure you want to delete this Follower ?'))
	{
		document.getElementById('follower_Id').value = followerID;
		document.frmFollowersList.submit();
	}
}


function encodeUpdateMessage () {
 var sValue =  encodeURIComponent ($('#send_data_update').attr ('value'));
  $('#send_data_update	').attr ('value',sValue);
  return true;
}


/*
** Send a direct message to one of your followers.
*/ 
function makeReply (sTo) {
	
	var aOptions = $('#twitme_followers_Selectbox > option');
	aOptions.each (function (key, val) {
		if (val.value == sTo)
		{
			val.selected = true;
			$('#twitMode > ul').tabs('select', 2);
		}
	});
}


/*
** Go to the Timeline tab 
** And send an at (@) message to a follower.
*/ 
function makeAtReply (sTo)
{
	if (sTo) $('#Twitmode_update').attr ('value', '@' + sTo+ ' ');
	$('#twitMode > ul').tabs('select', 0);
	
	document.location.href=document.location.href + '#Twitmode_update'
}



/*
** The Inbox tab has been clicked this means the user now knows that 
** a new PM is awaiting in the inbox. So now we can disable the notification bubble.
*/
function resetInboxNotification()
{
	$.get ( $('#twit_url').attr ('value') + 'twitAjax.php?action=resetInboxNotification');		
}


/*
** Message related functions
*/
function deleteMessage (iPostID)
{
	if (confirm ('Are you sure you want to delete this message ?'))
	{

		$.post ($('#twit_url').attr ('value') + 'twitAjax.php', { 'action' : 'deleteMessage', 'messageID': iPostID }, 
		function (data) {
			loadTwitmode();
			loadFavorites();			
		});	
	}
}


/*
** Message related functions
*/
function deleteDirectMessage (iPostID)
{
	if (confirm ('Are you sure you want to delete this direct message ?'))
	{

		$.post ($('#twit_url').attr ('value') + 'twitAjax.php', { 'action' : 'deleteDirectMessage', 'messageID': iPostID }, 
		function (data) {
			loadIncomming();
			loadOutgoing();			
		});	
	}
}
