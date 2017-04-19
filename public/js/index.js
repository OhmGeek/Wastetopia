$(function(){
  //fancy select
  $(".js-example-basic-multiple").select2();

  $('.main-search-form').submit(function(e){
    e.preventDefault();
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

    console.log(selectedFilters.join('+'))

    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({
      componentRestrictions: {
        country: 'GB',
        postalCode: postcode
      }
    },
    function(results, status) {
      if(status === google.maps.GeocoderStatus.OK){
        var lat = results[0].geometry.location.lat();
        var lng = results[0].geometry.location.lng();
        var baseURL = $('#baseURL').attr('href');
        var searchURL = baseURL + '/search/' + search + '/' + postcode + '/' + lat + '/' + lng;
        $.post(searchURL, { filter : selectedFilters.join('+'), sort : sortBy}, function(event, status){
          if (status === "success") {
            location.href = searchURL
          }else {
            window.alert("Request could not be completed. Try again later.")
          }
        })
      }
      else if (status === google.maps.GeocoderStatus.ZERO_RESULTS){
        window.alert("Could not find postcode");
      }
      else{
        window.alert("There was an error processing your request. \n Please try again later");
      }
    });

  })

})
