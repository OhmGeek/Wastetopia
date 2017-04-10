// JS file to deal with requestModel stuff on any page (if cardIDs are the same)
$(function(){
    var baseURL =  window.location.protocol + "//" + window.location.host;
    
    // Delete completed transacion??
    $grid.on('click', '#delete', function(){{
      // In offers.completed section, what is Delete supposed to do?
      // Set Active flag for ListingTransactions to 0??
        var transactionID = $(this).closest('.thumbnail').attr("id");
        var listingID = $(this).closest('a[href=view]').attr("id"); 
        
    });
    
    
    // Make listing inactive
    $grid.on('click', '#remove', function(){{
      // Extract listingID
        var listingID = $(this).closest('.thumbnail').attr("id"); //????
      // Send to /items/remove-listing 
        
        var url = baseURL + "/items/remove-listing";
        var data = {"listingID" : listingID};
        $.post(url, data, function(response){
           if(response){
               // Do something
           }else{
               // Show error   
           }
        });
    });
    
    
    // Mark request as complete
    $grid.on('click', '#complete', function(){{
      // Extract transactionID and listingID and new quantity
       var transactionID = $(this).closest('.thumbnail').attr("id");
        var listingID = $(this).closest('a[href=view]').attr("id");
      // Send to /items/confirm-request
        
        var url = baseURL + "/items/confirm-request";
        var data = {"listingID" : listingID, "transactionID" : transactionID};
        $.post(url, data, function(response){
           if(response){
               // Do something
           }else{
               // Show error   
           }
        });
    });
    
    
    //Reject request
    $grid.on('click', '#reject', function(){{
      // Extract transactionID and listingID
       var transactionID = $(this).closest('.thumbnail').attr("id");
        var listingID = $(this).closest('a[href=view]').attr("id"); 
      // Send to /items/reject-request
        
        var url = baseURL + "/items/reject-request";
        var data = {"listingID" : listingID, "transactionID" : transactionID};
        $.post(url, data, function(response){
           if(response){
               // Do something
           }else{
               // Show error   
           }
        });
    });
    
    
    // Cancel request
    $grid.on('click', '#cancel', function(){{
      // Extract transactionID and listingID
        var transactionID = $(this).closest('.thumbnail').attr("id");
        var listingID = $(this).closest('a[href=view]').attr("id"); 
      // Send to /items/cancel-request
        
        var url = baseURL + "/items/cancel-request";
        var data = {"listingID" : listingID, "transactionID" : transactionID};
        $.post(url, data, function(response){
           if(response){
               // Do something
           }else{
               // Show error   
           }
        });
    });
    
    
    // Request listing
    $grid.on('click', '#request', function(event){{
        event.preventDefault();
        
        // Extract listingID
      var listingID = $(this).closest('a[href=view]').attr("id"); 
        // Send to /items/request
    
        var url = baseURL + "/items/request";
        var data = {"listingID" : listingID};
        $.post(url, data, function(response){
            console.log(response);
           if(response){
               // Do something
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
        var data = {"listingID" : listingID};
        $.post(url, data, function(response){
           if(response){
               // Do something
           }else{
               // Show error   
           }
        });
    });
    
    
    // View listing
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
    

  // Toggle listings in the watch list
  $grid.on('click', '.btn-watch', function(){
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
