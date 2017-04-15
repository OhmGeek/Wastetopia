$(function(){

  // Updates notification icons on the Navigation bar
  function updateNotifications(){
      // Get notifications in JSON format
      var url = window.location.protocol + "//" + window.location.host + "/" + 'notifications/update';
      $.getJSON(url, function(response){
          // Extract data
          $requestNotifications = response["requestNotifications"];
          $messageNotifications = response["messageNotifications"];
          
          // Update relevant parts of the page
      });
  }

  // Constant polling for notifications
  setInterval(updateNotifications, 3000);
  
});
