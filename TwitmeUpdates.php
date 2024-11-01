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

	/*
	** This class is written with php4 compatabilty because im a pro webdev my self
	** i know from experiance that 7 out of 10 servers still run php4 so lets respect that.
	*/
	class TwitmeUpdates
	{
		var $CurlHandler;
		
		function TwitmeUpdates ()
		{
			/*
			** Init the curl library.
			*/
			$this->CurlHandler = curl_init();
		}

		
	   /**
		* create_postData function
		* Returns an array with encoded Post Data variables.
		*
		* @return array
		* @author Johnny Mast
		**/
		function create_postData ($aPostData)
		{
		   $sPostData = '';
		   if (is_array ($aPostData)) {
		   foreach ($aPostData as $k=>$v)
		   {
		       $sPostData.= "$k=".urlencode(stripcslashes($v))."&";
		   }
		      $sPostData=substr($sPostData,0,-1);
		   } else
		   $sPostData = $aPostData;
		   return $sPostData;
		}
		
		/**
		 * An internal function that will send information about your 
		 * blog to the author of this plugin.
		 * 
		 * @return string
		 */
		function getSubmitData()
		{
			$aDataRecord = array (
				'host'       => TWITME_URL,
				'ip'         => isset ($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['HTTP_HOST'],
				'wp_version' => TWITME_WP_REAL_VERSION,
				'twitme_version' => TWITME_VERSION,
				'email'      => get_option ('admin_email')
			);	
			return $this->create_postData( $aDataRecord );
		}


		/**
		 * This function will check with the author of this plugin for updates.
		 * This could be software updates or messages dispatched by the author.
		 *
		 * @return object
		 */

		function checkForUpdates()
		{	
			curl_setopt($this->CurlHandler, CURLOPT_URL,'http://www.phpvrouwen.nl/twitmeupdates.php');
			curl_setopt($this->CurlHandler, CURLOPT_POSTFIELDS, $this->getSubmitData());
			curl_setopt($this->CurlHandler, CURLOPT_VERBOSE, 1);
	        curl_setopt($this->CurlHandler, CURLOPT_NOBODY, 0);
	        curl_setopt($this->CurlHandler, CURLOPT_HEADER, 0);
			curl_setopt($this->CurlHandler, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($this->CurlHandler, CURLOPT_FOLLOWLOCATION,0);
	 		

			
			$sResponse     = curl_exec($this->CurlHandler);
			$iResponseCode = (int)curl_getinfo($this->CurlHandler, CURLINFO_HTTP_CODE);

			if ($iResponseCode != 200) return false;
			return json_decode ( $sResponse );
		}
	}
?>