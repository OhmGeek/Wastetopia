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

// only autofill images if we can edit items
if(mode == "edit") {
    $.getJSON('https://wastetopia.herokuapp.com/api/items/view/' + listingID)
        .done(function (data) {
            console.log(data);
            // iterate through all images, getting the url, and adding them
            data.images.forEach(function (elem) {
                console.log(elem);
                showUploadedItem(elem.url, null);
            });
        });
}

