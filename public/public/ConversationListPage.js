/**
 * Created by Stephen on 04/03/2017.
 */
$(document).ready(function(){
});

//Takes user to MessagePage for this conversation
//This will just be a link on the page
// $(document).on('click', '#people-list a', function(){
//     var id = $(this).attr("id");
//     var details = id.split("-");
//     var conversationID = details[1]; //ADDED TO MAKE IT EASIER (need to add it to TWIG file)
//
//     //Send to conversation page
//
// });

//Polling to update People-list (in case of new conversations/messages)
setInterval(function(){
    loadUsers();
}, 3000);


function loadUsers(){
    var peopleList = $("#people-list");
    var url = "MessageRouter.php?routeType=pollUsers";

    //Replace list with new HTML
    peopleList.load(url);
}


