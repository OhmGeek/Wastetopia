$(function(){
  //fancy select
  $(".js-example-basic-multiple").select2();

$('.main-search-form').submit(function(e){
  e.preventDefault();
  var search = $('#search').val().trim()
  var postcode = $('#postocode').val().trim()
  var quantity = $('#quantity').val().trim()
  var distance = $('#distance').val().trim()
  var exclude = []
  var include = []

  $('.main-search-form *').filter('.form-group').each(function(){
    var formGroup = $(this)
    console.log(formGroup)
    if ( formGroup.find('.filter-label').data('filtertype') === 'negative' ) {
      formGroup.find('select').val().each(function(){
        var input = $(this)
        console.log(input.val())
        console.log('exclude')
        exclude.push(input.attr('id'))
      });
    } else {
      formGroup.find('select').val().each(function(){
        var input = $(this)
        console.log(input.val())
        console.log('include')
        exclude.push(input.attr('id'))
      });
    }
  });


})

})
