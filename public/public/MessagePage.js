/**
 * Created by Stephen on 04/03/2017.
 */
$(document).ready(function(){
});

//Sends message when button is clicked
$(document).on('click', '#message-form #sendBtn', function(){
    console.log("Sending message");

    //Extract conversation ID from the page
    var conversationIDDiv = $("#conversation-id");
    var id = conversationIDDiv.html();

    var content = $("#message").val();     //Get the message content
    content.val("");                      //Reset textarea content to empty

    //Option 1: Add this message to display client-side, and send the message to the database without reloading messages

    //Option 2: Send the message to the database, and send back the html for the whole conversation
    //Send message
    var url = 'MessageRouter.php?type=poll&conversationID=' + conversationID + '&message=' + message;
    $.getJSON(url, function(response){
        //Don't care what the response is
        //Load messages
        loadMessages();
    });

});


//Polling for messages in the current conversation
setInterval(function(){
    loadMessages(conversationID);
}, 3000);


//GOES ON MESSAGES PAGE
function loadMessages(){
    console.log("Checking for new messages");

    //Extract conversation ID from the page
    var conversationIDDiv = $("#conversation-id");
    var id = conversationIDDiv.html();

    //Get message display part of page
    var messageDisplay = $("#message-location");

    //Replace its inner HTML with new messages
    var url = 'MessageRouter.php?type=poll&conversationID=' + conversationID;
    messageDisplay.load(url);

}
