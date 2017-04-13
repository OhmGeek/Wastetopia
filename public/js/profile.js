$(function () {
  
  // Get last active tab and make it active 
  var activeTab = localStorage.getItem('activeTab');
  console.log(activeTab);
  if(activeTab){
       $('a[href="' + activeTab + '"]').trigger('click');
  }
  
  // Get baseURL for the site
  var baseURL = window.location.protocol + "//" + window.location.host;
  
  // init Isotope
  var $grid = $('.grid').isotope({
    itemSelector: '.grid-item',
    percentPosition: true,
    masonry: {
      columnWidth: '.grid-sizer'
    }
  });
  
      // Displays an error message in the appropriate place
    function displayError(error){

        // Create warning div
        var errorDiv = $("<div>").addClass("alert alert-danger fade in");
    
        // Add error to the div
        errorDiv.html('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+ error)
        
        // Add alert to the alert div
        $("#errorMessage").html(errorDiv);
     }
    
     // Displays an error message in the appropriate place
    function displaySuccess(message){
        // Change HTML in an existing DIV
//         console.log("Displaying error message");
//         $("#errorMessage").html("<p>"+error+"<p>");

        // OR using bootstrap alerts
        // Create warning div
        var successDiv = $("<div>").addClass("alert alert-success fade in");
    
        // Add error to the div
        successDiv.html('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+ message)
        
        // Add alert to the alert div
        $("#errorMessage").html(successDiv);
     }
  
    // Remove an element from the layout - ele is in the form $(element)
  function remove(ele) {
    // init Isotope
   var $grid = $('.grid').isotope({
      itemSelector: '.grid-item',
      percentPosition: true,
      masonry: {
        columnWidth: '.grid-sizer'
      }
    });
    // remove clicked element (in a very skitchy way right now)
    $grid.isotope( 'remove', ele.closest('.grid-item'))
    // layout remaining item elements
    .isotope('layout');
  };

     // Given a sub tab (i.e "pending-3"), it updates the number by adding "value" to it (can be negative)
    function changeSubTabCounter(counter, value){
        if (!(counter == null)){
            var html = counter.html();
            var name = html.split("-")[0];
            var count = html.split("-")[1];
            var newCount = parseInt(count) + value;
            counter.html(name+"- "+newCount);
        }
    }
  
  // Reload certain tabs - FIX ISSUE WITH LISTING AND HOME TABS
  $(document).on('click', 'a[data-toggle="tab"]', function(){
    console.log("Trying to reload");
    var userID = $('.user-name').attr("id");
    var tabID = $(this).attr('href');
    var subTabID = "";
    var otherSubTab = "";
    
    var relativeURL = "";
    if (tabID == "#listings"){
      relativeURL = "load-listings-tab";
      tabID = "#listings"; // So it doesn't load Divs inside the other two tabs
    }else if (tabID == "#available-listing"){
      relativeURL = "load-listings-tab";
      subTabID = tabID;
      otherSubTab = "#out-of-stock-listing";
      tabID = "#listing"; // So it doesn't load Divs inside the other two tabs
    }else if (tabID == "#out-of-stock-listing"){
      relativeURL = "load-listings-tab";
      subTabID = tabID;
      otherSubTab = "#available-listing";
      tabID = "#listing"; // So it doesn't load Divs inside the other two tabs
    }else if (tabID == "#requests" ){
      relativeURL = "load-requests-tab";
      tabID = "#requests";
    }else if (tabID == "#completed-request" ){
      relativeURL = "load-requests-tab";
      subTabID = tabID;
      otherSubTab = "#pending-request";
      tabID = "#requests";
    }else if(tabID == "#pending-request" ){
      relativeURL = "load-requests-tab";
      subTabID = tabID;
      otherSubTab = "#comleted-request";
      tabID = "#requests";
    }else if(tabID == "#offers"){
     relativeURL = "load-offers-tab";
      tabID = "#offers";
    }else if(tabID == "#completed-transaction"){
      console.log("Completed!");
     relativeURL = "load-offers-tab";
      subTabID = tabID;
      otherSubTab = "#pending-transaction";
      tabID = "#offers";
    }else if(tabID == "#pending-transaction" ){
     relativeURL = "load-offers-tab";
     subTabID = tabID;
      otherSubTab = "#completed-transaction";
     tabID = "#offers";
    }else if(tabID == "#watchList"){
     relativeURL = "load-watchlist-tab";
    }else if(tabID == "#home"){
     relativeURL = "load-home-tab";
    }else{
      return;
    }
    console.log(subTabID);
    console.log(otherSubTab);
    console.log(tabID);
    
    reloadTab(tabID, relativeURL, userID, subTabID, otherSubTab);

  });
  
  // Reloads the content of the given tab, for the given user, from the given relative URL
  function reloadTab(tabID, relativeURL, userID, subTabID, otherSubTab){
    var url = baseURL + "/profile/" + relativeURL +"/" + userID;
    
    $.get(url, function(response){
      
       var div = $(tabID);
       div.replaceWith(response);
      
      $(tabID).addClass("in active"); // Make it visible?

      if(!(subTabID === "" )){
         $(subTabID).addClass("in active"); // Make it visible?
        $(subTabID).parent("li").addClass("active");
        $('a[href="'+subTabID+'"]').parent("li").addClass("active");
         $(otherSubTab).removeClass("in");
        $(otherSubTab).removeClass("active");
        $('a[href="'+otherSubTab+'"]').parent("li").removeClass("active");
       }
      console.log("Successful");
      // re initialize isotope
       $grid = $('.grid').isotope({
          itemSelector: '.grid-item',
          percentPosition: true,
          masonry: {
            columnWidth: '.grid-sizer'
          }
        });
    });
  }
  
  $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
    var tabHREF = $(e.target).attr('href');
    // If tab shown is one of the main ones
    if(tabHREF == "#home" || tabHREF == "#listings" || tabHREF == "#offers" || tabHREF == "#requests" || tabHREF == "#watchList"){
      // Store this being active in local storage
      localStorage.setItem('activeTab', $(e.target).attr('href'));
      console.log("Stored active tab");
      console.log(localStorage.getItem('activeTab'));
    }
    
    console.log($(e.target).attr("href"));
    var width = 150;
    var height = 150;
    var nameSize = 25;
    var iconSize = 35;
    var topPadding = 230;
    if (tabHREF != '#home') {
      width = 50;
      height = 50;
      nameSize = 16;
      iconSize = 20;
      topPadding = 130;
      $grid.isotope('layout');
    }
    $('.user-profile .user-img').css('width',width);
    $('.user-profile .user-img').css('height',height);
    $('.user-profile .user-name').css('font-size',nameSize);
    $('.user-profile .popularity i').css('font-size',iconSize);
    $('.user-profile .popularity').css('font-size',iconSize);
    $('.page-height').css('padding-top',topPadding);

  });

  
  // Set all pending transactions to viewed
  $(document).on('shown.bs.tab', 'a[href="#pending-transaction"]', function(){
    console.log("Setting pending as viewed");
    var url = window.location.protocol + "//" + window.location.host + "/profile/set-pending-viewed";
    console.log(url);
      $.post(url, function(response){
        // Do nothing
        console.log(response);
      })
  });

  $(document).on('click', 'a[href="#delete"]', function(event){
      event.preventDefault();
      var card = $(this).closest('.thumbnail');
      var giverOrReceiver = $("#offers").hasClass("active");
      var transactionID = card.attr("id");
      var url = baseURL + "/profile/set-listing-transaction-hidden";
     var data = {giverOrReceiver: giverOrReceiver, transactionID: transactionID}
     console.log(data);
      $.post(url, data, function(response){
        console.log(response);
        console.log("Done");
        if(response){
          // Remove card from screen
          remove(card);
          
          // Get current sub tab
          var subTabID = giverOrReceiver ? "#completed-transaction" : "#completed-request";
          var counter = $('a[href="'+subTabID+'"]');
          
          // Take 1 off current completed tab
          changeSubTabCounter(counter, - 1);
        }
      });
  });
  
  $(document).on('click', '.user-name', function(event){
    event.preventDefault();
    var userID = $(this).attr("id");
    var url = baseURL + "/profile/user/" + userID;
    location.href = url;
  });
 
  
  // Lets user upload a new profile picture
  $(document).on('click', '#upload-picture', function(event){
      event.preventDefault();
      var userID = $('.user-name').attr("id");
    
      // Set up Modal to get file from user
      $('body').append(updatePictureModal);

      $("#update-picture-modal").modal({backdrop: "static"})

      $("#update-picture-modal").on("shown.bs.modal", function () {
        // Do something?
      }).modal('show');
    
      $("#update-picture-modal .accept-button").on('click', function(){
         var formdata = new FormData();
         formdata.append('image', $('#image-file').files[0]); // todo add all files

         var url = baseURL + "/profile/change-profile-picture";
        
          //ADD FILE UPLOAD STUFF HERE
          var data = formdata;// Some array of files (only contains one file)
          
        console.log(data);
         $('#update-picture-modal').modal('hide');
        
          return;
          $.post(url, data, function(response){
                if (response){
                    reloadTab("#home", "load-home-tab", userID, "", "");
                }
           });
      });
    
    // Remove the modal 
     $('#update-picture-modal').on('hidden.bs.modal', function(){
        console.log("hidden");
        $('#update-picture-modal').remove();
      });
  });
  
  
  // Let user change their password - NEEDS MODAL
  $(document).on('click', '#change-password', function(event){
    event.preventDefault();
    
    // Set up Modal with one input for old password, one input for new password
    
    $('body').append(changeModal);

    $("#change-modal").modal({backdrop: "static"})

    $("#change-modal").on("shown.bs.modal", function () {
      $(this).find('.modal-msg').text("Please enter your current password and new password.");
    }).modal('show');
    
    
    
    $("#change-modal .accept-button").on('click', function(){
          var button = $(this);
          var oldPassword = $('#old-password').val();// Get from modal
          var newPassword = $('#new-password').val();// Get from modal
        
           // Send to /items/renew-listing/
           $('#change-modal').modal('hide');
      
            var url = baseURL + "/profile/change-password";
            var data = {oldPassword : oldPassword, newPassword : newPassword};

            $.post(url, data, function(response){
              var json = $.parseJSON(response);
              if(json.hasOwnProperty("error")){
                       console.log("Error occurred");
                        displayError("Couldn't change password: "+json["error"]);
                        return;
                   }else if(json.hasOwnProperty("success")){
                       console.log("Successful");
                       displaySuccess("Password successfully changed");
                       //Reload page (logged out so should take user to login page)   
                       return;
                    }else{
                       displayError("WHAAAAT");
                       console.log("Something really went wrong");
                       return;
                   }
            });
        });
    
       $('#change-modal').on('hidden.bs.modal', function(){
        console.log("hidden");
        $('#change-modal').remove();
      });
  });
  
//   // Let user change their email - NEEDS MODAL
//     $(document).on('click', '#change-email', function(event){
//     event.preventDefault();
    
//     // Set up Modal with one input for old password, one input for new password
    
//     var oldEmail = ;// Get from modal
//     var newEmail = ;// Get from modal
    
//     var url = baseURL + "/change-password";
//     var data = {oldEmail : oldEmail, newEmail : newEmail};
    
//     $.post(url, data, function(response){
//       var json = $.parseJSON(response);
//       if(json.hasOwnProperty("error")){
//                console.log("Error occurred");
//                 displayError(json["error"]);
//                 return;
//            }else if(json.hasOwnProperty("success")){
//                console.log("Successful");
//                displaySuccess("Email changed");
//                // Reload page (logged out so should take user to login page)  
//                return;
//             }else{
//                displayError("WHAAAAT");
//                console.log("Something really went wrong");
//                return;
//            }
//     });
//   });
  
  
//     $(document).on('click', '#addOffer', function(){
//         // Send to add-item page
//     });
  
  
  var changeModal = '<div id="change-modal" class="modal fade" role="dialog">'+
                    '<div class="modal-dialog">'+
                      '<div class="modal-content">'+
                        '<div class="modal-header">'+
                          '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                        '</div>'+
                        '<div class="modal-body">'+
                          '<div class="modal-msg"></div>'+
                          '<div class="container-fluid">'+
                                '<div class="form-group zero-padding old-password">'+
                                  '<label for="old-password">Current password: </label>'+
                                  '<input type="text" class="form-control" id="old-password">'+
                                '</div>'+
                                '<div class="form-group zero-padding new-password">'+
                                  '<label for="new-password">New password: </label>'+
                                  '<input type="text" class="form-control" id="new-password">'+
                                '</div>'+
                            '</div>'+
                          '</div>'+
                          '<div class="modal-footer">'+
                            '<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>'+
                            '<button type="button" class="btn btn-primary accept-button" id="ok">Ok</button>'+
                          '</div>'+
                        '</div>'+
                      '</div>'+
                    '</div>';
  
 var updatePictureModal = '<div id="update-picture-modal" class="modal fade" role="dialog">'+
                    '<div class="modal-dialog">'+
                      '<div class="modal-content">'+
                        '<div class="modal-header">'+
                          '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                        '</div>'+
                        '<div class="modal-body">'+
                          '<div class="modal-msg"> Please enter the file name for the image </div>'+
                          '<div class="container-fluid">'+
                                '<div class="form-group zero-padding profile-picture">'+
                                  '<label for="image-file">Upload Image </label>'+
                                  '<input type="file" class="form-control" id="image-file">'+
                                '</div>'+
                            '</div>'+
                          '</div>'+
                          '<div class="modal-footer">'+
                            '<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>'+
                            '<button type="button" class="btn btn-primary accept-button" id="ok">Ok</button>'+
                          '</div>'+
                        '</div>'+
                      '</div>'+
                    '</div>';  
});

