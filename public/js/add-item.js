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
  formdata.append('image', $('input[type=file]')[0].files[0]); // todo add all files
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
  $('.upload-pic img').each(function(elem) {
    imageList.append(elem.href); //todo check this - we want to get the href of the image tag
  });
  return imageList;
}

function getSelectedDietaryReqs() {
  var requirementsList = [];
  // todo use filter to get this working nicely.
  $('.dietary-req').each(function(elem) {
    // if the selected checkbox is actually selected, add the item
    // to the requirements list. Otherwise, move to the next one.
    if(elem.text()) {
      requirementsList.append(elem.text()); //get the text (or the contents of the tag).
    }
  });

  // check if the user has any other requirements that need fulfilling.
  var otherRequirements = $('#other-req').val();

  if(otherRequirements) {
    requirementsList.append(otherRequirements);
  }

  return requirementsList;
}

function getMayContainsDetails() {
  var mayContain = [];
  $('.may-contains-select option:selected').each(function(elem) {
    if(elem.text()) {
      mayContain.append(elem.text());
    }
  });
  return mayContain;
}


function getLocationOfItem() {
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
  geocoder.geocode({
    componentRestrictions: {
      country: $('#country').val(),
      postalCode: $('#postcode').val()
    }
  },
  function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
      return results[0].geometry.location;
    } else {
      alert("geocode of " + $('#postcode').val() + " failed:" + status);
    }
  });
}

function serializeItem() {
  //todo: process expiry date (need more research into this). Think it's just .val, but not fully sure.
  // todo: process item type properly.
  // todo: check errors in images/date/location

  var item = {
    "name": $('#name').val(),
    "images": getImagesFromDOM(),
    "classification": $("#type option:selected").text(), //get the text of the selected option
    "dietary": getSelectedDietaryReqs(), //dietary requirement
    "contains": getMayContainsDetails(), //allergy tags
    "expires": $('#date').val(),
    "description": $('description').val(),
    "location": getLocationOfItem()
  };

  return item;
}

$('#submit-item').on('click', function() {
  this.submit();
});

$(document).ready(function() {
    $( "#main-form" ).on('submit', function( event ) {
        console.log( "Handler for .submit() called." );
        event.preventDefault();

        var itemData = serializeItem();
        console.log(itemData);
        // if(isValid(itemData)) {
        //   // submit using AJAX
        //     $.post('https://wastetopia-pr-17.herokuapp.com/api/items/additem', jsonData, function(response) {
        //         console.log(response);
        //     }, 'json');
        // }
    });
});