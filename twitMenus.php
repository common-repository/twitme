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

/* Add the action to add new menu options */
add_action('admin_menu', 'twitme_add_pages');

/**
 * Add the management pages to the Wordpress dashboard.
 *
 */
function twitme_add_pages() {

	/*
	** Under settings we will add the "TwitMe Options" 
	*/
    if (TWITME_WP_VERSION == '2.6')
    {
    	
	     /*
		** Under settings we will add the "TwitMe Options" 
		*/
	    add_options_page('TwitMe Settings', __('TwitMe Options',TWITME_TRANSLATION_DOMAIN), 8, 'twitmeoptions', 'twiteme_load_settings');
	
	 	/*
		** And under manage a option to Manage posts send to twitter.
		*/
	    add_management_page(__('Manage your Twitter Community (Twitme`s)',TWITME_TRANSLATION_DOMAIN), __('TwitsMe`s',TWITME_TRANSLATION_DOMAIN), 8, 'twitmemanage', 'twiteme_manage_twits');
   	
    
    } else {

    	/* 
    	** These are a litle Tweaks for Wordpress 2.7 where we have to add Twitme 
    	** to the vertical left menu.
    	*/
	    add_menu_page(__('Twitme`s',TWITME_TRANSLATION_DOMAIN), 'Twitme ', 8, __FILE__, 'getPage');
		add_submenu_page(__FILE__, __('Twitme Settings',TWITME_TRANSLATION_DOMAIN), 'Settings', 8, 'settings', 'getPage');
    }
     

}

/**
 * This function will be called if a page has been choosen.
 *
 */
function getPage ()
{
	switch ($_GET['page'])
	{
		case 'settings':
		 require_once TWITME_PATH.'pages/settings.php';
		break;
		
		case 'Twitme`s':
		  require_once TWITME_PATH.'pages/manage.php';
		break;
		
		default:
		  require_once TWITME_PATH.'pages/manage.php';
		break;
	}
}


/**
 * This function loads the settings page for wordpress 2.6.x
 *
 */
function twiteme_load_settings()
{
	require_once TWITME_PATH.'pages/settings.php';
}

/**
 * This function loads te Twitme`s page for wordpress 2.6.x
 *
 */
function twiteme_manage_twits()
{
    require_once TWITME_PATH.'pages/manage.php';
}

?>
