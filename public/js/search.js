
// 54.7754719 -1.576942000000031 <- durham

function searchSubmit(event){
        event.preventDefault();
        var searchBox = $('#searchBox');
        var postBox = $('#postcodeBox');
        var baseURL = $('#baseURL').attr('href');

        var lat = 51.496715;
        var long = -0.1765;

        var geocoder = new google.maps.Geocoder();
        var address = postBox.val();
        var region = 'GB';

        var postcode = postBox.val().replace(/ /g, '');
        if(postcode === "" || postcode.length < 6 || postcode.length > 7)
        {
            alert("Please enter a valid postcode");
        }
        else
        {
            geocoder.geocode( {'address':address, 'region':region}, function(results, status){
                if(status === google.maps.GeocoderStatus.OK){
                    var search = searchBox.val();
                    var postcode = postBox.val();
                    var lat = results[0].geometry.location.lat();
                    var long = results[0].geometry.location.lng();
                    location.href = baseURL + '/search/' + search + '/' + postcode + '/' + lat + '/' + long;
                }
                else if (status === google.maps.GeocoderStatus.ZERO_RESULTS){
                    window.alert("Could not find postcode");
                }
                else{
                    window.alert("There was an error processing your request. \n Please try again later");
                }
            });
        }
        
        return false;
    }