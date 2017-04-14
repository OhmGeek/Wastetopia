// init Isotope
var $grid = $('.grid').isotope({
  itemSelector: '.grid-item',
  percentPosition: true,
  masonry: {
    columnWidth: '.grid-sizer'
  }
});

//equal height rows
$('.upload-pic').matchHeight();

//fancy select
$(".js-example-basic-multiple").select2();

//fancy datetime picker
$('#date').bootstrapMaterialDatePicker({ format : 'D MMMM, YYYY', weekStart : 0, time: false });

// Remove an element from the layout in the grid
function remove(elem) {
  // remove clicked element (in a very skitchy way right now)
  $grid.isotope( 'remove', $(elem).closest('.grid-item'))
  // layout remaining item elements
  .isotope('layout');
};

// Remove selected images
$('#deleteBtn').on( 'click', function() {
  var $checkboxes = $('.item-imgs-section input');
  // inclusive filters from checkboxes
  $checkboxes.each( function( i, elem ) {
    // if checkbox, use value if checked
    if ( elem.checked ) {
      remove(elem)
    }
  });
});

function showUploadedItem(url, id) {
  var $item = $('<div class="grid-item col-xs-4 col-sm-2 zero-padding">'+
  '<div class="row-action-primary checkbox img-checkbox">'+
  '<label><input type="checkbox"></label>'+
  '</div>'+
  '<div data-mh="my-group" class="upload-pic">'+
  '<img src="'+ url +'" data-imgid="' + id + '"</div>'+
  '</div>'+
  '</div>');

  // prepend items to grid
  $grid.prepend( $item )
  // add and lay out newly prepended items
  .isotope( 'prepended', $item );
}

function imageUpload() {
  // go through and get the images
  var formdata = new FormData($('#form-image')[0]);
  formdata.push('image', $('input[type=file]')[0].files[0]); // todo add all files
  $.ajax({
    url: 'https://wastetopia-pr-17.herokuapp.com/api/items/addimage',
    type: "POST",
    data: formdata,
    cache: false,
    processData: false,
    contentType: false,
    success: function (res) {
      var items = JSON.parse(res);
      items.forEach(function(item) {
        showUploadedItem(item.url, item.id);
      });
    }
  });

}

$('#form-image').change(function() {
  imageUpload();
});

function getImagesFromDOM() {
  var imageList = [];
  $('.upload-pic img').each(function(index, elem) {
    imageList.push(elem.href); //todo check this - we want to get the href of the image tag
  });
  return imageList;
}

function getSelectedDietaryReqs() {
  var requirementsList = [];
  // todo use filter to get this working nicely.
  $('.dietary-req option:selected').each(function(index, elem) {
    // if the selected checkbox is actually selected, add the item
    // to the requirements list. Otherwise, move to the next one.
    if(elem.text) {
      requirementsList.push(elem.text); //get the text (or the contents of the tag).
    }
  });

  // check if the user has any other requirements that need fulfilling.
  var otherRequirements = $('#other-req').val();

  if(otherRequirements) {
    requirementsList.push(otherRequirements);
  }

  return requirementsList;
}

function getMayContainsDetails() {
  var mayContain = [];
  $('.may-contains-select option:selected').each(function(index, elem) {
    if(elem.text) {
      mayContain.push(elem.text);
    }
  });
  var otherMayContains = $('#other-content').val();

  if(otherMayContains) {
    mayContain.push(otherMayContains);
  }
  return mayContain;
}


function getLocationOfItem(callbackToSubmit) {
  // todo use Google Maps/Ben's API
  // var location = {
  //     "state": Alabama,
  //     "firstLineAddr": 23 Frances,
  //     "secondLineAddr": Postcode
  // }
  //must include google api link
  //can just use the postcode and country then make a request to google api to get the longitude, latitude
  //(need this for map search :p) and city < can make this as one of the option no need to use the api
  var geocoder = new google.maps.Geocoder();
  var latlng = geocoder.geocode({
    componentRestrictions: {
      country: $('#inputLocation1').val(),
      postalCode: $('#inputLocation2').val()
    }
  },
  function(results, status) {
    if (status === google.maps.GeocoderStatus.OK) {
      console.log(results[0].geometry.location);
        // return a location object
        var latlng = results[0].geometry.location;
        var locationFinal = {
            'firstLineAddr': $('#inputLocation1').val(),
            'secondLineAddr': $('#inputLocation2').val(),
            'lat': latlng.lat(),
            'long': latlng.lng()
        };
      return callbackToSubmit(locationFinal);
    } else {
      alert("geocode of " + $('#inputLocation2').val() + " failed:" + status);
    }
  });
}


function getStateDetails() {
    var state = [];
    $('#state option:selected').each(function(index, elem) {
        if(elem.text) {
            state.push(elem.text);
        }
    });
    var otherMayContains = $('#otherstate').val();

    if(otherMayContains) {
        state.push(otherMayContains);
    }
    return state;
}
function serializeAndSendItem(location) {
  //todo: process expiry date (need more research into this). Think it's just .val, but not fully sure.
  // todo: process item type properly.
  // todo: check errors in images/date/location
  console.log("Start serializing and sending item");
  var itemData = {
    "name": $('#name').val(),
    "images": getImagesFromDOM(),
    "classification": $("#type option:selected").text(), //get the text of the selected option
    "dietary": getSelectedDietaryReqs(), //dietary requirement
    "contains": getMayContainsDetails(), //allergy tags
    "state": getStateDetails(),
    "expires": $('#date').val(),
    "description": $('#description').val(),
    "location": location
  };

    console.log(itemData);
    if(isValid(itemData)) {
        // submit using AJAX
        var jsonData = JSON.stringify(itemData);
        $.post('https://wastetopia-pr-17.herokuapp.com/api/items/additem', jsonData, function(response) {
            console.log("Sent AJAX");
            console.log(response);
        }, 'json');
    }
    else {
      console.log("Not valid");
    }
};
//todo validate all the fields
function isValid(itemData) {
  return true;
}
$(document).ready(function() {
    $('#main-form').on('submit', function(event) {

    });
});

function submit() {
    console.log( "Handler for .submit() called." );

    getLocationOfItem(serializeAndSendItem);

}