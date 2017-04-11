// JS file to deal with requestModel stuff on any page (if cardIDs are the same)
$(function(){
    // init Isotope
  var $grid = $('.grid').isotope({
    itemSelector: '.grid-item',
    percentPosition: true,
    masonry: {
      columnWidth: '.grid-sizer'
    }
  });
    
    //TODO: ADD FUNCTIONALITY TO SET ALL PENDING TRANSACTIONS AS VIEWED 
    //TODO: Fix watch-list toggle issue
    //TODO: Test renew and rate
    //TODO: Put in pop-up boxes where necessary
    //TODO: Add responses to successful functions (Deleting cards or reloading page or changing buttons)
    //TODO: Link to edit and messaging pages 
    //TODO: Come up with functionality for delete button
    
    var baseURL =  window.location.protocol + "//" + window.location.host;
    
//     $('a[data-target="PENDING_REQUESTS_TAB"]').click(function(){
//        var url = baseURL + "/profile/set-pending-requests-viewed";
       
//         $.post(url, function(response){
//             if(reponse){
//                 // Do nothing ?
//             }
//         });
//     });
    
    // Delete completed transaction?? (Would remove for other user too)
    $grid.on('click', '#delete', function(){
      // In offers.completed section, what is Delete supposed to do?
      // Set Active flag for ListingTransactions to 0??
        var transactionID = $(this).closest('.thumbnail').attr("id");
        var listingID = $(this).prevAll('a[href="#view"]').attr("id"); 
        
    });
    
    
    // Make listing inactive - THIS WORKS (BUT ALSO REMOVES ALL TRANSACTIONS FOR THAT LISTING)
    $grid.on('click', '#remove', function(){
      // Extract listingID
        var listingID = $(this).closest('.thumbnail').attr("id"); //????
        
      // Send to /items/remove-listing 
        var url = baseURL + "/items/remove-listing";
        var data = {listingID : listingID};
        console.log(data);
        $.post(url, data, function(response){
           if(response){
               // Remove card from screen
           }else{
               // Show error   
           }
        });
        
    });
    
    
    // Mark request as complete - SEEMS TO WORK 
    $grid.on('click', '#complete', function(){
      // Extract transactionID and listingID and new quantity
       var transactionID = $(this).closest('.thumbnail').attr("id");
       var listingID = $(this).closest(".btn-watch").prevAll('a[href="#view"]').attr("id");
        
       var quantity = 1; // GET FROM POP-UP
      // Send to /items/confirm-request
        
        var url = baseURL + "/items/confirm-request";
        var data = {listingID : listingID, transactionID : transactionID, quantity: quantity};
        $.post(url, data, function(response){
           if(response){
               // Remove card from screen
           }else{
               // Show error   
           }
        });
        

    });
    
    
    //Reject request - THIS WORKS
    $grid.on('click', '#reject', function(){
      // Extract transactionID and listingID
       var transactionID = $(this).closest('.thumbnail').attr("id");
       var listingID = $(this).closest(".btn-watch").prevAll('a[href="#view"]').attr("id"); 
      // Send to /items/reject-request
        
        var url = baseURL + "/items/reject-request";
        var data = {listingID : listingID, transactionID : transactionID};
        console.log(data);
        $.post(url, data, function(response){
            console.log(response)
           if(response){
               // Remove card from screen
           }else{
               // Show error   
           }
        });
    });
    
    
    // Cancel request - THIS WORKS (ONLY ON USER'S OWN PROFILE)
    $grid.on('click', '#cancel', function(){
        console.log("Cancelling");
        
      // Extract transactionID and listingID
        var transactionID = $(this).closest('.thumbnail').attr("id");
        console.log(transactionID);

      // Send to /items/cancel-request
        
        var url = baseURL + "/items/cancel-request";
        var data = {transactionID : transactionID};
        
        $.post(url, data, function(response){
            console.log(response);
           if(response){
               // Do something
               // Remove card from screen
           }else{
               // Show error   
           }
        });
    });
    
    // Cancel request using listingID - USED WHEN VIEWING CARD OUTSIDE OF YOUR PROFILE (I.E on search page)
    // WORKS BUT DOES SOME WEIRD REDIRECTION WITH A SERVER ERROR AFTER IT'S DONE
    $grid.on('click', '#cancel-by-listing', function(){
        console.log("Cancelling");
        
      // Extract transactionID and listingID
        var listingID = $(this).closest('.thumbnail').attr("id");
        console.log(listingID);

      // Send to /items/cancel-request
        
        var url = baseURL + "/items/cancel-request-listing";
        var data = {listingID : listingID};
        
        $.post(url, data, function(response){
            console.log(response);
           if(response){
               // Do something
               // Change button to "Request"
           }else{
               // Show error   
           }
        });
    });
    
    
    // Request listing - THIS WORKS
    $grid.on('click', '#request', function(event){
        event.preventDefault();
        console.log("Requesting");
        // Extract listingID
      var listingID = $(this).prevAll('a[href="#view"]').attr("id");  
        console.log(listingID);
        // Send to /items/request
        var url = baseURL + "/items/request";
        var data = {listingID : listingID};
        $.post(url, data, function(response){
            console.log(response);
           if(response){
               // Do something
               // Change button to cancel request button
           }else{
               // Show error   
           }
        });
    });
    
    // Edit listing - ADD URL FROM RYAN'S PAGES
    $grid.on('click', '#edit', function(event){
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
    $grid.on('click', '#rate', function(){
        console.log("Rating");
        
      // Extract listingID
        var transactionID = $(this).closest('.thumbnail').attr("id"); 
        var rating = 0; // Get from pop-up
        
      // NEED TO DECIDE WHAT URL TO USE
        var url = baseURL + "/items/rate-user"; // GET CORRECT URL
        var data = {transactionID : transactionID, rating: rating};
        console.log(data);
        
        $.post(url, data, function(response){
           if(response){
               // Remove rating buton from card
           }else{
               // Show error   
           }
        });
        
    });
    
    
    // Renew listing
    $(document).on('click','a[href="#renew"]',function(){
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
  $grid.on('click', '#watch', function(){
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
});
