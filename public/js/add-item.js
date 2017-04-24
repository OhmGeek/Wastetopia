var $grid;
var showUploadedItem;
$(function(){
// init Isotope
$grid = $('.grid').isotope({
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
                remove(elem);
            }
        });
    });


    showUploadedItem = function(url, id) {
      var $item = $('<div class="grid-item col-xs-6 col-md-4 zero-padding">'+
                    '<div class="img-checkbox">'+
                      '<div class="checkbox">'+
                        '<label><input type="checkbox"></label>'+
                      '</div>'+
                    '</div>'+
                    '<div data-mh="my-group" class="upload-pic">'+
                      '<img src="'+ url +'" data-imgid="' + id + '"/>'+
                    '</div>'+
                  '</div>');

        // prepend items to grid
        $grid.prepend( $item )
        // add and lay out newly prepended items
            .isotope( 'prepended', $item );
        // var currentImgs = $item + $('#img-rows').html();
        // $('#img-rows').html(currentImgs)
        $.material.init();
    }

    function imageUpload() {
        // go through and get the images
        var formdata = new FormData($('#form-image')[0]);
        formdata.append('image', $('#upload').prop('files')[0]); // todo add all files
        $.ajax({
            url: $('base').attr('href') + '/api/items/addimage',
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

    $('#form-image #upload').change(function() {
        imageUpload();
    });

    function getImagesFromDOM() {
        var imageList = [];
        $('.upload-pic img').each(function(index, elem) {
            imageList.push(elem.src); //todo check this - we want to get the href of the image tag
        });
        return imageList;
    }

    function getSelectedDietaryReqs() {
        var requirementsList = [];
        // todo use filter to get this working nicely.
        $('#dietary-requirements option:selected').each(function(index, elem) {
            // if the selected checkbox is actually selected, add the item
            // to the requirements list. Otherwise, move to the next one.
            if(elem.text) {
                requirementsList.push(elem.text); //get the text (or the contents of the tag).
            }
        });

        return requirementsList;
    }

    function getMayContainsDetails() {
        var mayContain = [];
        $('.may-contains-select option:selected').each(function(index, elem) {
            if(elem.text) {
                mayContain.push(elem.text);
            }
        });
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
                    country: "UK",
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
        var mode = $('.grid-body').data('mode');
        var listingID = $('.grid-body').data('listingid');
        console.log(mode);
        var url = "";
        if(mode == "edit") {
            url = $('base').attr('href') + "/api/items/edititem/" + listingID;
        }
        else {
            url = $('base').attr('href') + "/api/items/additem";
        }

        console.log("Start serializing and sending item");
        var itemData = {
            "name": $('#name').val(),
            "images": getImagesFromDOM(),
            "classification": [$("#type option:selected").text()], //get the text of the selected option
            "dietary": getSelectedDietaryReqs(), //dietary requirement
            "contains": getMayContainsDetails(), //allergy tags
            "state": getStateDetails(),
            "expires": $('#date').val(),
            "description": $('#description').val(),
            "location": location,
            "quantity": $('#quantity').val()
        };

        console.log(itemData);
        if(isValid(itemData)) {
            // submit using AJAX
            var jsonData = {'item': JSON.stringify(itemData)};
            console.log(jsonData);
            $.post(url, jsonData, function(resp) {
                console.log("Sent AJAX");
                var listingID = resp.listingID;
                window.location.replace($('base').attr('href') + "/items/view/" + listingID);
            }, 'json');
        }
        else {
            console.log("Not valid");
        }
    }

//todo validate all the fields
    function isValid(itemData) {
        return true;
    }
    function submit() {
        console.log( "Handler for .submit() called." );

        getLocationOfItem(serializeAndSendItem);

    }
    $( "#main-form" ).submit(function( event ) {
        event.preventDefault();
        submit();
        return false;
    });
});
