/**
 * Created by Stephen on 09/04/2017.
 */
$(document).ready(function(){

    // // Should use separate alert box for this, or just colour box next to passwordConfirm box
    // $('#pwd, #pwdConfirm').on('keyup', function () {
    //     if ($('#pwd').val() == $('#pwdConfirm').val()) {
    //         $('#errorMessage').html('Matching').css('color', 'green');
    //     } else
    //         $('#errorMessage').html('Not Matching').css('color', 'red');
    // });

    //TODO: Add real-time checking of fields (especially password match)
    // Can add a coloured box/line next to/under each field, change the colour when the field is filled in

    //TODO: Disable Submit button until all checks are passed

    $("#submit").click(function(){
        var firstName = $("#firstName").val();
        var lastName = $("#lastName").val();
        var email = $("#email").val();
        var passwd = $("#pwd").val();
        var passwdConfirm = $("#pwdConfirm").val();
        var tAndC = $("#termsAndConditions");
        var picture = $("#profilePic").val();

        // Check all the necessary fields are filled in
        if(firstName == ""){
            displayError("First name field must be filled in");
            return;
        }
        if(lastName == ""){
            displayError("Last name field must be filled in");
            return;
        }
        if(email == ""){
            displayError("Email field must be filled in");
            return;
        }
        if(passwd == ""){
            displayError("Password field must be filled in");
            return;
        }

        // Check the passwords match
        if (!(checkPassword(passwd, passwdConfirm))){
            displayError("Passwords don't match");
            return;
        }

        // Check the Terms and Conditions are checked
        if (!(tAndC.is(":checked"))){
            displayError("Must accept terms and conditions to submit");
            return;
        }

        // Get pictureURL from pictureDIV (put there by another controller when user uploads a picture??)
        var pictureURL = picture.val() == "" ? null : picture.val();

        // Create dictionary of data
        var data = {forename:firstName, surname:lastName, password:passwd, passwordConfirm: passwdConfirm, email:email, pictureURL: pictureURL};
        // Create url
        var url = window.location.protocol + "//" + window.location.host + "/register/add-user";

        $.post(url, data, function(json){
           // If there was an error, display it
           if(json.hasOwnProperty("error")){
               console.log("Error occurred");
                displayError(json["error"]);
                return;
           }else if(json.hasOwnProperty("success")){
               console.log("Successful");
             // User added successfully
             // Reload page?? OR Send to Home page??
              location.href = "";
            }else{
               displayError("WHAAAAT");
               console.log("Something really went wrong");
           }
        });

    });


    // What is this for??
    $(function () {
        $.material.init();
    });


    // Returns true if the passwords match
    function checkPassword(p, c){
        if (p === c){
            return true;
        }else{
            return false;
        }
    }

    // Displays an error message in the appropriate place
    function displayError(error){
        // Change HTML in an existing DIV
        console.log("Displaying error message");
        $("#errorMessage").html("<p>"+error+"<p>");

        // OR using bootstrap alerts
    //     // Create warning div
    //     $errorDiv = $("<div>").addClass("alert-warning");
    //
    //     // Add error to the div
    //     $errorDiv.text(error);
    //
    //     // Add alert to the alert div
    //     $("#alerts").append($errorDiv);
     }

});


