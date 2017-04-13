// JS file to deal with requestModel stuff on any page (if cardIDs are the same)
$(function(){
    
    //TODO: Test renew with date
    //TODO: Link to edit and messaging pages - When everything is merged to master
    //TODO: Stop quantity of completed transaction exceeding quantity of listing - Has to be done in requestModel
    //TODO: Fix issue with requestedQuantity not showing on Modal for Marking as Complete
    //TODO: Deal with transactions for listings with 0 quantity or for inactive listings
    //TODO: Let user know when a request is rejected
    //TODO: There is no formatting for the date on the renew modal: user doesn't know how to write it


    var baseURL =  window.location.protocol + "//" + window.location.host;

    //fancy datetime picker
    $('#renew-date').bootstrapMaterialDatePicker({ format : 'D MMMM, YYYY', weekStart : 0, time: false }).on('open', function(event){
      console.log('open');
    });

    
    // Given a sub tab (i.e "pending-3"), it updates the number by adding "value" to it (can be negative)
    function changeSubTabCounter(counter, value){
        if (!(counter == null)){
            var html = counter.html();
            var name = html.split("-")[0];
            var count = html.split("-")[1];
            var newCount = parseInt(count) + value;
            counter.html(name+"- "+newCount);
        }
    }
    

//     $(document).on('click', '#addOffer', function(){
//         // Send to add-item page
//     });


//     // Delete completed transaction?? (Would remove for other user too)
//     $grid.on('click', 'a[href="#delete"]', function(){
//       // In offers.completed section, what is Delete supposed to do?
//       // Set Active flag for ListingTransactions to 0??
//         var transactionID = $(this).closest('.thumbnail').attr("id");
//         var listingID = $(this).prevAll('a[href="#view"]').attr("id");

//     });


    // Make listing inactive - THIS WORKS (BUT ALSO REMOVES ALL TRANSACTIONS FOR THAT LISTING)
    $(document).on('click', 'a[href="#remove"]', function(event){
      event.preventDefault();
      var card = $(this).closest('.thumbnail');

      // Extract listingID
      var listingID = $(this).attr("id");

      var itemName = card.find('.caption').find('h3').text()
      console.log(itemName)

      $('body').append(deleteModal);

      $("#delete-modal").modal({backdrop: "static"})

      $("#delete-modal").on("shown.bs.modal", function () {
        $(this).find('.modal-msg').html("Do you want to remove the item ")
        $(this).find('.item-name').html(itemName + '?')
        $(this).find('.modal-submsg').html("You won't be able to undo this once you press 'Ok'")
      }).modal('show');

      $("#delete-modal #ok").on('click', function(){
        // Send to /items/remove-listing
        var url = baseURL + "/items/remove-listing";
        var data = {listingID : listingID};

        $('#delete-modal').modal('hide');

        $.post(url, data, function(response){
          if(response){
            // Remove card from screen
            remove(card);

            // Take 1 off Listings counter
            var listingsCounter = $('a[href="#listings"] .count');
            if (!(listingsCounter == null)){
                listingsCounter.html(parseInt(listingsCounter.html()) - 1);
            }

            // Take 1 off appropriate sub tab (check both of them)
            var availableListingsTab = $('#available-listing');
            var outOfStockTab = $('#out-of-stock-listing');

            var counter = null;
            // Set counter to whichever tab is active
            if (availableListingsTab.hasClass("active")){
               var counter = $('a[href="#available-listing"]');
            }else if(outOfStockTab.hasClass("active")){
              var counter = $('a[href="#out-of-stock-listing"]');
            }else{
                // Do nothing else
            }

            // If it exists on the page, change the name
             changeSubTabCounter(counter, -1)
            
          }else{
            // Show error
          }
        });
      });
      $('#delete-modal').on('hidden.bs.modal', function(){
        console.log("hidden");
        $('#delete-modal').remove();
      });
    });

    // Mark request as complete - SEEMS TO WORK
    // added modal for this code
 $(document).on('click', 'a[href="#complete"]', function(event){
      event.preventDefault();
      var card = $(this).closest('.thumbnail');
      // Extract transactionID and listingID and new quantity
      var transactionID = card.attr("id");
      var listingID = $(this).closest(".btn-watch").prevAll('a[href="#view"]').attr("id");

      var itemName = card.find('.caption').find('h3').text()
      var requestedQuantity = card.find('.caption').find('.trans-info .quantity').text() // This doesn't work

      $('body').append(completeModal);

      $("#complete-modal").modal({backdrop: "static"})

      $("#complete-modal").on("shown.bs.modal", function () {
        $(this).find('.item-name').html(itemName)
        $(this).find('.requested-quantity').html(' / ' + requestedQuantity)
      }).modal('show');

        $("#complete-modal #ok").on('click', function(){
            var quantity = $('#complete-modal #complete-quantity').val(); // GET FROM POP-UP
              // What's to stop them putting more than their original listing had?? Nothing. In the requestModel, don't ever set a quantity < 0, default it to 0 as the boundary
            // Send to /items/confirm-request

            var url = baseURL + "/items/confirm-request";
            var data = {listingID : listingID, transactionID : transactionID, quantity: quantity};

            $('#complete-modal').modal('hide');

            $.post(url, data, function(response){
              if(response){
                    // Remove card from screen
                    remove(card);

                    // Take 1 off pending transactions, add 1 to complete transactions
                    var pendingCounter = $('a[href="#pending-transaction"]');
                    var completedCounter = $('a[href="#completed-transaction"]');
                    changeSubTabCounter(pendingCounter, - 1);
                    changeSubTabCounter(completedCounter, 1);
              }else{
                // Show error
              }
            });
        });

      $('#complete-modal').on('hidden.bs.modal', function(){
        console.log("hidden");
        $('#complete-modal').remove();
      });
 });


    //Reject request - THIS WORKS
 $(document).on('click', 'a[href="#reject"]', function(event){
      event.preventDefault();
        var card = $(this).closest('.thumbnail');
      // Extract transactionID and listingID
       var transactionID = card.attr("id");
       var listingID = $(this).closest(".btn-watch").prevAll('a[href="#view"]').attr("id");
      // Send to /items/reject-request

      var itemName = card.find('.caption').find('h3').text()
      console.log(itemName)

      $('body').append(deleteModal);

      $("#delete-modal").modal({backdrop: "static"})

      $("#delete-modal").on("shown.bs.modal", function () {
        $(this).find('.modal-msg').html("Do you want to reject the request for ")
        $(this).find('.item-name').html(itemName + '?')
        $(this).find('.modal-submsg').html("You won't be able to undo this once you press 'Ok'")
      }).modal('show');

      $("#delete-modal #ok").on('click', function(){
        var url = baseURL + "/items/reject-request";
        var data = {listingID : listingID, transactionID : transactionID};
        console.log(data);

        $('#delete-modal').modal('hide');

        $.post(url, data, function(response){
            console.log(response)
           if(response){
               // Remove card from screen
               remove(card);

               // Take 1 off offers received tab
               var offersReceivedCounter = $('a[href="#offers"] .count');
               if(!(offersReceivedCounter == null)){
                  offersReceivedCounter.html(parseInt(offersReceivedCounter.html()) - 1);
               }

               // Take 1 off pending transactions sub tab
               var pendingCounter = $('a[href="#pending-transaction"]');
                changeSubTabCounter(pendingCounter, - 1);

           }else{
               // Show error
           }
        });
      });
      $('#delete-modal').on('hidden.bs.modal', function(){
        console.log("hidden");
        $('#delete-modal').remove();
      });
    });


    // Cancel request - THIS WORKS (ONLY ON USER'S OWN PROFILE)
    $(document).on('click', 'a[href="#cancel"]', function(event){
      event.preventDefault();
      var card = $(this).closest('.thumbnail');
      console.log("Cancelling");

      // Extract transactionID and listingID
      var transactionID = card.attr("id");
      console.log(transactionID);

      // Send to /items/cancel-request

      var url = baseURL + "/items/cancel-request";
      var data = {transactionID : transactionID};

      var itemName = card.find('.caption').find('h3').text()
      console.log(itemName)

      $('body').append(deleteModal);

      $("#delete-modal").modal({backdrop: "static"})

      $("#delete-modal").on("shown.bs.modal", function () {
        $(this).find('.modal-msg').html("Are you sure you want to cancel the request for ")
        $(this).find('.item-name').html(itemName + '?')
        $(this).find('.modal-submsg').html("You won't be able to undo this once you press 'Ok'")
      }).modal('show');

      $("#delete-modal #ok").on('click', function(){
        $('#delete-modal').modal('hide');
        $.post(url, data, function(response){
          console.log(response);
          if(response){
            // Remove card from screen
            remove(card);

             // Take 1 off requests made tab
               var requestsMadeCounter = $('a[href="#offers"] .count');
               if(!(requestsMadeCounter == null)){
                  requestsMadeCounter.html(parseInt(requestsMadeCounter.html()) - 1);
               }

               // Take 1 off pending requests sub tab
               var pendingCounter = $('a[href="#pending-request"]');
                changeSubTabCounter(pendingCounter, - 1);
            

              // Take one off Requests Made counter and Pending counter
          }else{
            // Show error
          }
        });
      });
      $('#delete-modal').on('hidden.bs.modal', function(){
        console.log("hidden");
        $('#delete-modal').remove();
      });
    });

    // Cancel request using listingID - USED WHEN VIEWING CARD OUTSIDE OF YOUR PROFILE (I.E on search page)
    // WORKS BUT DOES SOME WEIRD REDIRECTION WITH A SERVER ERROR AFTER IT'S DONE
    $(document).on('click', 'a[href="#cancel-by-listing"]', function(event){
      event.preventDefault();
      var button = $(this);
      console.log("Cancelling");

      // Extract transactionID and listingID
      var listingID = $(this).closest('.thumbnail').attr("id");
      console.log(listingID);

      // Send to /items/cancel-request

      var url = baseURL + "/items/cancel-request-listing";
      var data = {listingID : listingID};

      var card = $(this).closest('.thumbnail');

      var itemName = card.find('.caption').find('h3').text()
      console.log(itemName)

      $('body').append(deleteModal);

      $("#delete-modal").modal({backdrop: "static"})

      $("#delete-modal").on("shown.bs.modal", function () {
        $(this).find('.modal-msg').html("Are you sure you want to cancel the request for ")
        $(this).find('.item-name').html(itemName + '?')
        $(this).find('.modal-submsg').html("You won't be able to undo this once you press 'Ok'")
      }).modal('show');

      $("#delete-modal #ok").on('click', function(){

        $('#delete-modal').modal('hide');

        $.post(url, data, function(response){
          console.log(response);
          if(response){
            // Do something
            // Change button to a "Request" button
            button.html("Request");
            button.attr("href", "#request");

            // Take 1 off Requests Made tab counter
               var requestsMadeCounter = $('a[href="#offers"] .count');
               if(!(requestsMadeCounter == null)){
                  requestsMadeCounter.html(parseInt(requestsMadeCounter.html()) - 1);
               }

          }else{
            // Show error
          }
        });
      });
      $('#delete-modal').on('hidden.bs.modal', function(){
        console.log("hidden");
        $('#delete-modal').remove();
      });
    });


    // Request listing - THIS WORKS
    $(document).on('click', 'a[href="#request"]', function(event){
        event.preventDefault();
        var button = $(this);
        console.log("Requesting");

       // Extract listingID
      var listingID = $(this).prevAll('a[href="#view"]').attr("id");

      // Pop up to get quantity
      var card = $(this).closest('.thumbnail');

      var itemName = card.find('.caption').find('h3').text()
      var actualQuantity = card.find('.caption').find('.trans-info .quantity').text()
      console.log(card.find('.caption').find('.trans-info .quantity'))

      $('body').append(requestModal);

      $("#request-modal").modal({backdrop: "static"})

      $("#request-modal").on("shown.bs.modal", function () {
        $(this).find('.item-name').html(itemName)
        $(this).find('.item-quantity').html(' / ' + actualQuantity)
        $('#request-modal #request-quantity').attr("max", actualQuantity); // Don't allow user to request more than is available

      }).modal('show');

      $("#request-modal #ok").on('click', function(){
        var quantity = $('#request-modal #request-quantity').val(); // GET FROM POP-UP
        console.log(quantity)

         if(quantity > actualQuantity || quantity == 0){
             // Display error
             console.log("Asking for a stupid amount, won't request");
             return;
         }

        // Send to /items/request
        var url = baseURL + "/items/request";
        var data = {listingID : listingID, quantity: quantity};

        $('#request-modal').modal('hide');

        $.post(url, data, function(response){
            console.log(response);
           if(response){
               // Do something
               // Change button to cancel request button
               button.html("Cancel request");
               button.attr("href", "#cancel-by-listing");

               // Add 1 to Requests Made tab counter
               var requestsMadeCounter = $('a[href="#offers"] .count');
               if(!(requestsMadeCounter == null)){
                  requestsMadeCounter.html(parseInt(requestsMadeCounter.html()) + 1);
               }

               // Add 1 to pending requests sub tab
               var pendingRequestsTab = $('#pending-request');
               var pendingCounter = $('a[href="#pending-request"]');
               changeSubTabCounter(pendingCounter, + 1);
             }else{
               // Show error
             }
           });
         });
         $('#request-modal').on('hidden.bs.modal', function(){
           console.log("hidden");
           $('#request-modal').remove();
         });
       });

       // Edit listing - ADD URL FROM RYAN'S PAGES
       $(document).on('click', 'a[href="#edit"]', function(event){
         event.preventDefault();

         // Extract listingID
         var listingID = $(this).prevAll('a[href="#view"]').attr("id");

         // Send to /items/request
         var url = baseURL + "/items/edit"; // REPLACE WITH  CORRECT URL
         var data = {listingID : listingID};
         //location.href = "EDIT_PAGE_URL";
         return;
       });

       // Rate listing(user)
       $(document).on('click', 'a[href="#rate"]', function(event){
         event.preventDefault();
         var button = $(this);
         console.log("Rating");

         // Extract listingID
         var card = $(this).closest('.thumbnail')
         var transactionID = card.attr("id");

         // Pop up box here
         // Pop up to get quantity
         var itemName = card.find('.caption').find('h3').text()
         var userName = card.find('.caption').find('.user-name').text()

         $('body').append(rateModal);

         $("#rate-modal").modal({backdrop: "static"})

         $("#rate-modal").on("shown.bs.modal", function () {
           $(this).find('.item-name').html(itemName)
           $(this).find('.rate-user').html('Rate ' + userName)
         }).modal('show');

         $("#rate-modal #ok").on('click', function(){
           var rating = $('#rate-modal #rate').val();// Get from pop-up

           // NEED TO DECIDE WHAT URL TO USE
           var url = baseURL + "/items/rate-user"; // GET CORRECT URL
           var data = {transactionID : transactionID, rating: rating};
           console.log(data);

           //TODO add a check for the rating value then allow the modal to hide
           $('#rate-modal').modal('hide');

           $.post(url, data, function(response){
             if(response){
               // Remove rating buton from card
               button.remove();
             }else{
               // Show error
             }
           });
         });
         $('#rate-modal').on('hidden.bs.modal', function(){
           console.log("hidden");
           $('#rate-modal').remove();
         });
       });


    // Renew listing
    $(document).on('click','a[href="#renew"]',function(event){
      event.preventDefault();
        var listingID = $(this).attr("id");

        var card = $(this).closest('.thumbnail')
        var itemName = card.find('.caption').find('h3').text()

        $('body').append(renewModal);

        $("#renew-modal").modal({backdrop: "static"})

        $("#renew-modal").on("shown.bs.modal", function () {
          $(this).find('.item-name').html(itemName)
        }).modal('show');

        $("#renew-modal .accept-button").on('click', function(){
           var button = $(this);
          var quantity = $('#renew-modal #renew-quantity').val();
          var date = $('#renew-modal #renew-date').val();

           // Send to /items/renew-listing/
           $('#renew-modal').modal('hide');

            var url = baseURL + "/items/renew-listing";
            var data = {listingID : listingID, quantity:quantity, useByDate: date};
            $.post(url, data, function(response){
               console.log(response);
               if(response >= 1){
                   // Do something
                   // Reload the div??
                   remove(card);

                   if (button.attr("id") == "renewEdit"){
                       var newListingID = parseInt(response);
                       var editPageURL = baseURL + "/item/edit/" + newListingID
                        //location.href = editPageURL
                       // return;
                   }
                   // Add 1 to the counter for Available listings
                   var availableListingsTab = $('#available-listing');
                   if(!(availableListingsTab == null)){
                       var counter = $('a[href="#available-listing"]');
                       changeSubTabCounter(counter, 1);
                   }

                   // Take 1 off the counter for Out of Stock listings
                   var outOfStockTab = $('#out-of-stock-listing');
                   if(!(outOfStockTab == null)){
                       var counter = $('a[href="#out-of-stock-listing"]');
                       changeSubTabCounter(counter, -1);
                   }


               }else{
                   // Show error
               }
            });
          });
      $('#renew-modal').on('hidden.bs.modal', function(){
        console.log("hidden");
        $('#renew-modal').remove();
      });
    });


    // View listing - THIS WORKS
    $(document).on('click','a[href="#view"]',function(event){
        event.preventDefault();
        console.log("VIEW");
        var listingID = $(this).attr("id");
       // Send to /items/view/[:listingID]

        var url = baseURL + "/items/view/"+listingID;
        console.log(url);

        location.href = url;
    });



//     // View conversation
//     $('a[href="#message"]'.click(function(){
//         var listingID = $(this).attr("id");
//        // Send to /messages/conversation/[:listingID]

//         var url = baseURL + "/messages/conversation/"+listingID;

//         location.href = url;
//     });


  // Toggle listings in the watch list - DOES NOT WORK (WEIRD ERROR)
  $(document).on('click', 'a[href="#watch"]', function(event){
      event.preventDefault();
      console.log("toggle");
     var listingID = $(this).closest('.thumbnail').attr("id");
     var isUser = parseInt($(this).closest('.user-stats').attr("id"));
     var listing = $(this);

     var data = {listingID: listingID}
     var url = baseURL + "/profile/toggle-watch-list"
     $.post(url, data, function(response){
       // Do something depending on if response is true or false?? (Currently always true)
       console.log("DONE");
       console.log(response);

      // Get tab button link to watch list
       var counter = $('a[href="#watchList"] .count');
       if(!(counter == null)){
            var watchListCount = parseInt(counter.html()); // Extract watchListCount if it exists
        }

      // 1 means deleted, 2 means added
      if (response == 1){
        // Set colour to pale (Deleted)
        listing.removeClass("watched");

        // Take 1 off watch list tab counter
        if(!(counter == null)){
            counter.html(watchListCount-1); // Extract watchListCount if it exists
        }
      }else{
        // Set colour to dark (Added)
        listing.addClass("watched");

        // Add 1 to watch list tab counter
        if(!(counter == null)){
            counter.html(watchListCount+1);
        }

      }
     });
  });

  // Remove an element from the layout - ele is in the form $(element)
  function remove(ele) {
    // init Isotope
   var $grid = $('.grid').isotope({
      itemSelector: '.grid-item',
      percentPosition: true,
      masonry: {
        columnWidth: '.grid-sizer'
      }
    });
    // remove clicked element (in a very skitchy way right now)
    $grid.isotope( 'remove', ele.closest('.grid-item'))
    // layout remaining item elements
    .isotope('layout');
  };


  // modals/popups html
  // Delete - anything to do with removing or cancelling
  var deleteModal = '<div id="delete-modal" class="modal fade" role="dialog">'+
                      '<div class="modal-dialog">'+
                        '<div class="modal-content">'+
                          '<div class="modal-header">'+
                            '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                          '</div>'+
                          '<div class="modal-body">'+
                            '<div class="modal-msg">Are you sure you want to remove the request for this item</div>'+
                              '<div class="item-name">'+
                              '</div>'+
                              '<div class="modal-submsg">'+
                                "The history of this request would be removed entirely from the site and can't be retrieved."+
                              '</div>'+
                            '</div>'+
                            '<div class="modal-footer">'+
                              '<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>'+
                              '<button type="button" class="btn btn-primary" id="ok">Ok</button>'+
                            '</div>'+
                          '</div>'+
                        '</div>'+
                      '</div>';

  // Requesting a listing (and renew)
  var requestModal = '<div id="request-modal" class="modal fade" role="dialog">'+
                        '<div class="modal-dialog">'+
                          '<div class="modal-content">'+
                            '<div class="modal-header">'+
                              '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                            '</div>'+
                            '<div class="modal-body">'+
                            '<div class="modal-msg">Make a request for</div>'+
                            '<div class="item-name">'+
                            '</div>'+
                            '<div class="container-fluid">'+
                              '<div class="form-group zero-padding request-quantity">'+
                                '<label for="request-quantity">Quantity</label>'+
                                '<input type="number" class="form-control" id="request-quantity" min="0">'+
                                '<label class="item-quantity"></label>'+
                              '</div>'+
                            '</div>'+
                          '</div>'+
                          '<div class="modal-footer">'+
                            '<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>'+
                            '<button type="button" class="btn btn-primary" id="ok">Ok</button>'+
                          '</div>'+
                        '</div>'+
                      '</div>'+
                    '</div>';

  // Rating a user from a transaction
  var rateModal = '<div id="rate-modal" class="modal fade" role="dialog">'+
                    '<div class="modal-dialog">'+
                      '<div class="modal-content">'+
                    '<div class="modal-header">'+
                      '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                    '</div>'+
                    '<div class="modal-body">'+
                      '<div class="modal-msg">The request for'+
                        '<div class="item-name">'+
                      '</div>'+
                    'Has been marked as complete'+
                    '</div>'+
                    '<div class="container-fluid">'+
                      '<div class="form-group zero-padding request-quantity">'+
                        '<label for="rate" class="rate-user"></label>'+
                        '<input type="number" class="form-control" id="rate" min="0" max="10">'+
                        '<label> / 10</label>'+
                      '</div>'+
                    '</div>'+
                  '</div>'+
                  '<div class="modal-footer">'+
                    '<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>'+
                    '<button type="button" class="btn btn-primary" id="ok">Ok</button>'+
                  '</div>'+
                '</div>'+
              '</div>'+
            '</div>';

  // Marking as complete
  var completeModal = '<div id="complete-modal" class="modal fade" role="dialog">'+
                        '<div class="modal-dialog">'+
                        '<div class="modal-content">'+
                          '<div class="modal-header">'+
                            '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                          '</div>'+
                          '<div class="modal-body">'+
                            '<div class="modal-msg">To mark the request for'+
                            '<div class="item-name">'+
                            '</div>'+
                            'as complete, please confirm the quantity requested.'+
                            '</div>'+
                            '<div class="container-fluid">'+
                              '<div class="form-group zero-padding request-quantity">'+
                                '<label for="complete-quantity">Requested quantity</label>'+
                                '<input type="number" class="form-control" id="complete-quantity" min="0">'+
                                '<label class="requested-quantity"></label>'+
                              '</div>'+
                            '</div>'+
                          '</div>'+
                          '<div class="modal-footer">'+
                            '<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>'+
                            '<button type="button" class="btn btn-primary" id="ok">Ok</button>'+
                          '</div>'+
                        '</div>'+
                      '</div>'+
                    '</div>';

    // Requesting a listing (and renew)
    var renewModal = '<div id="renew-modal" class="modal fade" role="dialog">'+
                          '<div class="modal-dialog">'+
                            '<div class="modal-content">'+
                              '<div class="modal-header">'+
                                '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                              '</div>'+
                              '<div class="modal-body">'+
                              '<div class="modal-msg">Renew offer for </div>'+
                              '<div class="item-name">'+
                              '</div>'+
                              '<div class="container-fluid">'+
                                '<div class="form-group zero-padding request-quantity">'+
                                  '<label for="renew-quantity">Quantity</label>'+
                                  '<input type="number" class="form-control" id="renew-quantity" min="0">'+
                                '</div>'+
                                '<div class="form-group zero-padding request-quantity">'+
                                  '<label for="renew-date">Expiry Date</label>'+
                                  '<input type="text" class="form-control" id="renew-date">'+
                                '</div>'+
                              '</div>'+
                            '</div>'+
                            '<div class="modal-footer">'+
                              '<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>'+
                              '<button type="button" class="btn btn-primary accept-button" id="justRenew">Renew</button>'+
                              '<button type="button" class="btn btn-default accept-button" id="renewEdit">Renew & Edit</button>'+
                            '</div>'+
                          '</div>'+
                        '</div>'+
                      '</div>';
});
