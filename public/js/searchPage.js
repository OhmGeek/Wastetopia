// setting the global variables
var $grid;
var maxItems = 30;
// set the current number of items
var itemsNum;
var mapURL;
var lat = 54.7754719;
var long = -1.57694200;

$(function () {

  function filterHeight(){
    var dropdownHeight = $(window).height() - $('.btn').outerHeight() - $('.navbar').height()
		console.log(dropdownHeight)
		$('#filter-list').css({'max-height': dropdownHeight})
  }

  filterHeight()

  $(window).resize(function () {
		filterHeight()
	});

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

  var quantitySlider = document.getElementById('quantity');
  var quantityFormat = wNumb({ decimals: 0, postfix: '+' })

  noUiSlider.create(quantitySlider, {
    range: {
      'min': 0,
      'max': 10
    },
    connect: [true, false],
    step: 1,
    start: 1
  });

  $('.slider').css({background:'#31353D'})



  var searchVariables = JSON.parse($('#searchTerm').attr("data-searchTerm"));
  /*SET VARIABLES FROM ADVANCED SEARCHS*/
  if(searchVariables.advancedSearch === true){
      setAdvancedSearchVariables(searchVariables);
  }


  var filters = "";
  // filter with selects and checkboxes
  var $checkboxes = $('#filter-list input');

  function getFilters(){
    var radius = radiusFormat.to(parseFloat(radiusSlider.noUiSlider.get()));
    var quantity = parseFloat(quantitySlider.noUiSlider.get())
    if (quantity > 9) {
      quantity = quantityFormat.to(parseFloat(quantitySlider.noUiSlider.get()));
    }
    filters = '<span class="label label-primary"> within ' + radius + ' radius </span>' + '<span class="label label-primary"> quantity ' + quantity + ' </span>'
    $('#radius-output').html('radius: <span>' + radius + '</span>')
    $('#quantity-output').html('quantity: <span>' + quantity + '</span>')
    // inclusive filters from checkboxes
    $checkboxes.each( function( i, elem ) {
      // if checkbox, use value if checked
      if ( elem.checked ) {
        filters += '<span class="label label-primary">' + elem.value + '</span>';
      }
    });
    $('#filters-picked').html(filters);
  }

  function setCheckbox(val){
    $checkboxes.each( function( i, elem ) {
      // if checkbox, use value if checked
      console.log(elem.value)
      if ( elem.value === val ) {
        $(elem).prop('checked', true)
      }
    });
  }

  getFilters();

  radiusSlider.noUiSlider.on('update', function (e) {
    getFilters();
  });

  quantitySlider.noUiSlider.on('update', function (e) {
    getFilters();
  });

  $grid = $('.grid').isotope({
    itemSelector: '.grid-item',
    percentPosition: true,
    layoutMode: 'masonry'
  });

  $('.grid').imagesLoaded().progress( function() {
        $grid.isotope('layout');
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

  /*RESET SEARCH BOX VALUES*/

    var searchBox = $('#searchBox');
    var postBox = $('#postcodeBox');

    var searchDetails = JSON.parse($('#searchTerm').attr("data-searchTerm"));
    var searchTerm = searchDetails.search;
    var postcode = searchDetails.postcode;

    if(searchTerm !== "")
    {
        searchBox.val(searchTerm);
    }
    if(postcode !== "")
    {
        postBox.val(postcode);
    }

    /*Set Detfault Search Values*/
    var include = [];
    var exclude = [];

    if((searchDetails.lat !== '') && (searchDetails.long !== ''))
    {
        lat = searchDetails.lat;
        long = searchDetails.long;
    }

    var sortOrder = ''
    var distanceLimit = 50;

    var pageNumber = 0;




  function refreshPage() {
    include = [];
    exclude = [];

    sortOrder = $('#sort-options').val();
    distanceLimit = parseFloat(radiusSlider.noUiSlider.get());

    $('.grid').html('<div class="grid-item col-xs-12"><h3 style="text-align:center;">Loading</h3></div>');


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
    var query = baseURL + '/api/search/page/' + lat + '/' + long + '/' + searchTerm + '/' + include.join('+') + '/' + exclude.join('+') + '/' + distanceLimit + '/' + pageNumber + '/' + sortOrder;
    mapURL = window.location.protocol + "//" + window.location.host + '/api/search/map/' + lat + '/' + long + '/' + searchTerm + '/' + include.join('+') + '/' + exclude.join('+') + '//';
    if ($('#map-tab').hasClass('active')) {
      console.log('map is active')
      initMap();
    }
    $.ajax({
        url: query,
        success: function(result){
            if(result === '[]')
            {
                noResults();
            }
            else
            {
                displayPage(result);
            }
        }
    });
  }

  $('#filter-form').change(function(){
    refreshPage();
  });

  $('#sort-options').change(function(){
    refreshPage();
  });

  radiusSlider.noUiSlider.on('change', function(){
    refreshPage();
  });

  refreshPage();


/*INFINITE SCROLLING*/



// for infinite scrolling
// TODO add the ajax request

  var infiniteScrollingEnabled = true;
  $(window).scroll(function(){

    if(infiniteScrollingEnabled){
        var scrollTop = $(window).scrollTop();
        var windowHeight = $(window).height();
        var docuHeight = $(document).height();

        if(scrollTop + windowHeight == docuHeight){
            infiniteScrollingEnabled = false;
            pageNumber += 1;


            var baseURL = $('#baseURL').attr('href');
            var query = baseURL + '/api/search/page/' + lat + '/' + long + '/' + searchTerm + '/' + include.join('+') + '/' + exclude.join('+') + '/' + distanceLimit + '/' + pageNumber + '/' + sortOrder;

            $.ajax({
                url: query,
                success: function(result){
                    if(result === '[]'){}
                    else
                    {
                        var count = addPage(result);
                        if (count === 30) {
                            infiniteScrollingEnabled = true;
                        }
                    }
                }
            });
        }
    }


  });

  $('#btn-map').on('click', function(event){
    $('#btn-grid').removeClass('hide')
    $('#btn-map').addClass('hide')
  })

  $('#btn-grid').on('click', function(event){
    $('#btn-map').removeClass('hide')
    $('#btn-grid').addClass('hide')
  })

  $('#btn-map').on('shown.bs.tab', function(event){
    console.log('map part appeared')
    mapURL = window.location.protocol + "//" + window.location.host + '/api/search/map/' + lat + '/' + long + '/' + searchTerm + '/' + include.join('+') + '/' + exclude.join('+') + '//';
    initMap();
    var height = $('.search-header').outerHeight() + 70
    $('#map-tab .warning').css({'top': height})
  });
});



function displayPage(result)
{
    var json = JSON.parse(result);

    var html = ""
    json.forEach(function(element){

        html += getHTML(element);


    });

    $('.grid').html(html);

}

function addPage(result){

    var json = JSON.parse(result);

    var count = Object.keys(json).length;

    var html = ""
    json.forEach(function(element){

        html += getHTML(element);

    });
    $('.grid').append(html);

    return count;
}

function noResults()
{
    var html = '<div class="grid-item col-xs-12"><h3 style="text-align:center;">No Items Found</h3></div>'


    $('.grid').html(html);
}


function getHTML(element){
    var cardHTML = '' +
        '<div class="grid-item col-xs-12 col-sm-4 col-md-4 col-lg-3">' +
            '<div class="thumbnail zero-padding" id="'+ element.ListingID +'">' +
              '<div class="caption">' +
                '<div class="centerAll">' +
                  '<img src="'+ element.Picture_URL +'" class="user-image"/>' +
                  '<div class="user-details">' +
                    '<a class="user-name" href="#'+ element.UserID +'">' +
                      element.Forename + ' ' + element.Surname +
                    '</a>' +
                    '<div class="added-date">' +
                       element.Time_Of_Creation +
                    '</div>' +
                  '</div>' +
                  '<div class="road-distance">' +
                    '<i class="material-icons">place</i> '+ element.Post_Code +
                  '</div>' +
                '</div>' +
              '</div>' +
              '<img src="'+ element.Image_URL +'" style="border-color: lightgrey;" />' +
              '<div class="caption">' +
                '<h3>'+ element.Name +'</h3>' +
                '<div class="trans-info">' +
                  '<div><span>Quantity: </span>'+ element.Quantity +'</div>' +
                '</div>' +
                '<div class="nav-btns">' +
                  '<a href="#view" class="btn btn-primary view" role="button" id="'+ element.ListingID +'">View</a>';

                  if (element.isRequesting){
                      cardHTML += '<a href="#cancel-by-listing" class="btn btn-default" role="button">Cancel request</a>';
                  }
                  else {
                      cardHTML += '<a href="#request" class="btn btn-default" role="button">Request</a>';
                  }
                  if (element.isWatching){
                      cardHTML += '<a href="#watch" role="button" class="btn-watch watched" id="'+ element.ListingID +'"><i class="material-icons">visibility</i></a>';
                  }
                  else{
                      cardHTML += '<a href="#watch" role="button" class="btn-watch" id="'+ element.ListingID +'"><i class="material-icons">visibility</i></a>';
                  }

    cardHTML +=  '</div>'+
              '</div>'+
            '</div>'+
        '</div>';

    return cardHTML;
}

function setAdvancedSearchVariables(searchVariables){

    var filters = searchVariables.filters.split('+');

    $('#filter-form *').filter(':input').each(function(){

        if($.inArray($(this).attr('id'), filters) !== -1)  {
            $(this).prop('checked', true);
        }
    });

    var sort = searchVariables.sortOption;
    var sortSelector = $('#sort-options')

    if(sort !== null){
        switch(sort) {
    case 'D':
        sortSelector.val('D')
        break;
    case 'AZ':
        sortSelector.val('AZ')
        break;
    case 'ZA':
        sortSelector.val('ZA')
        break;
    case 'UR':
        sortSelector.val('UR')
        break;
    default:
        sortSelector.val('D')
}
    }
}
