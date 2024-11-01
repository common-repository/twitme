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

	/*
	** This class is written with php4 compatabilty because im a pro webdev my self
	** i know from experiance that 7 out of 10 servers still run php4 so lets respect that.
	*/
	class twitclass 
	{
		var $SafeMode     = false;
		var $CurlHandler = null;
		var $bHaveCurl    = false;
		var $sUsername    = '';
		var $sPassword    = '';
		var $sTwitterUrL  = 'http://www.twitter.com/';
		var $sHeaders     = null;
		var $sUserAgent   = null;
		var $aFollowers   = array();
		
		function twitclass()
		{
			$this->SafeMode  = false;
			$this->bHaveCurl = false;

		    /////////////// Hint from the writer of twitterPHP 
		    //
		    // I don't know if these headers have become standards yet
		    // but I would suggest using them.
		    // more discussion here.
		    // http://tinyurl.com/3xtx66
		    //
		    ///////////////
		    $this->sHeaders=array('X-Twitter-Client:  Twitme for wordpress',
		                          'X-Twitter-Client-Version: '.TWITME_VERSION,
		                          'X-Twitter-Client-URL: '.TWITME_PLUGINURL.'plugindata.xml');
	
			$this->sUserAgent = TWITME_USERAGENT;
			
			if (function_exists ('curl_init')) $this->bHaveCurl = true;
			
			if ($this->bHaveCurl)
			{
				if ($this->SafeMode == false)
				{
					$this->curlinit();
				} else
				echo 'safe mode is on :(';
			} else
			die ('Curl was not found');
			
			if (TWITME_HAVEUSER)
			{
			 $this->setLoginInfo (TWITME_USER, TWITME_PASSWORD);
			}
		}
		
		
	   /**
		* curlinit function
		* Initialize curl.
		*
		* @return void
		* @author Johnny Mast
		**/
		function curlinit()
		{
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
		* sendCurlData function
		* Send data to twitter using curl.
		*
		* @return array
		* @author Johnny Mast
		**/			
		function sendCurlData ($Url, $PostData="", $forceget=false)
		{
			$sPostData = $this->create_postData ($PostData);

			if (!empty ($sPostData) && $forceget==false) {
			 curl_setopt($this->CurlHandler, CURLOPT_POST , 1);
			 curl_setopt($this->CurlHandler, CURLOPT_POSTFIELDS, $sPostData);
			} else
			curl_setopt($this->CurlHandler, CURLOPT_POST, false);
			
			
			curl_setopt($this->CurlHandler, CURLOPT_USERPWD, $this->sUsername.':'.$this->sPassword);						
			curl_setopt($this->CurlHandler, CURLOPT_URL,$Url);

			curl_setopt($this->CurlHandler, CURLOPT_VERBOSE, 1);
	        curl_setopt($this->CurlHandler, CURLOPT_HEADER, 0);
			curl_setopt($this->CurlHandler, CURLOPT_RETURNTRANSFER, 1);
		
	        curl_setopt($this->CurlHandler, CURLOPT_HTTPHEADER, $this->sHeaders); 
			curl_setopt($this->CurlHandler, CURLOPT_USERAGENT, $this->sUserAgent); 

			$sResponse     = curl_exec($this->CurlHandler);
			$iResponseCode = (int)curl_getinfo($this->CurlHandler, CURLINFO_HTTP_CODE);
			
			return array ($sResponse, $iResponseCode);
		}
		
	   /**
		* login function
		* Login to twitter.
		*
		* @return Boolean
		* @author Johnny Mast
		*/	
		function login ($username, $password)
		{
			$this->setLoginInfo ($username, $password);


			if (!empty ($this->sUsername) && !empty ($this->sPassword))
			{
				if ($this->SafeMode)
				{
					die ('savemode is on hack around it !!');
				} else
				{
					if ($this->bHaveCurl)
					{
						$message = '';
						list ($sData, $iReturnCode) = $this->sendCurlData ($this->sTwitterUrL.'/account/verify_credentials.json');
						
						return $iReturnCode == 200;	
					} else
					die ('use something else as curl');
				}
			} else
			die ('error: No login information pressent');	
		}

		
	   /**
		* setLoginInfo function
		* Set login information for the connection.
		*
		* @return Boolean
		* @author Johnny Mast
		**/
		function setLoginInfo($username="", $password="")
		{
			$this->sUsername = $username;
			$this->sPassword = $password;
		}



		/*
		** Get the followers
		*/
		function getFollowers($sPage = 1)
		{
			if (!function_exists('user_sort'))
			{
				function user_sort($a, $b) 
				{
				  
				  //print_rn($b);
				   //echo 'a == '.$a->screen_name. 'b == '.$b->screen_name."\n";
				   $a = $a->screen_name;
				   $b = $b->screen_name;
				   
				   if ($a == $b) return 0;
				   return strcmp(strtolower($a),strtolower($b));
				}
			}
			
			if (!empty ($this->aFollowers))
			 return $this->aFollowers;
			 
			if (!empty ($this->sUsername) && !empty ($this->sPassword))
			{
				if ($this->SafeMode)
				{
					die ('savemode is on hack around it !!');
				} else
				{
					if ($this->bHaveCurl)
					{
						list ($sData, $iReturnCode) = $this->sendCurlData ($this->sTwitterUrL.'/statuses/followers.json', array('page' => $sPage), true);
						if ($iReturnCode == 200)
						{
							$aUser            = $this->getUserInformation();
							$this->aFollowers = json_decode ($sData);
						
							usort ($this->aFollowers, 'user_sort');
							
							if ($aUser)
							  $this->aFollowers['follower_count'] = $aUser->followers_count;
							else
							 $this->aFollowers['follower_count'] = count($this->aFollowers);
							
							return $this->aFollowers;
						} else
						return null;
					} else
					die ('use something else as curl');
				}
			} else
			die ('error: No login information pressent');
		}


		function allowFollower($p_iFollowerId)
		{
			if (!empty ($this->sUsername) && !empty ($this->sPassword))
			{
				if ($this->SafeMode)
				{
					die ('savemode is on hack around it !!');
				} else
				{
					if ($this->bHaveCurl)
					{
						$message = '';
						list ($sData, $iReturnCode) = $this->sendCurlData ($this->sTwitterUrL.'/blocks/destroy/'.$p_iFollowerId.'.json', array('id' => $p_iFollowerId));
						if ($iReturnCode == 200)
						{
							return json_decode ($sData);
						} else
						return null;
					} else
					die ('use something else as curl');
				}
			} else
			die ('error: No login information pressent');
		}

		function deleteFollower($p_iFollowerId)
		{
			if (!empty ($this->sUsername) && !empty ($this->sPassword))
			{
				if ($this->SafeMode)
				{
					die ('savemode is on hack around it !!');
				} else
				{
					if ($this->bHaveCurl)
					{
						$message = '';
						list ($sData, $iReturnCode) = $this->sendCurlData ($this->sTwitterUrL.'/blocks/create/'.$p_iFollowerId.'.json', array('id' => $p_iFollowerId));
						if ($iReturnCode == 200)
						{
							return json_decode ($sData);
						} else
						return null;
					} else
					die ('use something else as curl');
				}
			} else
			die ('error: No login information pressent');
		}
		
		/*
		** Send a direct message to a user.
		*/
		function sendDirectMessage($p_iUserID, $p_sMessage)
		{
			if (!empty ($this->sUsername) && !empty ($this->sPassword))
			{
				if ($this->SafeMode)
				{
					die ('savemode is on hack around it !!');
				} else
				{
					if ($this->bHaveCurl)
					{
						list ($sData, $iReturnCode) = $this->sendCurlData ($this->sTwitterUrL.'/direct_messages/new.xml', array ('user' => $p_iUserID, 'text' => $p_sMessage));
						
						if ($iReturnCode == 200)
						{
							return json_decode ($sData);
						} else
						return null;
					} else
					die ('use something else as curl');
				}
			} else
			die ('error: No login information pressent');
		}

		
		/*
		** Get the public time line
		*/
		function getDirectMessages($p_iCount = 5, $p_iPage = 1) 
		{
			$iCount = $p_iCount;
			 
			if (!empty ($this->sUsername) && !empty ($this->sPassword))
			{
				if ($this->SafeMode)
				{
					die ('savemode is on hack around it !!');
				} else
				{
					if ($this->bHaveCurl)
					{
						list ($sData, $iReturnCode) = $this->sendCurlData ($this->sTwitterUrL.'/direct_messages.json');
						if ($iReturnCode == 200)
						{
							return json_decode ($sData);
						} else
						return null;
					} else
					die ('use something else as curl');
				}
			} else
			die ('error: No login information pressent');		
		}

		function getDirectOutgoingMessages($p_iCount = 5, $p_iPage = 1) 
		{
			$iCount = $p_iCount;
			 
			if (!empty ($this->sUsername) && !empty ($this->sPassword))
			{
				if ($this->SafeMode)
				{
					die ('savemode is on hack around it !!');
				} else
				{
					if ($this->bHaveCurl)
					{
						list ($sData, $iReturnCode) = $this->sendCurlData ($this->sTwitterUrL.'/direct_messages/sent.json');
						if ($iReturnCode == 200)
						{
							return json_decode ($sData);
						} else
						return null;
					} else
					die ('use something else as curl');
				}
			} else
			die ('error: No login information pressent');		
		}

		function deleteDirectmessage($p_iMessageID) 
		{
			if (!empty ($this->sUsername) && !empty ($this->sPassword))
			{
				if ($this->SafeMode)
				{
					die ('savemode is on hack around it !!');
				} else
				{
					if ($this->bHaveCurl)
					{
						list ($sData, $iReturnCode) = $this->sendCurlData ($this->sTwitterUrL.'/direct_messages/destroy/'.$p_iMessageID.'.json', array ('trash' => ''));
						if ($iReturnCode == 200)
						{
							return json_decode ($sData);
						} else
						return null;
					} else
					die ('use something else as curl');
				}
			} else
			die ('error: No login information pressent');		
		}
		
		
		function getUserInformation()
		{
			if (!empty ($this->aFollowers))
			 return $this->aFollowers;
			 
			if (!empty ($this->sUsername) && !empty ($this->sPassword))
			{
				if ($this->SafeMode)
				{
					die ('savemode is on hack around it !!');
				} else
				{
					if ($this->bHaveCurl)
					{
						list ($sData, $iReturnCode) = $this->sendCurlData ($this->sTwitterUrL.'/users/show/'.$this->sUsername.'.json');
						if ($iReturnCode == 200)
						{
							$this->aFollowers =  json_decode ($sData);
							return $this->aFollowers;
						} else
						return null;
					} else
					die ('use something else as curl');
				}
			} else
			die ('error: No login information pressent');
		}
		
		/*
		** Get the public time line
		*/
		function getPublicTimeLine($p_iCount = 5, $p_iPage = 0)
		{
			$iCount = $p_iCount;
			 
			if (!empty ($this->sUsername) && !empty ($this->sPassword))
			{
				if ($this->SafeMode)
				{
					die ('savemode is on hack around it !!');
				} else
				{
					if ($this->bHaveCurl)
					{
						list ($sData, $iReturnCode) = $this->sendCurlData ($this->sTwitterUrL.'/statuses/friends_timeline.json', array ('count' => $iCount, 'page' => $p_iPage), true);
						if ($iReturnCode == 200)
						{
							return json_decode ($sData);
						} else
						return null;
					} else
					die ('use something else as curl');
				}
			} else
			die ('error: No login information pressent');
		}


		/*
		** Get the users Favorites
		*/
		function getFavorites($p_iCount = 5)
		{
			$iCount = $p_iCount;
			
			if ($iCount > 20)
			 $iCount = 20;
			 
			if (!empty ($this->sUsername) && !empty ($this->sPassword))
			{
				if ($this->SafeMode)
				{
					die ('savemode is on hack around it !!');
				} else
				{
					if ($this->bHaveCurl)
					{
						list ($sData, $iReturnCode) = $this->sendCurlData ($this->sTwitterUrL.'/favorites.json', array ('count' => $iCount), true);
						if ($iReturnCode == 200)
						{
							return json_decode ($sData);
						} else
						return null;
					} else
					die ('use something else as curl');
				}
			} else
			die ('error: No login information pressent');
		}


		/*
		** Delete a favorite Favorites
		*/
		function destoryFavorite($p_iFavoriteID)
		{
			$iCount = $p_iCount;
			
			if ($iCount > 20)
			 $iCount = 20;
			 
			if (!empty ($this->sUsername) && !empty ($this->sPassword))
			{
				if ($this->SafeMode)
				{
					die ('savemode is on hack around it !!');
				} else
				{
					if ($this->bHaveCurl)
					{
						list ($sData, $iReturnCode) = $this->sendCurlData ($this->sTwitterUrL.'/favorites/destroy/'.$p_iFavoriteID.'.json', array ('count' => $iCount));
						if ($iReturnCode == 200)
						{
							return json_decode ($sData);
						} else
						return null;
					} else
					die ('use something else as curl');
				}
			} else
			die ('error: No login information pressent');
		}


		/*
		** Delete a favorite Favorites
		*/
		function createFavorite($p_iFavoriteID)
		{
			$iCount = $p_iCount;
			
			if ($iCount > 20)
			 $iCount = 20;
			 
			if (!empty ($this->sUsername) && !empty ($this->sPassword))
			{
				if ($this->SafeMode)
				{
					die ('savemode is on hack around it !!');
				} else
				{
					if ($this->bHaveCurl)
					{
						list ($sData, $iReturnCode) = $this->sendCurlData ($this->sTwitterUrL.'/favorites/create/'.$p_iFavoriteID.'.json', array ('count' => $iCount));
						if ($iReturnCode == 200)
						{
							return json_decode ($sData);
						} else
						return null;
					} else
					die ('use something else as curl');
				}
			} else
			die ('error: No login information pressent');
		}

		/*
		** Get Last posts
		*/
		function getLastPosts($p_iCount = 5)
		{
			$iCount = $p_iCount;
			
			if ($iCount > 20)
			 $iCount = 20;
			 
			if (!empty ($this->sUsername) && !empty ($this->sPassword))
			{
				if ($this->SafeMode)
				{
					die ('savemode is on hack around it !!');
				} else
				{
					if ($this->bHaveCurl)
					{
						list ($sData, $iReturnCode) = $this->sendCurlData ($this->sTwitterUrL.'/statuses/user_timeline.json', array ('count' => $iCount));
						if ($iReturnCode == 200)
						{
							return json_decode ($sData);
						} else
						return null;
					} else
					die ('use something else as curl');
				}
			} else
			die ('error: No login information pressent');
		}
		
		/*
		** Add the post to the twitter site
		*/
		function sendTimelinePost($sMessage)
		{
			if (!empty ($this->sUsername) && !empty ($this->sPassword))
			{
				if ($this->SafeMode)
				{
					die ('savemode is on hack around it !!');
				} else
				{
					if ($this->bHaveCurl)
					{
						$aData = $this->sendCurlData ($this->sTwitterUrL.'/statuses/update.json', array ('status' => $sMessage, 'source' => 'twitmeforwordpress'));
						return true;
					} else
					die ('use something else as curl');
				}
			} else
			die ('some error because of no twitter info');
			
		}
		
		/*
		** Delete a post from the public timeline
		*/
		function deleteTimelinePost($iPostID)
		{
			if (!empty ($this->sUsername) && !empty ($this->sPassword))
			{
				if ($this->SafeMode)
				{
					die ('savemode is on hack around it !!');
				} else
				{
					if ($this->bHaveCurl)
					{
						$aData = $this->sendCurlData ($this->sTwitterUrL.'/statuses/destroy/'.$iPostID.'.xml', array ('status' => $sMessage, 'source' => 'twitmeforwordpress'));
						return $aData;
					} else
					die ('use something else as curl');
				}
			} else
			die ('some error because of no twitter info');	
		}
		
		
		function massageAtLinks($p_sText, $p_sSender)
		{
			$sText      = $p_sText;
			$sResult    = $sTmp = $sText;
			
			while (($iPos = strpos($sTmp, '@')) >-1)
			{
				
				
				$sPart   = substr($sTmp, $iPos, strlen($sTmp)); 
				$sPart   = str_replace(',', ' ',$sPart);
				
				$iEndPos = strpos($sPart, ' ');
				
				if ($iEndPos == -1 || $iEndPos == 0)
				 $iEndPos = strlen($sPart);
				  
				$sReplaceThis = substr($sPart, 0, $iEndPos);
				
				
				$sScreenname  = substr($sReplaceThis, 1, strlen($sReplaceThis));
				$sReplaceWith = '@<a href="http://www.twitter.com/'.$sScreenname.'" target="_blank">'.$sScreenname.'</a>';
				
				$sResult = str_replace($sReplaceThis, $sReplaceWith, $sResult);
				$sTmp    = substr($sTmp, $iPos+$iEndPos, strlen($sTmp));
				
			}
			return $sResult;
		}
		
		function massageLinks ($p_sText, $p_sSender)
		{
			$sText = $p_sText;
		
			if (($iStartPos = strpos($sText, 'http://')) > -1 || ($iStartPos = strpos($sText, 'www.')) > -1)
			{
				$sInnerText = substr ($sText, $iStartPos, strlen ($sText));
				$iEndPos    = strpos ($sInnerText, ' ');
				
				if ($iEndPos <= 0) $iEndPos = strlen ($sInnerText);
				
				$sLink = substr ($sInnerText, 0, $iEndPos);
				
				
				$sLinkMarkup = '<a href="'.$sLink.'" target="_blank">'.$sLink.'</a>';
				$sText = str_replace ($sLink, $sLinkMarkup, $sText);
				return $this->massageAtLinks ($sText, $p_sSender);
			}
			return $this->massageAtLinks ($sText, $p_sSender);
		}
		
	}
?>