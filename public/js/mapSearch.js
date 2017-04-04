// This example displays a marker at the center of Australia.
// When the user clicks the marker, an info window opens.

function initMap() {
  var positions = [{lat: -25.363, lng: 131.044},{lat: -25.373, lng: 131.044},{lat: -25.363, lng: 131.054}];

  var map = new google.maps.Map(document.getElementById('map'), {
    zoom: 14,
    center: positions[0],
    maptype: google.maps.MapTypeId.ROADMAP
  });


  var infowindow = new google.maps.InfoWindow({
    maxwidth: 300
  });

  /* this part of the code is from http://en.marnoto.com/2014/09/5-formas-de-personalizar-infowindow.html */
  /*
  * The google.maps.event.addListener() event waits for
  * the creation of the infowindow HTML structure 'domready'
  * and before the opening of the infowindow defined styles
  * are applied.
  */
  infowindow.addListener('domready', function() {

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

    // Reference to the div that groups the close button elements.
    var iwCloseBtn = iwOuter.next();
    // Apply the desired effect to the close button
    iwCloseBtn.css({opacity: '1', right: '55px', top: '20px','box-shadow': '0', width: '25px', height: '25px'});

    // Change the default close-icon
    var iwCloseImg = iwCloseBtn.children(':nth-child(1)').attr('src','icons/close.png');
    iwCloseImg.css({width: '100%', height : '100%', position: 'relative', top:'0',left:'0'})
  });

  var markerIcon = {
    url: 'icons/placePen.png',
    //The size image file.
    scaledSize: new google.maps.Size(30, 30),
    //The point on the image to measure the anchor from. 0, 0 is the top left.
    origin: new google.maps.Point(0, 0),
    //The x y coordinates of the anchor point on the marker. e.g. If your map marker was a drawing pin then the anchor would be the tip of the pin.
    anchor: new google.maps.Point(15, 30)
  };

  for (var i = 0; i < 3; i++) {
    var contentString = '<div class="iw-container">'+
                          '<div class="iw-header">'+
                            '<img class="user-image" src="flowery.jpg"/>' +
                            '<div class="user-details">'+
                              '<a class="user-name" href="#">'+
                                'Mark Smith' +
                              '</a>'+
                              '<span class="is-offering">'+
                                ' is offering'+
                              '</span>'+
                            '</div>'+
                          '</div>' +
                          '<div class="item-image" style="background-image: url(food.jpg)"></div>'+
                          '<div class="iw-body caption">'+
                            '<div class="item-name">APPLES</div>'+
                            '<div class="trans-info">'+
                              '<div class="added-date">Added on 12 March 2018</div>'+
                              '<div><span>Quantity:</span>5</div>'+
                            '</div>'+
                            '<div class="nav-btns">'+
                              '<a href="#" class="btn btn-primary" role="button">View</a>'+
                              '<a class="btn btn-default" role="button">Request</a>'+
                              '<a role="button" class="btn-watch" id="watch"><i class="material-icons">visibility</i></a>'+
                            '</div>'+
                          '</div>'+
                        '</div>';

    var marker = new google.maps.Marker({
      position: positions[i],
      map: map,
      icon: markerIcon
    });

    google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(contentString);
          infowindow.open(map, marker);
        }
      })(marker, i));

    // Event that closes the Info Window with a click on the map
    map.addListener('click', function() {
      infowindow.close();
    });
  }
};
