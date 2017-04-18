// the proper link for the search wastetopia.herokuapp.com/api/search/map/<user latitude>/<user longitude>/<search term>/<tag+ids+split+by+signs>/<tags+to+disclude>/

var lat = 54.774759
var long = -1.570329
var searchTerm = 'b'

var url = window.location.protocol + "//" + window.location.host + '/api/search/map///' + searchTerm + '////';


var geocoder;
var map;
var bounds;
var markerIcon;

function initMap() {
  console.log("i'm in initMap")
  map = new google.maps.Map(
    document.getElementById("search-map"), {
      zoom: 13,
      center:new google.maps.LatLng(lat,long),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    console.log(map)
    bounds = new google.maps.LatLngBounds();
    markerIcon = {
      url: 'icons/placePin.png',
      scaledSize: new google.maps.Size(30, 30),
      origin: new google.maps.Point(0, 0),
      anchor: new google.maps.Point(15, 30)
    };
    console.log(markerIcon)
    geocoder = new google.maps.Geocoder();
    $.getJSON(url, function(items){
      for (var i in items) {
        console.log(items[i])
        geocodeAddress(items[i]);
      }
    })
  }

  function geocodeAddress(item) {
    geocoder.geocode({
      componentRestrictions: {
        country: 'GB',
        postalCode: item.Post_Code
      }
    },

    function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        var marker = new google.maps.Marker({
          icon: markerIcon,
          map: map,
          position: results[0].geometry.location,
          animation: google.maps.Animation.DROP,
        })
        infoWindow(marker, map, item);
        bounds.extend(marker.getPosition());
        map.fitBounds(bounds);
      } else {
        alert("geocode of " + item.Post_Code + " failed:" + status);
      }
    });
  }

  function infoWindow(marker, map, item) {
    google.maps.event.addListener(marker, 'click', function() {
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
      '<a role="button" class="btn-watch" id="watch"><i class="material-icons">visibility</i></a>'+
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
        var iwCloseImg = iwCloseBtn.children(':nth-child(1)').attr('src','icons/close.png');
        iwCloseImg.css({width: '100%', height : '100%', position: 'relative', top:'0',left:'0'})
      });
      iw.open(map, marker);
    });
  }

$(function(){
  initMap()
  google.maps.event.addListener(map, "idle", function(){
    google.maps.event.trigger(map, 'resize');
  })
  map.setZoom( map.getZoom() - 1);
  map.setZoom( map.getZoom() + 1);
})
