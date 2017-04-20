var $grid;
$(function () {

    //equal height rows
    $('.small').matchHeight();

    // Get last active tab and make it active if it's set (so user can relaod and come back to same place)
    var activeTab = localStorage.getItem('activeTab');
    console.log(activeTab);
    if (activeTab) {
      $('a[href="' + activeTab + '"]').trigger('click');
    }

    // Get baseURL for the site
    var baseURL = window.location.protocol + "//" + window.location.host;

    // init Isotope
    var url = window.location.protocol + "//" + window.location.host + "/" + "js/plugins/isotope/isotope.pkgd.min.js"
    $.getScript(url , function(){
      $('.grid').imagesLoaded().progress( function() {
        $grid = $('.grid').isotope({
          itemSelector: '.grid-item',
          percentPosition: true,
          layoutMode: 'masonry'
        });
        $grid.isotope('layout');
      });
    })

    // Displays an error message in the appropriate place
    function displayError(error) {

        // Create warning div
        var errorDiv = $("<div>").addClass("alert alert-danger fade in");

        // Add error to the div
        errorDiv.html('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + error)

        // Add alert to the alert div
        $("#errorMessage").html(errorDiv);
    }


    // Displays an error message in the appropriate place
    function displaySuccess(message) {
        var successDiv = $("<div>").addClass("alert alert-success fade in");

        // Add error to the div
        successDiv.html('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + message)

        // Add alert to the alert div
        $("#errorMessage").html(successDiv);
    }

    // Remove an element from the layout - ele is in the form $(element)
    function remove(ele) {
        // // init Isotope
        // var $grid = $('.grid').isotope({
        //     itemSelector: '.grid-item',
        //     percentPosition: true,
        //     masonry: {
        //         columnWidth: '.grid-sizer'
        //     }
        // });
        // remove clicked element (in a very skitchy way right now)
        $grid.isotope('remove', ele.closest('.grid-item'))
        // layout remaining item elements
            .isotope('layout');
    };

    // Given a sub tab (i.e "pending-3"), it updates the number by adding "value" to it (can be negative)
    function changeSubTabCounter(counter, value) {
        if (!(counter == null)) {
            var html = counter.html();
            var name = html.split("-")[0];
            var count = html.split("-")[1];
            var newCount = parseInt(count) + value;
            counter.html(name + "- " + newCount);
        }
    }

    function setPadding() {
      var neededPadding = $('.navbar').height() + $('.user-profile').height()
      $('#profileContentWrapper').css({'padding-top': neededPadding})
      console.log(neededPadding)
    }

    setPadding()

    $(window).resize(function () {
      setPadding()
      $grid.isotope('layout');
    })

    // Reload data in tabs when clicked - keeps everything up to date without reloading
    $(document).on('click', 'a[data-toggle="tab"]', function () {
        console.log("Trying to reload");
        var userID = $('.user-name').attr("id");
        var tabID = $(this).attr('href');
        var subTabID = "";
        var otherSubTab = "";

        // Choose which data to load
        var relativeURL = "";
        if (tabID == "#listings") {
            relativeURL = "load-listings-tab";
            tabID = "#listings"; // So it doesn't load Divs inside the other two tabs
        } else if (tabID == "#available-listing") {
            relativeURL = "load-listings-tab";
            subTabID = tabID;
            otherSubTab = "#out-of-stock-listing";
            tabID = "#listing"; // So it doesn't load Divs inside the other two tabs
        } else if (tabID == "#out-of-stock-listing") {
            relativeURL = "load-listings-tab";
            subTabID = tabID;
            otherSubTab = "#available-listing";
            tabID = "#listing"; // So it doesn't load Divs inside the other two tabs
        } else if (tabID == "#requests") {
            relativeURL = "load-requests-tab";
            tabID = "#requests";
        } else if (tabID == "#completed-request") {
            relativeURL = "load-requests-tab";
            subTabID = tabID;
            otherSubTab = "#pending-request";
            tabID = "#requests";
        } else if (tabID == "#pending-request") {
            relativeURL = "load-requests-tab";
            subTabID = tabID;
            otherSubTab = "#comleted-request";
            tabID = "#requests";
        } else if (tabID == "#offers") {
            relativeURL = "load-offers-tab";
            tabID = "#offers";
        } else if (tabID == "#completed-transaction") {
            console.log("Completed!");
            relativeURL = "load-offers-tab";
            subTabID = tabID;
            otherSubTab = "#pending-transaction";
            tabID = "#offers";
        } else if (tabID == "#pending-transaction") {
            relativeURL = "load-offers-tab";
            subTabID = tabID;
            otherSubTab = "#completed-transaction";
            tabID = "#offers";
        } else if (tabID == "#watchList") {
            relativeURL = "load-watchlist-tab";
        } else if (tabID == "#home") {
            relativeURL = "load-home-tab";
        } else {
            return;
        }

        // Load data into tab
        reloadTab(tabID, relativeURL, userID, subTabID, otherSubTab);

    });

    // Reloads the content of the given tab, for the given user, from the given relative URL
    // Gets HTML from baseURL/profile/relativeURL/userID

    function reloadTab(tabID, relativeURL, userID, subTabID, otherSubTab) {
        var url = baseURL + "/profile/" + relativeURL + "/" + userID;

        $.get(url, function (response) {

            var div = $(tabID);
            div.replaceWith(response);

            // Make main Tab active
            $(tabID).addClass("in active"); // Make it visible

            // Make correct Subtab active
            if (!(subTabID === "" )) {
                $(subTabID).addClass("in active"); // Make it visible
                $(subTabID).parent("li").addClass("active");
                $('a[href="' + subTabID + '"]').parent("li").addClass("active");
                $(otherSubTab).removeClass("in");
                $(otherSubTab).removeClass("active");
                $('a[href="' + otherSubTab + '"]').parent("li").removeClass("active");
            }

            // re initialize isotope
            $grid = $('.grid').isotope({
                itemSelector: '.grid-item',
                percentPosition: true,
                layoutMode: 'masonry'
            });
            $grid.isotope('layout');
        });
    }

    // Store last active tab
    $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        var tabHREF = $(e.target).attr('href');
        // If tab shown is one of the main ones
        if (tabHREF == "#home" || tabHREF == "#listings" || tabHREF == "#offers" || tabHREF == "#requests"
            || tabHREF == "#watchList" || tabHREF == "#analysis") {
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
        $('.user-profile .user-img').css('width', width);
        $('.user-profile .user-img').css('height', height);
        $('.user-profile .user-name').css('font-size', nameSize);
        $('.user-profile .popularity i').css('font-size', iconSize);
        $('.user-profile .popularity').css('font-size', iconSize);
        $('.page-height').css('padding-top', topPadding);

    });


    // Set all pending transactions to viewed
    $(document).on('shown.bs.tab', 'a[href="#pending-transaction"]', function () {
        console.log("Setting pending as viewed");
        var url = window.location.protocol + "//" + window.location.host + "/profile/set-pending-viewed";
        console.log(url);
        $.post(url, function (response) {
            // Do nothing
            console.log(response);
        })
    });

    // Delete a card from user's completed transactions (Requests Received and Requests Sent tabs)
    $(document).on('click', 'a[href="#delete"]', function (event) {
        event.preventDefault();

        // Get details - which tab, transactionID
        var card = $(this).closest('.thumbnail');
        var giverOrReceiver = $("#offers").hasClass("active");
        var transactionID = card.attr("id");
        var url = baseURL + "/profile/set-listing-transaction-hidden";
        var data = {giverOrReceiver: giverOrReceiver, transactionID: transactionID}
        // Send data to index.php
        $.post(url, data, function (response) {
            console.log(response);
            console.log("Done");
            if (response) {
                // Remove card from screen
                remove(card);

                // Get current sub tab
                var subTabID = giverOrReceiver ? "#completed-transaction" : "#completed-request";
                var counter = $('a[href="' + subTabID + '"]');

                // Take 1 off current completed tab
                changeSubTabCounter(counter, -1);
            }
        });
    });

    // Send to User's profile
    $(document).on('click', '.user-name', function (event) {
        event.preventDefault();
        var userID = $(this).attr("id");
        var url = baseURL + "/profile/user/" + userID;
        location.href = url;
    });


    // Lets user upload a new profile picture
    $(document).on('click', '#upload-picture', function (event) {
        event.preventDefault();
        var userID = $('.user-name').attr("id");

        // Set up Modal to get file from user
        $('body').append(updatePictureModal);

        $("#update-picture-modal").modal({backdrop: "static"})

        $("#update-picture-modal").on("shown.bs.modal", function () {
            // Do something?
        }).modal('show');

        // Add on click handler to "OK" button
        $("#update-picture-modal .accept-button").on('click', function () {
            // Get file
            var formdata = new FormData();
            formdata.append('image', $('#image-file')[0].files[0]);

            var url = baseURL + "/profile/change-profile-picture";

            $('#update-picture-modal').modal('hide');

            // Send file to url
            $.ajax({
                url: url,
                type: "POST",
                data: formdata,
                cache: false,
                processData: false,
                contentType: false,
                success: function (res) {
                    if (response) {
                        reloadTab("#home", "load-home-tab", userID, "", "");
                    }
                }
            });
        });

        // Remove the modal
        $('#update-picture-modal').on('hidden.bs.modal', function () {
            console.log("hidden");
            $('#update-picture-modal').remove();
        });
    });


    // Let user change their password
    $(document).on('click', '#change-password', function (event) {
        event.preventDefault();

        // Set up Modal with one input for old password, one input for new password

        $('body').append(changeModal);

        $("#change-modal").modal({backdrop: "static"})

        $("#change-modal").on("shown.bs.modal", function () {
            $(this).find('.modal-msg').text("Please enter your current password and new password.");
            $("#old-password").attr("type", "password");
            $("#new-password").attr("type", "password");
        }).modal('show');


        $("#change-modal .accept-button").on('click', function () {
            var button = $(this);
            var oldPassword = $('#old-password').val();// Get from modal
            var newPassword = $('#new-password').val();// Get from modal

            // Send to /items/renew-listing/
            $('#change-modal').modal('hide');

            var url = baseURL + "/profile/change-password";
            var data = {oldPassword: oldPassword, newPassword: newPassword};

            console.log(data);
            console.log(url);
            $.post(url, data, function (response) {
                console.log(response);
                var json = $.parseJSON(response);
                if (json.hasOwnProperty("error")) {
                    console.log("Error occurred");
                    displayError("Couldn't change password: " + json["error"]);
                    return;
                } else if (json.hasOwnProperty("success")) {
                    console.log("Successful");
                    displaySuccess("Password successfully changed");
                    //Reload page (logged out so should take user to login page)
                    return;
                } else {
                    displayError("WHAAAAT");
                    console.log("Something really went wrong");
                    return;
                }
            });
        });

        $('#change-modal').on('hidden.bs.modal', function () {
            console.log("hidden");
            $('#change-modal').remove();
        });
    });

    // Let user change their email - NEEDS MODAL
    $(document).on('click', '#change-email', function (event) {
        event.preventDefault();

        // Set up Modal with one input for old password, one input for new password
        $('body').append(changeModal);

        $("#change-modal").modal({backdrop: "static"})

        // Show modal and change labels to work with emails
        $("#change-modal").on("shown.bs.modal", function () {
            $(this).find('.modal-msg').text("Please enter your current email and new email.");
            $("#label1").html("Current email:");
            $("#label1").attr("for", "old-email");
            $("#label2").html("New email: ");
            $("#label2").attr("for", "new-email");
            $("#old-password").attr("type", "email");
            $("#old-password").attr("id", "old-email");
            $("#new-password").attr("type", "email");
            $("#new-password").attr("id", "new-email");
        }).modal('show');


        // Send details when "OK" button is pressed
        $("#change-modal .accept-button").on('click', function () {
            var button = $(this);
            var oldEmail = $('#old-email').val();// Get from modal
            var newEmail = $('#new-email').val();// Get from modal

            // Send to /items/renew-listing/
            $('#change-modal').modal('hide');

            var url = baseURL + "/profile/change-email";
            var data = {oldEmail: oldEmail, newEmail: newEmail};


            $.post(url, data, function (response) {
                var json = $.parseJSON(response);
                if (json.hasOwnProperty("error")) {
                    console.log("Error occurred");
                    displayError("Couldn't change email: " + json["error"]);
                    return;
                } else if (json.hasOwnProperty("success")) {
                    console.log("Successful");
                    displaySuccess("Email successfully changed");
                    //Reload page (logged out so should take user to login page)
                    return;
                } else {
                    displayError("WHAAAAT");
                    console.log("Something really went wrong");
                    return;
                }
            });
        });

        $('#change-modal').on('hidden.bs.modal', function () {
            console.log("hidden");
            $('#change-modal').remove();
        });
    });


    // $(document).on('click', '#addOffer', function(){
    //     // Send to add-item page
    //     return;
    // });


// MODALS USED
    var changeModal = '<div id="change-modal" class="modal fade" role="dialog">' +
        '<div class="modal-dialog">' +
        '<div class="modal-content">' +
        '<div class="modal-header">' +
        '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
        '</div>' +
        '<div class="modal-body">' +
        '<div class="modal-msg"></div>' +
        '<div class="container-fluid">' +
        '<div class="form-group zero-padding old-password">' +
        '<label id = "label1" for="old-password">Current password: </label>' +
        '<input type="text" class="form-control" id="old-password">' +
        '</div>' +
        '<div class="form-group zero-padding new-password">' +
        '<label id = "label2" for="new-password">New password: </label>' +
        '<input type="text" class="form-control" id="new-password">' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="modal-footer">' +
        '<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>' +
        '<button type="button" class="btn btn-primary accept-button" id="ok">Ok</button>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>';

    var updatePictureModal = '<div id="update-picture-modal" class="modal fade" role="dialog">' +
        '<div class="modal-dialog">' +
        '<div class="modal-content">' +
        '<div class="modal-header">' +
        '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
        '</div>' +
        '<div class="modal-body">' +
        '<div class="modal-msg"> Please enter the file name for the image </div>' +
        '<div class="container-fluid">' +
        '<div class="form-group zero-padding profile-picture">' +
        '<label for="image-file">Upload Image </label>' +
        '<input type="file" class="form-control" id="image-file">' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="modal-footer">' +
        '<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>' +
        '<button type="button" class="btn btn-primary accept-button" id="ok">Ok</button>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>';

});
