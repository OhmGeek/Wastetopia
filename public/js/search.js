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

  $(window).scroll(function(){

        var scrollTop = $(window).scrollTop();
        var windowHeight = $(window).height();
        var docuHeight = $(document).height();

        if(scrollTop + windowHeight == docuHeight){

            $('body').append('<div id="temp-load"</div>');

            page += 1;

            $('#temp-load').load('pages/page' + page + '.html #content', function(){

                $('#temp-load > #content').children().css({
                    opacity: 0
                }); 

                var toAdd = $('#temp-load > #content').html();

                $container.isotope('insert', $(toAdd), function(){

                    $container.children().css({
                    opacity: 1
                    });

                    $('#temp-load').remove();

                });

            });

        }

    });
});
