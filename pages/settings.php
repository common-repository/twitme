
<script src="<?php echo TWITME_PATH;?>/scripts/jquery.js" type="text/javascript" defer="true"></script>
<script src="<?php echo TWITME_PATH;?>/scripts/ui/effects.core.js" type="text/javascript"></script>
<script src="<?php echo TWITME_PATH;?>/scripts/ui/effects.highlight.js" type="text/javascript"></script>
<script src="<?php echo TWITME_PATH;?>/scripts/twitAjax.js" type="text/javascript"></script>

<style type="text/css">
<!--
.wrap .form-table tr td {
	font-weight: bold;
}
-->
</style>
<div class="wrap">
  <h2><?=__('TwitMe Options', TWITME_TRANSLATION_DOMAIN)?></h2>
  <p><img style="float: left; padding-right: 10px;" src="<?php echo TWITME_PATH;?>/images/logo.png" alt="Twitter logo" align="top" title="new logo created by Dustin Crisman (TwItCh on irc.freenode.net #css)">
  
  <?=sprintf (__("In order to make the connection between <em>%s</em> and Twitter we need your username and password for so that we can login into Twitter. It is wise to press the <em>test now</em> button so you are sure that your connection to Twitter will work well. Please dont forget to press the save settings button to store your settings. ", TWITME_TRANSLATION_DOMAIN),  get_bloginfo('name'))?><br /><br />
  <small><strong><?=__("Note: ",TWITME_TRANSLATION_DOMAIN)?></strong> 
  <?=__("If the message sent to Twitter is longer then 140 chars (Twitters Max) then the plugin will automaticly switch to
      \"Summary\" modes even if you have \"Template\" modes selected.",TWITME_TRANSLATION_DOMAIN)?>
  <br />
  <br />
 <br /> 
 <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="222195">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="" title="<?=__("Donate to Twitme",TWITME_TRANSLATION_DOMAIN)?>">
<img alt="" border="0" src="https://www.paypal.com/nl_NL/i/scr/pixel.gif" width="1" height="1">
</form><br /><br />
 <div id="result_content" name="result_content" style="background:#ffffff; width: 280px;"></div>
 <br />

 
  <form action="<?php echo TWITME_PATH;?>/twitOptions.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="return twitSaveSettings();">
   <table class="form-table">
      <tr>
        <td align="left" valign="top"><?=__("Twitter Username",TWITME_TRANSLATION_DOMAIN)?></td>
        <td align="left" valign="top"><input type="text" name="username" id="twit_username" value="<?php echo TWITME_USER;?>"></td>
      </tr>
      <tr>
        <td align="left" valign="top"><?=__("Twitter Password",TWITME_TRANSLATION_DOMAIN)?></td>
        <td align="left" valign="top"><input type="password" name="password" id="twit_password" value="<?php echo TWITME_PASSWORD;?>"></td>
      </tr>
      <tr>
        <td width="99" align="left" valign="top"><?=__('Twitter Message', TWITME_TRANSLATION_DOMAIN)?>
        <br />
        <small style="color:#999;">
         <?=__("You can alter this message 
         but leave the template tags in tackt !.", TWITME_TRANSLATION_DOMAIN)?><br />
         <?=__("Allowed Tags:", TWITME_TRANSLATION_DOMAIN)?>
         <ul>
          <li>%BLOGURL%</li>
          <li>%BLOGNAME%</li>
          <li>%POSTTITLE%</li>
          <li>%POSTURL%</li>
          
         </ul>
         </small></td>
        <td align="left" valign="top">
          <textarea id="postmessage" cols="45" rows="5"><?php echo TWITME_POSTMESSAGE;?></textarea>        </td>
      </tr>
      <tr>
        <td align="left" valign="top"><?=__("Exclude Categories", TWITME_TRANSLATION_DOMAIN)?><br />
        <small style="color:#999;">
          <?=__("If you are creating new posts from rss feeds or you just want to exclude any category from being posted to Twitter please mark the related category checkbox(es). For Rss posts if you not use this option it could lead to mass posting to Twitter", TWITME_TRANSLATION_DOMAIN)?>
        
        </small>
        </td>
        <td align="left" valign="top">
        <?php
		  $sExclude = unserialize(TWITME_EXCLUDE_CATS);
		  $aExclude = explode(' ', $sExclude);
		  
		  
          $args  = array ('hide_empty' => false);
		  $aCats = get_categories($args);
		  $i     = 0;
		  if (empty($aCats)) echo __("You did not add any categories yet.", TWITME_TRANSLATION_DOMAIN);
		  else
		   foreach($aCats as $thisCat)
		   {
			  if ($i == TWITME_CATS_PER_ROW)
			   echo '<br />';
			  ?>
               <label>
                 <?=$thisCat->cat_name;?>
                 <?php
				   $sValue = '';
				   if (in_array($thisCat->term_id, array_values($aExclude)))
				    $sValue = 'checked="checked"';
				 ?>
                 &nbsp;<input type="checkbox" class="twitme_exclude_category"  <?php echo $sValue; ?> name="<?=$thisCat->term_id;?>" />
               </label>&nbsp;
              <?php 
			  $i++;
		   }
        ?>
        </td>
      </tr>
      <tr>
        <td align="left" valign="top"><?=__("Google Maps key",TWITME_TRANSLATION_DOMAIN)?>
        <br />
        <small style="color:#999;">
          <?=__("If you fill in a Google MAPS key you can see where on the world your Followers are located (Only if they filled in there location)",TWITME_TRANSLATION_DOMAIN)?>
        </small>
        </td>
        <td align="left" valign="top"><input type="text" id="twitme_google_key" size="40" value="<?=TWITME_GOOGLE_KEY?>" />&nbsp;<a href="http://www.google.com/maps/api_signup?url=<?=get_bloginfo ( 'wpurl' )?>" target="_blank"><?=__("Signup for your key",TWITME_TRANSLATION_DOMAIN)?></a></td>
      </tr>
      <tr>
        <td align="left" valign="top"><strong><?=__('Enable Auto post', TWITME_TRANSLATION_DOMAIN)?></strong><br />
        <small style="color:#999;"><?=__('If you turn this option off Twitme will not send the message automaticly any more (this option is turned on by default)', TWITME_TRANSLATION_DOMAIN)?></small>        </td>
        <td align="left" valign="top">
		<p>
        	<?
				$aSelected = array (
								 (TWITME_AUTOPOST == 'on') ? 'checked="checked"' : '',
								 (TWITME_AUTOPOST == 'off') ? 'checked="checked"' : '');
				
			?>
            <label>
              <input type="radio" id="twitme_autosend_0"  name="twitme_autosend_radio"value="on" <?=$aSelected[0]?> />
            <?=__('On', TWITME_TRANSLATION_DOMAIN)?></label>
            <br />
            <label>
              <input type="radio" id="twitme_autosend_1" name="twitme_autosend_radio" value="off" <?=$aSelected[1]?> />
            <?=__('Off', TWITME_TRANSLATION_DOMAIN)?></label>
            <br />
        </p>        </td>
      </tr>
      <tr>
      <tr>
        <td align="left" valign="top"><strong><?=__('Send on edit post', TWITME_TRANSLATION_DOMAIN)?></strong><br />
        <small style="color:#999;"><?=__('Publish your posts to Twitter if you have edited a post (this option is turned off by default)', TWITME_TRANSLATION_DOMAIN)?></small>        </td>
        <td align="left" valign="top">
		<p>
        	<?
				$aSelected = array (
								 (TWITME_SEND_POST_UPDATE == 'yes') ? 'checked="checked"' : '',
								 (TWITME_SEND_POST_UPDATE == 'no') ? 'checked="checked"' : '');
				
			?>
            <label>
              <input type="radio" id="twitme_publish_edits_0"  name="twitme_publish_edit_radio"value="yes" <?=$aSelected[0]?> />
            <?=__('On', TWITME_TRANSLATION_DOMAIN)?></label>
            <br />
            <label>
              <input type="radio" id="twitme_publish_edits_1" name="twitme_publish_edit_radio" value="no" <?=$aSelected[1]?> />
            <?=__('Off', TWITME_TRANSLATION_DOMAIN)?></label>
            <br />
        </p>        </td>
      </tr>
      <tr
        <td align="left" valign="top"><strong><?=__('Use short urls', TWITME_TRANSLATION_DOMAIN)?></strong><br />
        <small style="color:#999;">
        <?=__('You can enable this option if you want to shorten the urls in the twitter posts. (This option is turned off by default)
        ', TWITME_TRANSLATION_DOMAIN)?></small>
        </td>
        <td align="left" valign="top"><?
				$aSelected = array (
								 (TWITME_SHORTURLS == 'on') ? 'checked="checked"' : '',
								 (TWITME_SHORTURLS == 'off') ? 'checked="checked"' : '');
				
			?>
          <label>
            <input type="radio" id="twitme_shorturls_0"  name="twitme_shorturls_radio"value="on" <?=$aSelected[0]?> />
            <?=__('On', TWITME_TRANSLATION_DOMAIN)?>
          </label>
          <br />
          <label>
            <input type="radio" id="twitme_shorturls_1" name="twitme_shorturls_radio" value="off" <?=$aSelected[1]?> />
            <?=__('Off', TWITME_TRANSLATION_DOMAIN)?>
        </label></td>
      </tr>
      <tr>
        <td width="140" align="left" valign="top"><?=__('Twitter Followers', TWITME_TRANSLATION_DOMAIN)?><br />
          <small style="color:#999;"><?=__("If desired send this messge to new Followers on twitter.", TWITME_TRANSLATION_DOMAIN)?></small></td>
        <td align="left" valign="top"><textarea name="followersmessage" id="followers_message" cols="45" rows="5" ><?php echo TWITME_NEWFOLLOWER_MESSAGE;?></textarea></td>
      </tr>
      <tr>
        <td align="left" valign="top"><?=__("Notify new Followers", TWITME_TRANSLATION_DOMAIN)?></td>
        <td align="left" valign="top"><p>
        	<?
				$aSelected = array (
								 (TWITME_NEWFOLLOWER_NOTIFY == 'on') ? 'checked="checked"' : '',
								 (TWITME_NEWFOLLOWER_NOTIFY == 'off') ? 'checked="checked"' : '');
				
			
			?>
            <label>
             <input type="radio" id="notify_followers_0" name="notify_followers" value="on" <?=$aSelected[0]?> />
             <?=__('Yes', TWITME_TRANSLATION_DOMAIN)?></label>
            <br />
            <label>
              <input type="radio" id="notify_followers_1" name="notify_followers" value="off" <?=$aSelected[1]?> />
            <?=__('No', TWITME_TRANSLATION_DOMAIN)?></label>
            <br />
        </p></td>
</tr>
      <tr>
        <td align="left" valign="top"><?=__("Report method", TWITME_TRANSLATION_DOMAIN)?></td>
        <td align="left" valign="top"><p>
        	<?
				$aSelected = array (
								 (TWITME_METHOD == 'template') ? 'checked="checked"' : '',
								 (TWITME_METHOD == 'summary') ? 'checked="checked"' : '');
				
			?>

          <label>
            <input type="radio" id="useMethod_0" name="groep_1" value="template"  <?=$aSelected[0]?> />
            <?=__("Use message template", TWITME_TRANSLATION_DOMAIN)?></label>
          <br />
          <label>
            <input type="radio" id="useMethod_1" name="groep_1" value="summary" <?=$aSelected[1]?> />
            <?=__("Use Post summary", TWITME_TRANSLATION_DOMAIN)?></label>
          <br />
        </p></td>
      </tr>
      <tr>
        <td align="left" valign="top"><input type="button" id="twit_test" value="<?=__("Test now", TWITME_TRANSLATION_DOMAIN)?>" onclick="return twitCheckConnection()" /></td>
        <td align="left" valign="top"><input type="submit" id="twit_save" value="<?=__("Save settings", TWITME_TRANSLATION_DOMAIN)?>"  onclick="return twitSaveSettings()" /></td>
      </tr>
    </table>
    <input type="hidden" id="twit_method" value="<?php echo TWITME_METHOD;?>" /> 
    <input type="hidden" id="twit_cmd" value="<?php echo (TWITME_HAVEUSER == TRUE) ? 'update' : 'store';?>" /> 
    <input type="hidden" id="twit_url" value="<?php echo TWITME_PATH;?>" />
  </form>
</div>
 
