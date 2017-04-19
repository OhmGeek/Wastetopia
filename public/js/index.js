$(function(){
  //fancy select
  $(".js-example-basic-multiple").select2();

  $('.main-search-form').submit(function(e){
    e.preventDefault();
    var search = $('#search').val().trim()
    var postcode = $('#postcode').val().trim()
    var position = getLatLng(postcode)
    console.log(position)
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

    var url =

    $.post()

  })

  function getLatLng(postcode) {
    var geocoder = new google.maps.Geocoder();
    var latlng = geocoder.geocode({
      componentRestrictions: {
        country: 'GB',
        postalCode: postcode
      }
    },
    function(results, status) {
      if (status === google.maps.GeocoderStatus.OK) {
          // return a location object
          var latlng = results[0].geometry.location;
          var locationFinal = {
              'lat': latlng.lat(),
              'long': latlng.lng()
          };
        return locationFinal;
      } else {
        alert("geocode of " + $('#postcode').val() + " failed:" + status);
      }
    });
    return latlng;
  }

})
