// JS file to deal with requestModel stuff on any page (if cardIDs are the same)
$(function(){

    // Delete completed transacion??
    $("#delete").click(function(){
      // In offers.completed section, what is Delete supposed to do?
    });
    
    
    // Make listing inactive
    $("#remove").click(function(){
      // Extract listingID
      // Send to /items/remove-listing 
    });
    
    
    // Mark request as complete
    $("#complete").click(function(){
      // Extract transactionID and listingID and new quantity
      // Send to /items/confirm-request
    });
    
    
    //Reject request
    $("#reject").click(function(){
      // Extract transactionID and listingID
      // Send to /items/reject-request
    });
    
    
    // Cancel request
    $("#cancel").click(function(){
      // Extract transactionID and listingID
      // Send to /items/cancel-request
    });
    
    
    // Request listing
    $("#request).click(function(){
        // Extract listingID
        // Send to /items/request
    });
    
    
    // Renew listing
    $("a[href=renew]").click(function(){
       // var listingID = $(this).attr("id");
       // Get new quantity to renew with
       // Send to /items/renew-listing/
    });
    
    
    // View listing
    $("a[href=view]").click(function(){
       // var listingID = $(this).attr("id");
       // Send to /items/view/[:listingID]
    });
    
    
    // View conversation
    $("a[href=message]".click(function(){
       // var listingID = $(this).attr("id");
       // Send to /messages/conversation/[:listingID]
    });
    
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
