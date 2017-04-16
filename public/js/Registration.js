/**
 * Created by Stephen on 09/04/2017.
 */
$(document).ready(function(){

    // Check all details are valid
    // Then send to "/register/add-user
    // Show error message or success message when done
    $("#submit").click(function(event){
        event.preventDefault();

        var firstName = $("#firstName").val();
        var lastName = $("#lastName").val();
        var email = $("#email").val();
        var passwd = $("#pwd").val();
        var passwdConfirm = $("#pwdConfirm").val();
        var tAndC = $("#termsAndConditions");
        //var picture = $("#profilePic").val();

        // Check all the necessary fields are filled in
        if(firstName == ""){
            $('#firstName').parent().addClass('there-error')
            displayError("First name field must be filled in");
            return;
        }
        if(lastName == ""){
            $('#lastName').parent().addClass('there-error')
            displayError("Last name field must be filled in");
            return;
        }
        if(email == ""){
            $('#email').parent().addClass('there-error')
            displayError("Email field must be filled in");
            return;
        }

        if(passwd == ""){
            $('#pwd').parent().addClass('there-error')
            displayError("Password field must be filled in");
            return;
        }

        if(passwd.length < 8){
            $('#pwd').parent().addClass('there-error')
            displayError("Password must be at least 8 characters in length");
            return;
        }

        // Check the passwords match
        if (!(checkPassword(passwd, passwdConfirm))){
            $('#pwdConfirm').parent().addClass('there-error')
            displayError("Passwords don't match");
            return;
        }

        // Check email is valid
        if(!(checkEmail(email))){
            $('#email').parent().addClass('there-error')
            displayError("Email is not in valid form");
            return;
        }

        // Check the Terms and Conditions are checked
        if (!(tAndC.is(":checked"))){
            $('#termsAndConditions').parent().addClass('there-error')
            displayError("Must accept terms and conditions to submit");
            return;
        }


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
               displaySuccess("Verificaiton email has been sent");
               return;
            }else{
               displayError("WHAAAAT");
               console.log("Something really went wrong");
               return;
           }
        });

    });

    // Returns true if the passwords match
    function checkPassword(p, c){
        if (p === c){
            //console.log("Passwords match");
            return true;
        }else{
            //console.log("Passwords don't match");
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
        // Create warning div
        var errorDiv = $("<div>").addClass("alert alert-danger fade in");
    
        // Add error to the div
       errorDiv.html('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+ error)
        
        // Add alert to the alert div
        $("#errorMessage").html(errorDiv);
     }
    
     // Displays an error message in the appropriate place
    function displaySuccess(message){
        var successDiv = $("<div>").addClass("alert alert-success fade in");
    
        // Add error to the div
        successDiv.html('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+ message)
        
        // Add alert to the alert div
        $("#errorMessage").html(successDiv);
     }

    $("#firstName, #lastName, #pwd, #pwdConfirm, #email").click(function(){
        if($(this).val() == ""){
          $(this).closest(".form-group").addClass("there-error");
       }
    });

    // Checks the input field is filled
    $("#firstName, #lastName").keyup(function(){
        if($(this).val() == ""){
            // Field is empty associated box/symbol is red
            $(this).closest(".form-group").addClass('there-error');
        }else{
            // Field is filled, associated box/symbol is green
            $(this).closest(".form-group").removeClass('there-error');
        }
    });

   // Checks the input field is filled
    $("#pwd").keyup(function(event){
        if($(this).val().length < 8){
            
            // Field is empty associated box/symbol is red 
            $(this).closest(".form-group").addClass('there-error');
            
            // Check values again
            if ($(this).val() != $("#pwdConfirm").val()){
                $("#pwdConfirm").closest(".form-group").addClass('there-error');                
            }
        }else{
            // Field is filled, associated box/symbol is green
            $(this).closest(".form-group").removeClass('there-error');
            
            // Check values again
            if ($(this).val() == $("#pwdConfirm").val()){
                $("#pwdConfirm").closest(".form-group").removeClass('there-error');                
            }else{
                $("#pwdConfirm").closest(".form-group").addClass('there-error');   
            }
        }
    });

    $("#pwdConfirm").keyup(function(event){
        var password = $("#pwd").val();
        var passwordConfirm = $(this).val();
        if ((password.length >= 8) && checkPassword(password, passwordConfirm)){
           // Passwords are filled and match, make box/symbol green
           $(this).parent().removeClass('there-error')
        }else{
           // Passwords don't match, make box/symbol red
           $(this).parent().addClass('there-error')
        }
    });

    $("#email").keyup(function(){
        var email = $(this).val();

        if (email != ""){
            if (checkEmail(email)){
              // Set box/symbol to green
              $(this).parent().removeClass('there-error')
            }else{
              // Set box/symbol to red
               $(this).parent().addClass('there-error')
            }
        }else{
           // Set box/symbol to red
           $(this).parent().addClass('there-error')
        }
    });

});
