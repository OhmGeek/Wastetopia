$(function(){

  // Update when first loaded
  updateNotifications();
  
  // Updates notification icons on the Navigation bar
  function updateNotifications(){
    
      // Get notifications in JSON format
      var url = window.location.protocol + "//" + window.location.host + "/" + 'notifications/update';
      $.getJSON(url, function(response){
          // Extract data
          var requestNotifications = response["requestNotifications"];
          var messageNotifications = response["messageNotifications"];
        
           // Update relevant parts of the page
        if (requestNotifications == 0){
            $("#requestNotifications").addClass("hide"); 
        }else{
            $("#requestNotifications").removeClass("hide");
            $("#requestNotifications").html(requestNotifications);          
        }
        
        if (messageNotifications == 0){
            $("#messageNotifications").addClass("hide"); 
        }else{
            $("#messageNotifications").removeClass("hide");
            $("#messageNotifications").html(messageNotifications);          
        }
      });
  }

  // Constant polling for notifications
  setInterval(updateNotifications, 3000);
  
});
