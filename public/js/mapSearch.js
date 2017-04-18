// the proper link for the search wastetopia.herokuapp.com/api/search/map/<user latitude>/<user longitude>/<search term>/<tag+ids+split+by+signs>/<tags+to+disclude>/

var lat = 54.774759
var long = -1.570329
var searchTerm = 'b'

var url = window.location.protocol + "//" + window.location.host + '/api/search/map///' + searchTerm + '////';


var positions = [];
var map;
var bounds;
var markerIcon;
var markerPinURL = window.location.protocol + "//" + window.location.host + "/js/icons/placePin.png"
var markerCloseURL = window.location.protocol + "//" + window.location.host + "/js/icons/close.png"

function initMap() {
  map = new google.maps.Map(
    document.getElementById("search-map"), {
      zoom: 13,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    console.log(map)
    bounds = new google.maps.LatLngBounds();
    markerIcon = {
      url: markerPinURL,
      scaledSize: new google.maps.Size(30, 30),
      origin: new google.maps.Point(0, 0),
      anchor: new google.maps.Point(15, 30)
    };
    console.log(markerIcon)
    geocoder = new google.maps.Geocoder();
    $.getJSON(url, function(items){
      // Add some markers to the map.
        // Note: The code uses the JavaScript Array.prototype.map() method to
        // create an array of markers based on a given "locations" array.
        // The map() method here has nothing to do with the Google Maps API.
        var markers = items.map(function(item) {
          return addMarker(item)
        });

        console.log(positions)
        // Add a marker clusterer to manage the markers.
        var markerCluster = new MarkerClusterer(map, markers,
            {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
      // for (var i in items) {
      //   console.log(items[i])
      //   addMarker(items[i]);
      // }
    })
  }

  function containPosition(pos){
    for (var position in positions) {
      if (pos.lat == position.lat && pos.long == position.long) {
        return true;
      }
    }
    return false;
  }

  function addMarker(item) {
    console.log(item)
    var position = {
      lat : parseFloat(item.Latitude),
      long : parseFloat(item.Longitude)
    }

    var latAdd = 0.000001, latSub = 0.000001, longAdd = 0.000001, longSub = 0.000001

    if (!containPosition(position)){
      positions.push(position)
      console.log('not a duplicated position')
    } else {
      console.log('found a duplicated position')
      var random = Math.random();
      if (random < 0.25) {
        position.lat += latAdd
        latAdd += 0.00001
      } else if (random < 0.5) {
        position.long -= longSub
        longSub += 0.00001
      } else if (random < 0.75) {
        position.lat -= latSub
        latSub += 0.00001
      } else {
        position.lat += longAdd
        longAdd += 0.00001
      }
      positions.push(position)
    }

    var marker = new google.maps.Marker({
      icon: markerIcon,
      map: map,
      position: new google.maps.LatLng(position.lat, position.long),
      animation: google.maps.Animation.DROP,
    })
    infoWindow(marker, map, item);
    bounds.extend(marker.getPosition());
    map.fitBounds(bounds);
    return marker
  }

  function infoWindow(marker, map, item) {
    google.maps.event.addListener(marker, 'click', function() {
      console.log(parseFloat(item.Latitude))
      console.log(parseFloat(item.Longitude))
      var contentString = '<div class="iw-container">'+
      '<div class="iw-header">'+
      '<img class="user-image" src="flowery.jpg"/>' +
      '<div class="user-details">'+
      '<a class="user-name" href="#' + item.UserID + '">'+
      item.Forename + ' ' + item.Surname  +
      '</a>'+
      '<span class="is-offering">'+
      ' is offering'+
      '</span>'+
      '</div>'+
      '</div>' +
      '<div class="item-image" style="background-image: url(food.jpg)"></div>'+
      '<div class="iw-body caption" id="' + item.ListingID + '">'+
      '<div class="item-name">'+ item.Name +'</div>'+
      '<div class="trans-info">'+
      '<div class="added-date">Added on '+ item.Time_Of_Creation +'</div>'+
      '<div><span>Quantity:</span>' + item.Quantity + '</div>'+
      '</div>'+
      '<div class="nav-btns">'+
      '<a href="#'+ item.ListingID + '" class="btn btn-primary" role="button">View</a>'+
      '<a class="btn btn-default" role="button">Request</a>'+
      '<div class="extra">'+
      '<a href="#watch" role="button" class="btn-watch lightgrey watched"><i'+
                  'class="material-icons">visibility</i></a>'+
        '<a href="#message" role="button" class="btn-watch" id="'+ item.ListingID +'"><i class="material-icons">message</i></a>'+
      '</div>' +
      '</div>'+
      '</div>'+
      '</div>';
      iw = new google.maps.InfoWindow({
        content: contentString,
        maxWidth: 300
      });
      iw.addListener('domready', function() {

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
        var iwCloseImg = iwCloseBtn.children(':nth-child(1)').attr('src',markerCloseURL);
        iwCloseImg.css({width: '100%', height : '100%', position: 'relative', top:'0',left:'0'})
      });
      iw.open(map, marker);
    });
  }

  $(function(){
    initMap()
    setTimeout(function(){google.maps.event.trigger(map, 'resize'); console.log('timeout')}, 3000);
  })
