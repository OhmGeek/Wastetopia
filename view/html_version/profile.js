$(function () {
  // init Isotope
  var $grid = $('.grid').isotope({
    itemSelector: '.grid-item',
    percentPosition: true,
    masonry: {
      columnWidth: '.grid-sizer'
    }
  });

  $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    console.log($(e.target).attr("href"));
    var width = 150;
    var height = 150;
    var nameSize = 25;
    var iconSize = 35;
    var topPadding = 230;
    if ($(e.target).attr("href") != '#home') {
      width = 50;
      height = 50;
      nameSize = 16;
      iconSize = 20;
      topPadding = 130;
      $grid.isotope('layout');
    }
    $('.user-profile .user-img').css('width',width);
    $('.user-profile .user-img').css('height',height);
    $('.user-profile .user-name').css('font-size',nameSize);
    $('.user-profile .popularity i').css('font-size',iconSize);
    $('.user-profile .popularity').css('font-size',iconSize);
    $('.page-height').css('padding-top',topPadding);
  });

  $grid.on( 'click', '#delete', function() {
    // get the id of the item would be removed
    console.log($(this).closest('.caption').find('h3').text());
    var item = $(this)
    var itemName = item.closest('.caption').find('h3').text()
    var itemQuantity = item.closest('.caption').find('.trans-info .quantity').text()
    $("#complete-modal").modal({backdrop: "static"})
    $("#complete-modal").on("shown.bs.modal", function () {
             $(this).find('.item-name').html(itemName + '?')
             $(this).find('.rate-user').html('Rate Mark Smith ')
        }).modal('show');
    $("#complete-modal #complete-ok").on('click', function(){
      remove(item)
    })
  });

  function remove(ele) {
    // remove clicked element (in a very skitchy way right now)
    $grid.isotope( 'remove', $(ele).closest('.grid-item'))
    // layout remaining item elements
    .isotope('layout');
  };
});
