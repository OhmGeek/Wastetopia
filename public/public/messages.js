//TODO - Figure out how to know what the current userId is (perhaps with a small php request)
//     - Figure out how to continuously poll for new messages


var userId = 1 // Get it from php using cookies on actual website (NOT USED?)
$(document).ready(function(){

});




// function addUser(otherId, otherFName, otherLName, peopleList){
// 	var element = $("<li>");
// 	var link = $("<a>");
// 	link.attr("id", otherId);
// 	link.append(otherFName + " " +otherLName);
// 	element.append(link);
// 	peopleList.append(element);
// }


$(document).on('click', '#people-list a', function(){
	// Display header
	var id = $(this).attr("id");
	var details = id.split("-");
	var otherUserId = details[0];  //Shouldn't need this

	var conversationID = details[1]; //ADDED TO MAKE IT EASIER (need to add it to TWIG file)
	
	$(this).css("background-color","white");

	$(".mainHeader").html($(this).text());  //Set the main header
	$(".mainHeader").attr("id", "header-"+conversationID); //Store useful details in id of header
	
	loadMessages(conversationID);
});


$(document).on('click', '#message-form #btn', function(){
	var id = $(".mainHeader").attr("id");
	id = id.split("-");
	//var otherUserId = id[1];

	var conversationID = id[1];  //Added to make it easier to find conversations in Database
	
	var content = $("#message-box").val();          //Get the message content
	$("#message-box").val("");                      //Reset textarea content to empty

    //Option 1: Add this message to display client-side, and send the message to the database
    //Option 2: Send the message to the database, and send back the html for the whole conversation

});



//POLLING

//Polling for messages in the current conversation
setInterval(function(){
    var id = $(".mainHeader").attr("id");
    id = id.split("-");
    var conversationID = id[1];

    if (conversationID !== "") {
        loadMessages(conversationID);
    }
}, 3000);

//Polling to update People-list (in case of new conversations/messages)
setInterval(function(){
	loadUsers();
	});
}, 3000);



function loadUsers(){
    var peopleList = $("#people-list");
    newPeopleList = null; //HTML from UserController
    peopleList.replaceWith(newPeopleList); //Replace current div with new list
}

function loadMessages(){
    console.log("Checking for specific messages");
    var header = $(".mainHeader");
    var details = header.attr("id");
    if (details !== ""){
        details = details.split("-");
        var conversationID = details[1]; //Get conversation ID

        var messageDisplay = $("#message-location");
        messageDisplay.replaceWith("HTML FROM MessageController.php for this conversationID");
    }
}



//This display stuff will be added to the twig files
// // displays messages from jsonData
// function displayMessages(jsonData, otherUserId){
// 	//Extract data
// 	var messages = jsonData['messages'];
//
// 	//Display messages
// 	var messageLocation = $("#message-location");
// 	messageLocation.html("");
//
// 	for (var x = 0; x<messages.length; x++){
// 		var message = messages[x];
// 		var content = message['content'];
// 		var sent; //boolean, true if this user sent the message
//
// 		if (message['to'] == otherUserId){
// 			sent = true;
// 		}else{
// 			sent = false;
// 		}
// 		var mainDiv = $("<div>");
// 		var container = $("<p>");
// 		if (sent){container.attr("style","text-align:right;")};
// 		container.append(content);
// 		messageLocation.append(container);
// 	}
// }

function displayError(error){
	var alerts = $("#alerts");
	var div = $("<div>").attr("class", "alert alert-danger alert-dismissable");
	var link = $("<a>").attr("class", "close");
	link.attr("data-dismiss", "alert");
	link.append("x");
	div.append(link);
	div.append(error);
	alerts.append(div);
	return;
}

	
