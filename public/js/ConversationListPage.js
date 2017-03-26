/**
 * Created by Stephen on 04/03/2017.
 */
$(document).ready(function(){
});

//Takes user to MessagePage for this conversation
//Will just be a link on the page
// $(document).on('click', '#chatBtn', function(){
//     var id = $(this).attr("id");
//     var details = id.split("-");
//     var conversationID = details[1]; //ADDED TO MAKE IT EASIER (need to add it to TWIG file)
//
//     //Send to conversation page
//
// });

$(document).on('click', '#deleteBtn', function(){
  var url = "messages/delete-conversation";
  
  var conversationID = $(this).val();
  var data = {conversationID: conversationID}
  $.get(url, data, function(response){
      loadUsers();
  });
});

//Polling to update People-list (in case of new conversations/messages)
setInterval(function(){
    loadUsers();
}, 3000);


function loadUsers(){
    var givingTab = $("#giving-tab");
    var receivingTab = $("#receiving-tab");

    var givingURL = "poll-sending";
    var receivingURL = "poll-receiving";

    givingTab.load(givingURL);
    receivingTab.load(receivingURL); 
}


