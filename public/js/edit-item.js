// /**
//  * Created by ryan on 18/04/17.
//  */
//
// // This file just does all the autofill stuff, ready for the queries to kick in.
// // Rest of the functionality is dealt with in the add-item.js file.

var listingID = $('#main-container').data("listingid");
var mode = $('#main-container').data("mode");

// only autofill images if we can edit items
if(mode == "edit") {
    $.getJSON('https://wastetopia-pr-17.herokuapp.com/api/items/view/' + listingID)
        .done(function (data) {
            // iterate through all images, getting the url, and adding them
            data.images.forEach(function (elem) {
                showUploadedItem(elem.url, null);
            });
        });
}