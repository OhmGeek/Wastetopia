$(function(){
    //fancy select
    $(".js-example-basic-multiple").select2();

    $('.main-search-form').submit(function(e){


        var search = $('#search').val().trim()
        var postcode = $('#postcode').val().trim()
        var quantity = $('#quantity').val().trim()
        var distance = $('#distance').val().trim()
        var sortBy = $('#sort').val()
        var selectedFilters = []

        $('.main-search-form *').filter('.selects').each(function(){
          var formGroup = $(this)
          formGroup.find('select option:selected').each(function(){
            var input = $(this)
            selectedFilters.push(input.attr('id'))
          });
        });

        var geocoder = new google.maps.Geocoder();

        geocoder.geocode( {'address':postcode, 'region':'GB'}, function(results, status){
            if(status === google.maps.GeocoderStatus.OK){
                var lat = results[0].geometry.location.lat();
                var lng = results[0].geometry.location.lng();
                var baseURL = $('#baseURL').attr('href');
                var searchURL = baseURL + '/search/' + search + '/' + postcode + '/' + lat + '/' + lng;

                $('.main-search-form').attr("action", searchURL)

                var newForm = '<form class="submit-form" action="'+ baseURL +'/search/'+ search +'/'+ postcode +'/'+ lat +'/'+ lng +'"> ' +
                              '<input id="postcode" type="text" value="'+ postcode +'"></input> <input id="quantity" type="number" value="'+ quantity +'"></input>' +
                              '<input id="distance" type="number" value="'+ distance +'"></input> <input id="filters" type="text" value="'+ selectedFilters.join('+') +'"></input>' +
                              '<input id="sort" type="text" value="'+sortBy+'"></input> </form>"'


                $('.secondary-search-form').html(newForm)
                $('.submit-form').submit();

            }
            else if (status === google.maps.GeocoderStatus.ZERO_RESULTS){
                window.alert("Could not find postcode");
            }
            else{
            window.alert("There was an error processing your request. \n Please try again later");
            }
        });
        return false;
    });
});