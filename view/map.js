       // This example displays a marker at the center of Australia.
       // When the user clicks the marker, an info window opens.

       function initMap() {
         var uluru = {lat: -25.363, lng: 131.044};
         var map = new google.maps.Map(document.getElementById('map'), {
           zoom: 8,
           center: uluru,
           maptype: google.maps.MapTypeId.ROADMAP
         });

         var contentString = '<div class="iw-container">'+
         '<div class="iw-header">'+
         '<div class="user">'+
         '<div class="user-img">'+
         '<img src="flowery.jpg"/>' +
         '</div>' +
         '<div class="user-name">'+
         'Mark Smith <span class="is-offering">is offering</span>'+
         '</div>'+
         '</div>'+
         '</div>' + 
         '<div class="iw-body">'+
         '<img src="dryfruits.jpg"/>'+
         '</div>'+
         '</div>';

         var infowindow = new google.maps.InfoWindow({
          content: contentString, maxwidth: 170
        });

         var marker = new google.maps.Marker({
           position: uluru,
           map: map,
         });
         marker.addListener('click', function() {
           infowindow.open(map, marker);
         });

         /* this part of the code is from http://en.marnoto.com/2014/09/5-formas-de-personalizar-infowindow.html */
         /*
  * The google.maps.event.addListener() event waits for
  * the creation of the infowindow HTML structure 'domready'
  * and before the opening of the infowindow defined styles
  * are applied.
  */
  google.maps.event.addListener(infowindow, 'domready', function() {

    // Reference to the DIV which receives the contents of the infowindow using jQuery
    var iwOuter = $('.gm-style-iw');

    /* The DIV we want to change is above the .gm-style-iw DIV.
    * So, we use jQuery and create a iwBackground variable,
     * and took advantage of the existing reference to .gm-style-iw for the previous DIV with .prev().
     */
     var iwBackground = iwOuter.prev();

    // Remove the background shadow DIV
    iwBackground.children(':nth-child(2)').css({'display' : 'none'});

    // Remove the white background DIV
    iwBackground.children(':nth-child(4)').css({'display' : 'none'});

     // Changes the desired tail shadow color.
     iwBackground.children(':nth-child(3)').find('div').children().css({'box-shadow': 'black0px 1px 6px', 'z-index' : '1'});

   });
};