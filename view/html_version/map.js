// This example displays a marker at the center of Australia.
// When the user clicks the marker, an info window opens.

function initMap() {
  //custom marker
  //adapted from http://gmaps-samples-v3.googlecode.com/svn/trunk/overlayview/custommarker.html
  function CustomMarker(latlng, map, imageSrc) {
    this.latlng_ = latlng;
    this.imageSrc = imageSrc;
    // Once the LatLng and text are set, add the overlay to the map.  This will
    // trigger a call to panes_changed which should in turn call draw.
    this.setMap(map);
  }

  var uluru = {lat: 37.77088429547992, lng: -122.4135623872337};

  var map = new google.maps.Map(document.getElementById('map'), {
    zoom: 8,
    center: uluru,
    maptype: google.maps.MapTypeId.ROADMAP
  });

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
                        '<div class="iw-body caption">'+
                          '<div class="item-name">APPLES</div>'+
                          '<div class="added-date">Added on 12 March 2018</div>'+
                          '<div class="nav-btns">'+
                            '<a href="#" class="btn btn-primary" role="button">View</a>'+
                            '<a class="btn btn-default" role="button">Request</a>'+
                            '<a role="button" class="btn-watch" id="watch"><i class="material-icons">visibility</i></a>'+
                          '</div>'+
                        '</div>'+
                      '</div>';

  var infowindow = new google.maps.InfoWindow({
    content: contentString, maxwidth: 300
  });

  CustomMarker.prototype = new google.maps.OverlayView();

  CustomMarker.prototype.draw = function () {
    // Check if the div has been created.
    var div = this.div_;
    if (!div) {
      // Create a overlay text DIV
      div = this.div_ = document.createElement('div');
      // Create the DIV representing our CustomMarker
      div.className = "customMarker"

      var img = document.createElement("img");
      img.src = this.imageSrc;
      div.appendChild(img);

      google.maps.event.addDomListener(div, "click", function (event) {
            console.log(event.latLng)
      });

      // Then add the overlay to the DOM
      var panes = this.getPanes();
      panes.overlayImage.appendChild(div);
    }

    // Position the overlay
    var point = this.getProjection().fromLatLngToDivPixel(this.latlng_);
    if (point) {
      div.style.left = point.x + 'px';
      div.style.top = point.y + 'px';
    }
  };

  CustomMarker.prototype.remove = function () {
    // Check if the overlay was on the map and needs to be removed.
    if (this.div_) {
      this.div_.parentNode.removeChild(this.div_);
      this.div_ = null;
    }
  };

  CustomMarker.prototype.getPosition = function () {
    return this.latlng_;
  };

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

  // Event that closes the Info Window with a click on the map
  map.addListener('click', function() {
    infowindow.close();
  });

  var data = [{
    profileImage: "http://www.gravatar.com/avatar/d735414fa8687e8874783702f6c96fa6?s=90&d=identicon&r=PG",
    pos: [37.77085, -122.41356]
  }, {
    profileImage: "http://placekitten.com/90/90",
    pos: [37.77220, -122.41555]
  }, {
    profileImage: "flowery.jpg",
    pos: uluru
  }]

  for(var i=0;i<data.length;i++){
    var marker = new CustomMarker(new google.maps.LatLng(data[i].pos[0],data[i].pos[1]), map,  data[i].profileImage)
    marker.addListener('click', function(event){
      console.log('click')
      infowindow.open(map,marker)
    })
  }
};
