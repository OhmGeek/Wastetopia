/**
* Created by Stephen on 04/03/2017.
*/
$(function () {
  console.log("ready");
  //Set scroll bar to bottom

  $(window).resize(function () {
		var dropdownHeight = $(window).height() - $('#conversation-header').height() - $('.navbar').height()
		console.log(dropdownHeight)
		$('.item-frame').css({'max-height': dropdownHeight})
	});

  scrollToBottom();

  function scrollToBottom(){
    $('#message-location').scrollTop($('#message-location')[0].scrollHeight);
  }

  //Sends message when button is clicked
  $(document).on('click', '#sendBtn', function(ev){
    ev.preventDefault();

    //Extract conversation ID from the page
    var conversationIDDiv = $("#conversation-id");
    var conversationID = conversationIDDiv.html();

    var content = $("#message").val();     //Get the message content
    $("#message").val("");                 //Reset textarea content to empty

    // Send message and reload all messages
    var url = window.location.protocol + "//" + window.location.host + "/" + 'messages/send';
    var data = {conversationID:conversationID, message:content};

    $.post(url, data, function(response){
      //Don't care what the response is
      //Load messages
      loadMessages();
      scrollToBottom();

    });

  });

    // Go back to conversations page
    $(document).on('click', '#goBack', function(){
        var url = window.location.protocol + "//" + window.location.host + "/messages";
        location.href = url;
    });
	
    $(document).on('click', '#view', function(){
	   var listingID = $("#listing-id").html();
	   var url = window.location.protocol + "//" + window.location.host + "/item/view/"+listingID";
	    location.href = url;
	    
    });
	

  //Polling for messages in the current conversation
  setInterval(function(){
    loadMessages();
  }, 3000);

  // Load messages from the conversation into the message box display
  function loadMessages(){

    //Extract conversation ID from the page
    var conversationIDDiv = $("#conversation-id");
    var conversationID = conversationIDDiv.html();

    //Get message display part of page
    var messageDisplay = $("#message-location");

    //Replace its inner HTML with new messages
    var url = window.location.protocol + '//' + window.location.host + '/messages/poll-messages/' + conversationID;
    messageDisplay.load(url);
  }
});
