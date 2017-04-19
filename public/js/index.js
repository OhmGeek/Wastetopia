var lat = NaN;
var lng = NaN;
$(function(){
  //fancy select
  $(".js-example-basic-multiple").select2();

  $('.main-search-form').submit(function(e){
    e.preventDefault();
    var search = $('#search').val().trim()
    var postcode = $('#postcode').val().trim()
    getLatLng(postcode)
    console.log(lat)
    var quantity = $('#quantity').val().trim()
    var distance = $('#distance').val().trim()
    var exclude = []
    var include = []

    $('.main-search-form *').filter('.selects').each(function(){
      var formGroup = $(this)
      console.log(formGroup)
      if ( formGroup.find('.filter-label').data('filtertype') === 'negative' ) {
        formGroup.find('select option:selected').each(function(){
          var input = $(this)
          console.log(input.val())
          console.log('exclude')
          exclude.push(input.attr('id'))
        });
      } else {
        formGroup.find('select option:selected').each(function(){
          var input = $(this)
          console.log(input.val())
          console.log('include')
          exclude.push(input.attr('id'))
        });
      }
    });

    var baseURL = $('#baseURL').attr('href');
    var searchURL = baseURL + '/search/' + search + '/' + postcode + '/' + lat + '/' + long;

    $.post()

  })

  function getLatLng(postcode) {
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({
      componentRestrictions: {
        country: 'GB',
        postalCode: postcode
      }
    },
    function(results, status) {
      if(status === google.maps.GeocoderStatus.OK){
        lat = results[0].geometry.location.lat();
        console.log(lat)
        lng = results[0].geometry.location.lng();
      }
      else if (status === google.maps.GeocoderStatus.ZERO_RESULTS){
        window.alert("Could not find postcode");
      }
      else{
        window.alert("There was an error processing your request. \n Please try again later");
      }
    });
  }

})
