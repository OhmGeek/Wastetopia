$(function () {
  var radiusSlider = document.getElementById('radius');
  var radiusFormat = wNumb({ decimals: 0, postfix: 'km' })

  noUiSlider.create(radiusSlider, {
    range: {
      'min': 5,
      'max': 50
    },
    connect: [true, false],
    step: 5,
    start: 5
  });

  $('.slider').css({background:'#31353D'})

  var filters = "";
  // filter with selects and checkboxes
  var $checkboxes = $('#filter-list input');

  function getFilters(){
    var radius = radiusFormat.to(parseFloat(radiusSlider.noUiSlider.get()));
    filters = '<span class="label label-primary"> within ' + radius + ' radius </span>'
    $('#radius-output').html('radius: <span>' + radius + '</span>')
    // inclusive filters from checkboxes
    $checkboxes.each( function( i, elem ) {
      // if checkbox, use value if checked
      if ( elem.checked ) {
        filters += '<span class="label label-primary">' + elem.value + '</span>';
      }
    });
    $('#filters-picked').html(filters);
  }

  getFilters();

  radiusSlider.noUiSlider.on('update', function (e) {
    getFilters();
  });

  var $grid = $('.grid').isotope({
    itemSelector: '.grid-item',
    percentPosition: true,
    masonry: {
      columnWidth: '.grid-sizer'
    }
  });

  $('#filter-list').on('click', '.filter-category a', function(e){
    $(this).closest('.btn-group').addClass('dontClose');
  })










  function refreshPage() {

    var include = [];
    var exclude = [];
    var searchTerm = $('#searchTerm').attr("data-searchTerm");
    var lat;
    var long;
    

    $('#filter-form *').filter('.tab').each(function(){
        var tab = $(this)
        if ( tab.find('.filter-category').data('filtertype') === 'negative' )
        {
            tab.find('.filter-options *').filter(':input').each(function(){
                var input = $(this)
                if (input.prop('checked') === true)
                {
                    exclude.push(input.attr('id'))
                }
            });
        }
        else
        {
            tab.find('.filter-options *').filter(':input').each(function(){
                var input = $(this)
                if (input.prop('checked') === true)
                {
                    include.push(input.attr('id'))
                }
            });
        }
    });

    var baseURL = $('#baseURL').attr('href');

    $.ajax({
        url: baseURL + '/api/search/page/' + lat + '/' + long + '/' + searchTerm + '/' + include.join('+') + '/' + exclude.join('+') + '/' + '0'
    });



  }

  $('#filter-form').change(function(){
      refreshPage();
  });

























  $checkboxes.change( function(e) {
    getFilters();    
  });

  $('#filter-dropdown').on('hide.bs.dropdown', function(e) {
    if ( $(this).hasClass('dontClose') ){
      e.preventDefault();
      $('#filter-dropdown > button').addClass('white')
    } else {
      $('#filter-dropdown > button').removeClass('white')
    }
    $(this).removeClass('dontClose');
  });
});



