/**
 * Created by Stephen on 14/04/2017.
 */
// JS file to deal with requestModel stuff on view-item page
$(function() {

    //equal height rows
    $('.small').matchHeight();

    var baseURL = window.location.protocol + "//" + window.location.host;


    // Make listing inactive - THIS WORKS (BUT ALSO REMOVES ALL TRANSACTIONS FOR THAT LISTING)
    $(document).on('click', 'a[href="#remove"]', function (event) {
        event.preventDefault();

        // Extract listingID
        var listingID = LISTING_ID

        var itemName = ITEM_NAME

        $('body').append(deleteModal);

        $("#delete-modal").modal({backdrop: "static"})

        $("#delete-modal").on("shown.bs.modal", function () {
            $(this).find('.modal-msg').html("Do you want to remove the item ")
            $(this).find('.item-name').html(itemName + '?')
            $(this).find('.modal-submsg').html("You won't be able to undo this once you press 'Ok'")
        }).modal('show');

        $("#delete-modal #ok").on('click', function () {
            // Send to /items/remove-listing
            var url = baseURL + "/items/remove-listing";
            var data = {listingID: listingID};

            $('#delete-modal').modal('hide');

            $.post(url, data, function (response) {
                console.log(response);
                if (response) {
                    // Redirect somewhere?? Listing no longer exists

                } else {
                    // Show error
                }
            });
        });
        $('#delete-modal').on('hidden.bs.modal', function () {
            console.log("hidden");
            $('#delete-modal').remove();
        });
    });



    // Cancel request using listingID
    $(document).on('click', 'a[href="#cancel-by-listing"]', function (event) {
        event.preventDefault();
        var button = $(this);
        console.log("Cancelling");

        // Extract transactionID and listingID
        var listingID = LISTING_ID

        // Send to /items/cancel-request

        var url = baseURL + "/items/cancel-request-listing";
        var data = {listingID: listingID};

        var itemName = ITEM_NAME

        $('body').append(deleteModal);

        $("#delete-modal").modal({backdrop: "static"})

        $("#delete-modal").on("shown.bs.modal", function () {
            $(this).find('.modal-msg').html("Are you sure you want to cancel the request for ")
            $(this).find('.item-name').html(itemName + '?')
            $(this).find('.modal-submsg').html("You won't be able to undo this once you press 'Ok'")
        }).modal('show');

        $("#delete-modal #ok").on('click', function () {

            $('#delete-modal').modal('hide');

            $.post(url, data, function (response) {
                console.log(response);
                if (response) {
                    // Change button to a "Request" button
                    button.html("Request");
                    button.attr("href", "#request");
                } else {
                    // Show error
                }
            });
        });
        $('#delete-modal').on('hidden.bs.modal', function () {
            console.log("hidden");
            $('#delete-modal').remove();
        });
    });


    // Request listing - THIS WORKS
    $(document).on('click', 'a[href="#request"]', function (event) {
        event.preventDefault();
        var button = $(this);
        console.log("Requesting");

        // Extract listingID
        var listingID = LISTING_FROM_PAGE;


        var itemName = ITEM_NAME
        var actualQuantity = LISTING_QUANTITY

        $('body').append(requestModal);

        $("#request-modal").modal({backdrop: "static"})

        $("#request-modal").on("shown.bs.modal", function () {
            $(this).find('.item-name').html(itemName)
            $(this).find('.item-quantity').html(' / ' + actualQuantity)
            $('#request-modal #request-quantity').attr("max", actualQuantity); // Don't allow user to request more than is available
            $('#request-modal #request-quantity').val(1); // Set to 1 by default

        }).modal('show');

        $("#request-modal #ok").on('click', function () {
            var quantity = $('#request-modal #request-quantity').val(); // GET FROM POP-UP
            console.log(quantity)

            if (quantity > actualQuantity || quantity == 0) {
                // Display error
                console.log("Asking for a stupid amount, won't request");
                return;
            }

            // Send to /items/request
            var url = baseURL + "/items/request";
            var data = {listingID: listingID, quantity: quantity};

            $('#request-modal').modal('hide');

            $.post(url, data, function (response) {
                console.log(response);
                if (response) {
                    // Change button to cancel request button
                    button.html("Cancel request");
                    button.attr("href", "#cancel-by-listing");
                } else {
                    // Show error
                }
            });
        });
        $('#request-modal').on('hidden.bs.modal', function () {
            console.log("hidden");
            $('#request-modal').remove();
        });
    });

    // Edit listing - ADD URL FROM RYAN'S PAGES
    $(document).on('click', 'a[href="#edit"]', function (event) {
        event.preventDefault();

        // Extract listingID
        var listingID = LISTING_ID_FROM_PAGE;

        // Send to /items/request
        var url = baseURL + "/items/edit/" + listingID; // REPLACE WITH  CORRECT URL
        //location.href = "EDIT_PAGE_URL";
        return;
    });

    // Renew listing
    $(document).on('click', 'a[href="#renew"]', function (event) {
        event.preventDefault();
        var listingID = LISTING_ID_FROM_PAGE;
        var itemName = NAME_OF_ITEM;

        $('body').append(renewModal);

        $("#renew-modal").modal({backdrop: "static"})

        //fancy datetime picker
        $('#renew-modal #renew-date').bootstrapMaterialDatePicker({format: 'D MMMM, YYYY', weekStart: 0, time: false})

        $("#renew-modal").on("shown.bs.modal", function () {
            $(this).find('.item-name').html(itemName)
        }).modal('show');

        $("#renew-modal .accept-button").on('click', function () {
            var button = $(this);
            var quantity = $('#renew-modal #renew-quantity').val();
            var date = $('#renew-modal #renew-date').val();

            // Send to /items/renew-listing/
            $('#renew-modal').modal('hide');

            var url = baseURL + "/items/renew-listing";
            var data = {listingID: listingID, quantity: quantity, useByDate: date};

            $.post(url, data, function (response) {
                // Response = listingID of new listing (old listing is now deleted)
                console.log(response);
                if (response >= 1) {
                    var newListingID = parseInt(response);
                    if (button.attr("id") == "renewEdit") {
                        // Take user to edit page of renewed listing
                        var editPageURL = baseURL + "/item/edit/" + newListingID
                        //location.href = editPageURL
                        // return;
                    }else{
                        // Take to view page of new listing
                    }
                } else {
                    // Show error
                }
            });
        });
        $('#renew-modal').on('hidden.bs.modal', function () {
            console.log("hidden");
            $('#renew-modal').remove();
            $('.dtp').remove();
        });
    });



//     // View conversation - Needs adding when merge with master
//     $('a[href="#message"]'.click(function(){
//         var listingID = $(this).attr("id");
//        // Send to /messages/conversation/[:listingID]

//         var url = baseURL + "/messages/conversation/"+listingID;

//         location.href = url;
//     });


    // Toggle listings in the watch list
    $(document).on('click', 'a[href="#watch"]', function (event) {
        event.preventDefault();

        var listingID = LISTING_FROM_PAGE;
        var isUser = GET_FROM_PAGE; // 1 if user is viewing their own listing
        var listing = WATCH_ICON;

        var data = {listingID: listingID};
        var url = baseURL + "/profile/toggle-watch-list"
        $.post(url, data, function (response) {
            // Do something depending on if response is true or false?? (Currently always true)
            console.log("DONE");
            console.log(response);

            // 1 means deleted, 2 means added
            if (response == 1) {
                // Set colour to pale (Deleted)
                listing.removeClass("watched");
            } else {
                // Set colour to dark (Added)
                listing.addClass("watched");
            }
        });
    });

    // // Remove an element from the layout - ele is in the form $(element)
    // function remove(ele) {
    //     // init Isotope
    //     var $grid = $('.grid').isotope({
    //         itemSelector: '.grid-item',
    //         percentPosition: true,
    //         masonry: {
    //             columnWidth: '.grid-sizer'
    //         }
    //     });
    //     // remove clicked element (in a very skitchy way right now)
    //     $grid.isotope('remove', ele.closest('.grid-item'))
    //     // layout remaining item elements
    //         .isotope('layout');
    // };


    // modals/popups html
    // Delete - anything to do with removing or cancelling
    var deleteModal = '<div id="delete-modal" class="modal fade" role="dialog">' +
        '<div class="modal-dialog">' +
        '<div class="modal-content">' +
        '<div class="modal-header">' +
        '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
        '</div>' +
        '<div class="modal-body">' +
        '<div class="modal-msg">Are you sure you want to remove the request for this item</div>' +
        '<div class="item-name">' +
        '</div>' +
        '<div class="modal-submsg">' +
        "The history of this request would be removed entirely from the site and can't be retrieved." +
        '</div>' +
        '</div>' +
        '<div class="modal-footer">' +
        '<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>' +
        '<button type="button" class="btn btn-primary" id="ok">Ok</button>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>';

    // Requesting a listing
    var requestModal = '<div id="request-modal" class="modal fade" role="dialog">' +
        '<div class="modal-dialog">' +
        '<div class="modal-content">' +
        '<div class="modal-header">' +
        '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
        '</div>' +
        '<div class="modal-body">' +
        '<div class="modal-msg">Make a request for</div>' +
        '<div class="item-name">' +
        '</div>' +
        '<div class="container-fluid">' +
        '<div class="form-group zero-padding request-quantity">' +
        '<label for="request-quantity">Quantity</label>' +
        '<input type="number" class="form-control" id="request-quantity" min="0">' +
        '<label class="item-quantity"></label>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="modal-footer">' +
        '<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>' +
        '<button type="button" class="btn btn-primary" id="ok">Ok</button>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>';



//     // Renew modal - may need if this button is added
//     var renewModal = '<div id="renew-modal" class="modal fade" role="dialog">'+
//         '<div class="modal-dialog">'+
//         '<div class="modal-content">'+
//         '<div class="modal-header">'+
//         '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
//         '</div>'+
//         '<div class="modal-body">'+
//         '<div class="modal-msg">Renew offer for </div>'+
//         '<div class="item-name">'+
//         '</div>'+
//         '<div class="container-fluid">'+
//         '<div class="form-group zero-padding request-quantity">'+
//         '<label for="renew-quantity">Quantity</label>'+
//         '<input type="number" class="form-control" id="renew-quantity" min="0">'+
//         '</div>'+
//         '<div class="form-group zero-padding request-quantity">'+
//         '<label for="renew-date">Expiry Date</label>'+
//         '<input type="text" class="form-control" id="renew-date">'+
//         '</div>'+
//         '</div>'+
//         '</div>'+
//         '<div class="modal-footer">'+
//         '<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>'+
//         '<button type="button" class="btn btn-primary accept-button" id="justRenew">Renew</button>'+
//         '<button type="button" class="btn btn-default accept-button" id="renewEdit">Renew & Edit</button>'+
//         '</div>'+
//         '</div>'+
//         '</div>'+
//         '</div>';
// });

});