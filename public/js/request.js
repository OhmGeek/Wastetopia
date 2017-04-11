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
    
    var baseURL =  window.location.protocol + "//" + window.location.host;
    
    // Delete completed transaction?? (Would remove for other user too)
    $grid.on('click', '#delete', function(){
      // In offers.completed section, what is Delete supposed to do?
      // Set Active flag for ListingTransactions to 0??
        var transactionID = $(this).closest('.thumbnail').attr("id");
        var listingID = $(this).prevAll('a[href="#view"]').attr("id"); 
        
    });
    
    
    // Make listing inactive - THIS WORKS (BUT ALSO REMOVES ALL TRANSACTIONS)
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
    
    
    // Mark request as complete
    $grid.on('click', '#complete', function(){
      // Extract transactionID and listingID and new quantity
       var transactionID = $(this).closest('.thumbnail').attr("id");
       var listingID = $(this).closest(".btn-watch").prevAll('a[href="#view"]').attr("id");
      // Send to /items/confirm-request
        
        var url = baseURL + "/items/confirm-request";
        var data = {listingID : listingID, transactionID : transactionID};
        $.post(url, data, function(response){
           if(response){
               // Remove card from screen
           }else{
               // Show error   
           }
        });
        

    });
    
    
    //Reject request
    $grid.on('click', '#reject', function(){
      // Extract transactionID and listingID
       var transactionID = $(this).closest('.thumbnail').attr("id");
       var listingID = $(this).closest(".btn-watch").prevAll('a[href="#view"]').attr("id"); 
      // Send to /items/reject-request
        
        var url = baseURL + "/items/reject-request";
        var data = {listingID : listingID, transactionID : transactionID};
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
    
    // Cancel request using listingID - WORKS WHEN VIEWING CARD OUTSIDE OF YOUR PROFILE (I.E on search page)
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
               // Remove card from screen
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
    
    // Edit listing
    $grid.on('click', '#edit', function(event){
        event.preventDefault();
        
        // Extract listingID
      var listingID = $(this).prevAll('a[href="#view"]').attr("id");  
        
        // Send to /items/request
        var url = baseURL + "/items/edit"; // REPLACE WITH  CORRECT URL
        var data = {listingID : listingID};
        $.post(url, data, function(response){
            console.log(response);
           if(response){
               // Do something
           }else{
               // Show error   
           }
        });
    });
    
    // Rate listing(user)
    $grid.on('click', '#rate', function(){
      // Extract listingID
        var listingID = $(this).closest('.thumbnail').attr("id"); //????
        
      // Send to /items/remove-listing 
        var url = baseURL + "/items/rate-user"; // GET CORRECT URL
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
    
    
    // Renew listing
    $('a[href="#renew"]').click(function(){
        var listingID = $(this).attr("id");
       // Get new quantity to renew with from an alert?
       // Send to /items/renew-listing/
        
        var url = baseURL + "/items/renew-listing";
        var data = {listingID : listingID};
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
    $('a[href="#view"]').click(function(event){
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
