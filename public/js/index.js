$(function(){
  //fancy select
  $(".js-example-basic-multiple").select2();

$('.main-search-form').submit(function(e){
  e.preventDefault();
  var search = $('#search').val().trim()
  var postcode = $('#postcode').val().trim()
  var quantity = $('#quantity').val().trim()
  var distance = $('#distance').val().trim()
  var exclude = []
  var include = []

  $('.main-search-form *').filter('.selects').each(function(){
    var formGroup = $(this)
    console.log(formGroup)
    if ( formGroup.find('.filter-label').data('filtertype') === 'negative' ) {
      var selectOptions = formGroup.find('select').val() || []
      selectOptions.each(function(){
        var input = $(this)
        console.log(input.val())
        console.log('exclude')
        exclude.push(input.attr('id'))
      });
    } else {
      var selectOptions = formGroup.find('select').val() || []
      selectOptions.each(function(){
        var input = $(this)
        console.log(input.val())
        console.log('include')
        exclude.push(input.attr('id'))
      });
    }
  });


})

})
