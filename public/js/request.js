// JS file to deal with requestModel stuff on any page (if cardIDs are the same)
$(function(){
//     // init Isotope
//   var $grid = $('.grid').isotope({
//     itemSelector: '.grid-item',
//     percentPosition: true,
//     masonry: {
//       columnWidth: '.grid-sizer'
//     }
//   });


    //TODO: Fix watch-list toggle issue
    //TODO: Test renew and rate
    //TODO: Link to edit and messaging pages
    //TODO: Stop quantity of completed transaction exceeding quantity of listing

    var baseURL =  window.location.protocol + "//" + window.location.host;

//     $('a[data-target="PENDING_REQUESTS_TAB"]').click(function(){
//        var url = baseURL + "/profile/set-pending-requests-viewed";

//         $.post(url, function(response){
//             if(reponse){
//                 // Do nothing ?
//             }
//         });
//     });

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
        $(this).find('.modal-msg').html("Do you want to remove the offer for ")
        $(this).find('.item-name').html(itemName + '?')
        $(this).find('.modal-submsg').html("You won't be able to undo this once you press 'Ok'")
      }).modal('show');

      $("#delete-modal #ok").on('click', function(){
        // Send to /items/remove-listing
        var url = baseURL + "/items/remove-listing";
        var data = {listingID : listingID};
        console.log(data);
        $.post(url, data, function(response){
          if(response){
            // Remove card from screen
            remove(card);
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
      var requestedQuantity = card.find('.caption').find('.trans-info .quantity').text()
      console.log(card.find('.caption').find('.trans-info .quantity'))

      $('body').append(completeModal);

      $("#complete-modal").modal({backdrop: "static"})

      $("#complete-modal").on("shown.bs.modal", function () {
        $(this).find('.item-name').html(itemName)
        $(this).find('.requested-quantity').html(' / ' + requestedQuantity)
      }).modal('show');

      $("#complete-modal #ok").on('click', function(){
        var quantity = $('#complete-modal #complete-quantity').val(); // GET FROM POP-UP
        // Send to /items/confirm-request

        var url = baseURL + "/items/confirm-request";
        var data = {listingID : listingID, transactionID : transactionID, quantity: quantity};
        $.post(url, data, function(response){
          if(response){
            // Remove card from screen
            remove(card);
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
        $.post(url, data, function(response){
            console.log(response)
           if(response){
               // Remove card from screen
               remove(card);
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
        $.post(url, data, function(response){
          console.log(response);
          if(response){
            // Remove card from screen
            remove(card);
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
        $.post(url, data, function(response){
          console.log(response);
          if(response){
            // Do something
            // Change button to a "Request" button
            button.html("Request");
            button.attr("href", "#request");
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
      var itemName = card.find('.caption').find('h3').text()
      var requestedQuantity = card.find('.caption').find('.trans-info .quantity').text()
      console.log(card.find('.caption').find('.trans-info .quantity'))

      $('body').append(requestModal);

      $("#request-modal").modal({backdrop: "static"})

      $("#request-modal").on("shown.bs.modal", function () {
        $(this).find('.item-name').html(itemName)
        $(this).find('.item-quantity').html(' / ' + requestedQuantity)
      }).modal('show');

      $("#request-modal #ok").on('click', function(){
        var quantity = $('#request-modal #request-quantity').val(); // GET FROM POP-UP
        console.log(quantity)
        // Send to /items/request
        var url = baseURL + "/items/request";
        var data = {listingID : listingID, quantity: quantity};
        $.post(url, data, function(response){
            console.log(response);
           if(response){
               // Do something
               // Change button to cancel request button
               button.html("Cancel request");
               button.attr("href", "#cancel-by-listing");
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
       $(document).on('click', '#rate', function(event){
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
           var quantity = $('#rate-modal #rate').val(); // GET FROM POP-UP
           var rating = 0; // Get from pop-up

           // NEED TO DECIDE WHAT URL TO USE
           var url = baseURL + "/items/rate-user"; // GET CORRECT URL
           var data = {transactionID : transactionID, rating: rating};
           console.log(data);

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
        var quantity = 1; // Get from pop-up

       // Send to /items/renew-listing/

        var url = baseURL + "/items/renew-listing";
        var data = {listingID : listingID, quantity:quantity};
        $.post(url, data, function(response){
           if(response){
               // Do something
               // Reload the div??
           }else{
               // Show error
           }
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
  $(document).on('click', 'a[href="#watch"]', function(){
      console.log("toggle");
     var listingID = $(this).closest('.thumbnail').attr("id");
     var isUser = parseInt($(this).closest('.user-stats').attr("id"));
     var listing = $(this);

     $.post("/profile/toggle-watch-list/"+listingID, function(response){
       // Do something depending on if response is true or false?? (Currently always true)
       console.log("DONE");
       console.log(response);
      // 1 means deleted, 2 means added
      if (response == 1){
        // Set colour to pale (Deleted)
        listing.removeClass("watched");
        console.log(listing);
      }else{
        // Set colour to dark (Added)
        listing.addClass("watched");
        console.log(listing);
      }
     });
  });

  // Remove an element from the layout - ele is in the form $(element)
  function remove(ele) {
    // remove clicked element (in a very skitchy way right now)
    $grid.isotope( 'remove', ele.closest('.grid-item'))
    // layout remaining item elements
    .isotope('layout');
  };


  // modals/popups html
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
});
