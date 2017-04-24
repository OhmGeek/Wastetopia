// /**
//  * Created by ryan on 18/04/17.
//  */
//
// // This file just does all the autofill stuff, ready for the queries to kick in.
// // Rest of the functionality is dealt with in the add-item.js file.

var listingID = $('#main-container').data("listingid");
var mode = $('#main-container').data("mode");
console.log(listingID);
console.log(mode);

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

// only autofill images if we can edit items
if(mode == "edit") {
    $.getJSON($('base').attr('href') + '/api/items/view/' + listingID)
        .done(function (data) {
            console.log(data);
            // iterate through all images, getting the url, and adding them
            data.images.forEach(function (elem) {
                console.log(elem);
                showUploadedItem(elem.url, null);
            });
        });
}

