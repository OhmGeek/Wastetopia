/**
 * Created by Stephen on 04/03/2017.
 */
$(document).ready(function(){
  var url = window.location.protocol + "//" + window.location.host + "/" + "js/plugins/isotope/isotope.pkgd.min.js"
  $.getScript(url , function(){
    $('#messages').imagesLoaded().progress( function() {
      $grid = $('#messages').isotope({
        itemSelector: '.grid-item',
        percentPosition: true,
        layoutMode: 'masonry'
      });
      $grid.isotope('layout');
    });
  })
});

//Takes user to MessagePage for this conversation

// $(document).on('click', '.chatBtn', function(){
//   var conversationID = $(this).attr("id");
//   var url = window.location.protocol + "//" + window.location.host + "/" + 'messages/conversation/' + conversationID ;
//   window.location.href = url;
// });

// Delete a conversation
$(document).on('click', '#deleteBtn', function(){
  var url = window.location.protocol + "//" + window.location.host + "/" + 'messages/delete-conversation';

  var listingID = $(this).val();
  var data = {listingID: listingID}
  $.get(url, data, function(response){
      loadUsers();
  });
});


// Polling to update People-list (in case of new conversations/messages)
setInterval(function(){
   loadUsers();
}, 3000);


function loadUsers(){
    var givingTab = $("#giving-tab");
    var receivingTab = $("#receiving-tab");

    var givingURL = window.location.protocol + "//" + window.location.host + "/" + "messages/poll-sending";
    var receivingURL = window.location.protocol + "//" + window.location.host + "/" + "messages/poll-receiving";
    givingTab.load(givingURL);
    receivingTab.load(receivingURL);
}
