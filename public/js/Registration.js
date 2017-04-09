/**
 * Created by Stephen on 09/04/2017.
 */
$(document).ready(function(){
    //TODO: Fix issue with password bars going green when they should be red
    //TODO: fix issue with email section going green but then going red when you press submit
    //TODO: fix issue with page reloading if eveything is filled in but passwords don't match


    $("#submit").click(function(){
        var firstName = $("#firstName").val();
        var lastName = $("#lastName").val();
        var email = $("#email").val();
        var passwd = $("#pwd").val();
        var passwdConfirm = $("#pwdConfirm").val();
        var tAndC = $("#termsAndConditions");
        //var picture = $("#profilePic").val();

        // Check all the necessary fields are filled in
        if(firstName == ""){
            $('#firstName').parent().addClass('has-error')
            displayError("First name field must be filled in");
            return;
        }
        if(lastName == ""){
            $('#lastName').parent().addClass('has-error')
            displayError("Last name field must be filled in");
            return;
        }
        if(email == ""){
            $('#email').parent().addClass('has-error')
            displayError("Email field must be filled in");
            return;
        }

        if(passwd == ""){
            $('#pwd').parent().addClass('has-error')
            displayError("Password field must be filled in");
            return;
        }

        if(passwd.length < 8){
            $('#pwd').parent().addClass('has-error')
            displayError("Password must be at least 8 characters in length");
            return;
        }

        // Check the passwords match
        if (!(checkPassword(passwd, passwdConfirm))){
            $('#pwdConfirm').parent().addClass('has-error')
            displayError("Passwords don't match");
            return;
        }

        // Check email is valid
        if(!(checkEmail(email))){
            $('#email').parent().addClass('has-error')
            displayError("Email is not in valid form");
            return;
        }

        // Check the Terms and Conditions are checked
        if (!(tAndC.is(":checked"))){
            $('#termsAndConditions').parent().addClass('has-error')
            displayError("Must accept terms and conditions to submit");
            return;
        }

        // Get pictureURL from pictureDIV (put there by another controller when user uploads a picture??)
        //var pictureURL = picture.val() == "" ? null : picture.val();

        // Create dictionary of data
        var data = {forename:firstName, surname:lastName, password:passwd, passwordConfirm: passwdConfirm, email:email};//, pictureURL: pictureURL};
        // Create url
        var url = window.location.protocol + "//" + window.location.host + "/register/add-user";

        $.post(url, data, function(json){
            console.log(json);
            var json = $.parseJSON(json);
           // If there was an error, display it
           if(json.hasOwnProperty("error")){
               console.log("Error occurred");
                displayError(json["error"]);
                return;
           }else if(json.hasOwnProperty("success")){
               console.log("Successful");
             // User added successfully
             // Reload page?? OR Send to Home page??
             // location.href = "#";
            }else{
               displayError("WHAAAAT");
               console.log("Something really went wrong");
           }
        });

    });

    // Returns true if the passwords match
    function checkPassword(p, c){
        if (p === c){
            console.log("Passwords match");
            return true;
        }else{
            console.log("Passwords don't match");
            return false;
        }
    }

    // Returns True if email valid
    function checkEmail(email){
     // Still to do
     var re = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i
     return re.test(email);
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
    //     $("#errorMessage").append($errorDiv);
     }

    $("#firstName, #lastName, #pwd, #pwdConfirm, #email").click(function(){
        if($(this).val() == ""){
          $(this).parent().addClass("has-error");
       }
    });

    // Checks the input field is filled
    $("#firstName, #lastName").keydown(function(){
        if($(this).val() == ""){
            // Field is empty associated box/symbol is red
            $(this).parent().addClass('has-error');
        }else{
            // Field is filled, associated box/symbol is green
            $(this).parent().removeClass('has-error');
        }
    });

   // Checks the input field is filled
    $("#pwd").keydown(function(){
        console.log($(this).val());
        console.log($(this).val().length);
        if(($(this).val().length) < 8){
            console.log("Password red");
            // Field is empty associated box/symbol is red
            console.log($(this).parent())
            $(this).parent().addClass('has-error');
        }else{
            // Field is filled, associated box/symbol is green
            console.log("Password green");
            $(this).parent().removeClass('has-error');
        }
    });

    $("#pwdConfirm").keydown(function(){
        var password = $("#pwd").val();
        var passwordConfirm = $(this).val();
        console.log(password);
        console.log(passwordConfirm);
        console.log(password.length<8);
        if ((password.length < 8) && checkPassword(password, passwordConfirm)){
            console.log("PC green");
            console.log($(this).parent())
           // Passwords are filled and match, make box/symbol green
           $(this).parent().removeClass('has-error')
        }else{
            console.log("PC red");
            console.log($(this).parent())
           // Passwords don't match, make box/symbol red
           $(this).parent().addClass('has-error')
        }
    });

    $("#email").keydown(function(){
        var email = $(this).val();

        if (email != ""){
            if (checkEmail(email)){
              // Set box/symbol to green
              $(this).parent().removeClass('has-error')
            }else{
              // Set box/symbol to red
               $(this).parent().addClass('has-error')
            }
        }else{
           // Set box/symbol to red
           $(this).parent().addClass('has-error')
        }
    });

});
