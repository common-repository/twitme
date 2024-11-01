<?php 


/**
** Credits to james of http://james.cridland.net/ he wrote 
** this function. Thanks !
*/
function get_short_link($url) 
{
	$com = new twitclass();
	$api_call = "http://api.bit.ly/shorten?version=2.0.1&longUrl=".$url."&login=".TWITME_BITLY_LOGIN."&apiKey=".TWITME_BITLY_API_KEY;
	
	/* Get the data */
	list($response_data, $response_code) = $com->sendCurlData ($api_call, NULL, true);
	
	if ($response_code != 200) return $url;
	$bitlyinfo=(array)json_decode(utf8_encode($response_data),true);
	
	
	if (defined('TWITME_ALT_JSON')) /* Old php versions without json_encode */
	{
		if (is_array($bitlyinfo))
		{
			if ($bitlyinfo['errorCode']==0) 
			{
			  return $bitlyinfo['results']->$url->shortUrl;
			} else 
			return $url;
		} else
		return $url;
	} else
	{
		if (is_array($bitlyinfo))
		{
			if ($bitlyinfo['errorCode']==0) 
			{
			  return $bitlyinfo['results'][$url]['shortUrl'];
			} else 
			return $url;
		} else
		return $url;		
	}
}

?>