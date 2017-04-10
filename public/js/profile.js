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

  // Reload HTML content when Back buttons pressed on each tab
  $grid.on('click', 'a[href="#home"]',function (){
    console.log("Reloading");
    var userID = $('.user-name').attr("id");
    var url = window.location.protocol + "//" + window.location.host + "/profile/update/" + userID;
     $.get(url, function(response){
        var div = $("#profileContentWrapper");
        div.replaceWith(response);
     });
   });

  // Delete a card from the page
  $grid.on( 'click', '#delete', function() {
    // get the id of the item would be removed
    console.log($(this).closest('.thumbnail').attr("id"));
    remove(this)
  });

  // Remove an element from the layout
  function remove(ele) {
    // remove clicked element (in a very skitchy way right now)
    $grid.isotope( 'remove', $(ele).closest('.grid-item'))
    // layout remaining item elements
    .isotope('layout');
  };

//   // Toggle listings in the watch list
//   $grid.on('click', '.btn-watch', function(){
//      var listingID = $(this).closest('.thumbnail').attr("id");
//      var isUser = parseInt($(this).closest('.user-stats').attr("id"));
//      var listing = $(this);

//      $.post("/profile/toggle-watch-list/"+listingID, function(response){
//        // Do something depending on if response is true or false?? (Currently always true)
//        console.log("DONE");
//        console.log(response);
//       // 1 means deleted, 2 means added
//       if (response == 1){
//         // Set colour to pale (Deleted)
//         listing.removeClass("watched");
//         console.log(listing);
//       }else{
//         // Set colour to dark (Added)
//         listing.addClass("watched");
//         console.log(listing);
//       }
//      });
//   });
});
