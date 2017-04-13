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

    $('body').append(renewModal);

    $("#renew-modal").modal({backdrop: "static"})

    //fancy datetime picker
    $('#renew-modal #renew-date').bootstrapMaterialDatePicker({ format : 'D MMMM, YYYY', weekStart : 0, time: false })

    $("#renew-modal").on("shown.bs.modal", function () {
      $(this).find('.item-name').html(itemName)
    }).modal('show');

    $("#renew-modal #justRenew").on('click', function(){
      remove(item)
    })
    $('#renew-modal').on('hidden.bs.modal', function(){
      console.log("hidden");
      $('#renew-modal').remove();
      $('.dtp').remove();
    });
  });

  // Requesting a listing (and renew)
  var renewModal = '<div id="renew-modal" class="modal fade" role="dialog">'+
                        '<div class="modal-dialog">'+
                          '<div class="modal-content">'+
                            '<div class="modal-header">'+
                              '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                            '</div>'+
                            '<div class="modal-body">'+
                            '<div class="modal-msg">Renew offer for </div>'+
                            '<div class="item-name">'+
                            '</div>'+
                            '<div class="container-fluid">'+
                              '<div class="form-group zero-padding request-quantity">'+
                                '<label for="renew-quantity">Quantity</label>'+
                                '<input type="number" class="form-control" id="renew-quantity" min="0">'+
                              '</div>'+
                              '<div class="form-group zero-padding request-quantity">'+
                                '<label for="renew-date">Expiry Date</label>'+
                                '<input type="text" class="form-control" id="renew-date">'+
                              '</div>'+
                            '</div>'+
                          '</div>'+
                          '<div class="modal-footer">'+
                            '<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>'+
                            '<button type="button" class="btn btn-primary accept-button" id="justRenew">Renew</button>'+
                            '<button type="button" class="btn btn-default accept-button" id="renewEdit">Renew & Edit</button>'+
                          '</div>'+
                        '</div>'+
                      '</div>'+
                    '</div>';

  function remove(ele) {
    // remove clicked element (in a very skitchy way right now)
    $grid.isotope( 'remove', $(ele).closest('.grid-item'))
    // layout remaining item elements
    .isotope('layout');
  };
});
