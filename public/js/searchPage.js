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




    var searchBox = $('#searchBox')
    var searchTerm = $('#searchTerm').attr("data-searchTerm");

    if(searchTerm !== "")
    {
        searchBox.val(searchTerm);
    }

            





  function refreshPage() {

    var include = [];
    var exclude = [];
    var searchTerm = $('#searchTerm').attr("data-searchTerm");
    var lat = "";
    var long = "";
    

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
    var query = baseURL + '/api/search/page/' + lat + '/' + long + '/' + searchTerm + '/' + include.join('+') + '/' + exclude.join('+') + '/' + '0';

    $.ajax({
        url: query,
        success: function(result){
            if(result === "")
            {
                noResults();
            }
            else
            {
                displayPage(result);
            }
        }
    });

    function displayPage(result)
    {
        var json = JSON.parse(result);

        var html = `<div class="grid-sizer col-xs-12 col-sm-6 col-md-4"></div>`;
        json.forEach(function(element){
            console.log(element);
            var cardHTML = `
            <div class="grid-item col-xs-12 col-sm-6 col-md-4">
                <div class="thumbnail zero-padding" id="`+ element.ListingID +`">
                  <div class="caption">
                    <div class="centerAll">
                      <img src="`+ element.Picture_URL +`" class="user-image"/>
                      <div class="user-details">
                        <a class="user-name" href="#`+ element.UserID +`">
                          `+ element.Forename + ` ` + element.Surname +`
                        </a>
                        <div class="added-date">
                          `+ element.Time_Of_Creation +`
                        </div>
                      </div>
                      <div class="road-distance">
                        <i class="material-icons">place</i> `+ element.Post_Code +`
                      </div>
                    </div>
                  </div>
                  <img src="`+ element.Default_Image_URL +`" style="border-color: lightgrey;" />
                  <div class="caption">
                    <h3>`+ element.Name +`</h3>
                    <div class="trans-info">
                      <div><span>Quantity: </span>`+ element.Quantity +`</div>
                    </div>
                    <div class="nav-btns">
                      <a href="#view" class="btn btn-primary" role="button" id="`+ element.ListingID +`">View</a>
                      {% if isRequesting %}
                        <a href="#cancel-by-listing" class="btn btn-default" role="button">Cancel request</a>
                      {% else %}
                        <a href="#request" class="btn btn-default" role="button">Request</a>
                      {% endif %}
                      <a href="#watch" role="button" class="btn-watch {% if isWatching %} watched {% endif %}" id="{{item.listingID}}"><i class="material-icons">visibility</i></a>
                    </div>
                  </div>
                </div>
            </div>`;

            html += cardHTML;

        });

    $('.grid').html(html);

    }

    function noResults()
    {
        
    }



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

  refreshPage();
});


