$(function(){

  function updateNotifications(){    
      var url = window.location.protocol + "//" + window.location.host + "/" + 'notifications/update';
      $.getJSON(url, function(response){
          $requestNotifications = response["requestNotifications"];
          $messageNotifications = response["messageNotifications"];
          
          // Update relevant parts of the page
      });
  });
  
  setInterval(updateNotifications, 3000);
  
});
