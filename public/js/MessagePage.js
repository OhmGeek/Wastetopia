/**
* Created by Stephen on 04/03/2017.
*/
$(function () {
  console.log("ready");
  //Set scroll bar to bottom

  scrollToBottom();

  function scrollToBottom(){
    $('#message-location').scrollTop($('#message-location')[0].scrollHeight);
  }

  //Sends message when button is clicked
  $(document).on('click', '#sendBtn', function(ev){
    ev.preventDefault();
    console.log("Sending message");

    //Extract conversation ID from the page
    var conversationIDDiv = $("#conversation-id");
    var conversationID = conversationIDDiv.html();

    var content = $("#message").val();     //Get the message content
    $("#message").val("");                      //Reset textarea content to empty

    //Option 1: Add this message to display client-side, and send the message to the database without reloading messages

    //Option 2: Send the message to the database, and send back the html for the whole conversation
    //Send message
    var url = window.location.protocol + "//" + window.location.host + "/" + 'messages/send';
    var data = {conversationID:conversationID, message:content};
    console.log(url);
    console.log(data);

    $.get(url, data, function(response){
      //Don't care what the response is
      //Load message
      console.log(response);
      console.log("sent the message");
      loadMessages();

    });

  });


  //Polling for messages in the current conversation
  setInterval(function(){
    loadMessages();
  }, 3000);

  //GOES ON MESSAGES PAGE
  function loadMessages(){
    console.log("Checking for new messages");

    //Extract conversation ID from the page
    var conversationIDDiv = $("#conversation-id");
    var conversationID = conversationIDDiv.html();

    //Get message display part of page
    var messageDisplay = $("#message-location");

    //Replace its inner HTML with new messages
    var url = window.location.protocol + '//' + window.location.host + '/messages/poll-messages/' + conversationID;
    messageDisplay.load(url, function(){
      $('#message-location').scrollTop($('#message-location')[0].scrollHeight);
    });
  }
});
