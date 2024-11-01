    var map = null;
    var geocoder = null;
    var markers  = new Array();
    var point    = null;
    
	function addMarker (oData)
    {
        if (typeof oData != 'undefined')
        {
            if (geocoder) 
            {
                geocoder.getLatLng(oData.location, function(point)  {
         
                   if (point)
                   {
	                   var marker = new GMarker(point);
	                   oData.location = unescape (oData.location);
	                   oData.image    = unescape (oData.image);
	                   oData.name     = unescape (oData.name);
	                   
	                   while (oData.location.lastIndexOf ("+") > -1)
	                    oData.location = oData.location.replace ('+',' ')
	                  
	                   while (oData.image.lastIndexOf ("+") > -1)
	                     oData.image = oData.image.replace ('+',' ')
	                     
	                   while (oData.name.lastIndexOf ("+") > -1)
	                     oData.name = oData.name.replace ('+',' ')
	                   
	                   GEvent.addListener(marker, "click", function() {
	  					  marker.openInfoWindowHtml('<img src="' + oData.image + '" / style="float: left;"><span id="content_holder">&nbsp;'+oData.name+'<br />&nbsp;' + oData.location +'</span>');
	  				   });
	                
	                   map.addOverlay(marker);
                   }
                });
            }   
        } 
    }
    	
	
    function load(oMarkers) 
	{
      if (GBrowserIsCompatible()) 
	  {
	    
		 map = new GMap2(document.getElementById("map"));
		 map.addControl(new GSmallMapControl());
		 map.addControl(new GMapTypeControl());
	     map.setCenter(new GLatLng(0,0),1);
		 geocoder = new GClientGeocoder();

        /*
        ** Create an object for every playing card there 
        ** is to play with.
        */
        jQuery.each(oMarkers,  function (indexNumber, oLocation)
        {
        	 addMarker ( $.evalJSON(oLocation));
        });
      }
    }
	   
