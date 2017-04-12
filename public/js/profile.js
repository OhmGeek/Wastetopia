$(function () {
  // init Isotope
  var $grid = $('.grid').isotope({
    itemSelector: '.grid-item',
    percentPosition: true,
    masonry: {
      columnWidth: '.grid-sizer'
    }
  });

  // RELOADING IS NOT WORKING
  $(document).on('click', 'a[data-toggle="tab"]', function(){
    console.log("Trying to reload");
    var userID = $('.user-name').attr("id");
    var tabID = $(this).attr('href');
    
    var relativeURL = "";
    if (tabID == "#listings" || tabID == "#available-listing" || tabID == "#out-of-stock-listing" ){
      relativeURL = "load-listings-tab";
      tabID = "#listing"; // So it doesn't load Divs inside the other two tabs
    }else if(tabID == "#requests" || tabID == "#completed-request" || tabID == "#pending-request" ){
      relativeURL = "load-requests-tab";
      tabID = "#requests";
    }else if(tabID == "#offers" || tabID == "#completed-transaction" || tabID == "#pending-transaction" ){
     relativeURL = "load-offers-tab";
      tabID = "#offers";
    }else if(tabID == "#watchList"){
     relativeURL = "load-watchlist-tab";
    }else if(tabID == "#home"){
     relativeURL = "load-home-tab";
    }else{
      return;
    }
   
    reloadTab(tabID, relativeURL, userID);

  });
  
  // Reloads HOME tab when watch list pressed (will this reload the page?)
  $(document).on('click', 'a[href="#watch"]', function(){
    var userID = $('.user-name').attr("id");
    reloadTab('#home', "load-home-tab", userID);
  });
  
  
  // Reloads the content of the given tab, for the given user, from the given relative URL
  function reloadTab(tabID, relativeURL, userID){
    var url = window.location.protocol + "//" + window.location.host + "/profile/" + relativeURL +"/" + userID;
    
    $.get(url, function(response){
       var div = $(tabID);
       div.replaceWith(response);
      
      $(tabID).addClass("in active"); // Make it visible?
        
      // re initialize isotope
       $grid = $('.grid').isotope({
          itemSelector: '.grid-item',
          percentPosition: true,
          masonry: {
            columnWidth: '.grid-sizer'
          }
        });
    });
  }
  
  $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {

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

  // Set all pending transactions to viewed
  $(document).on('shown.bs.tab', 'a[href="#pending-transaction"]', function(){
    var url = window.location.protocol + "//" + window.location.host + "/profile/set-pending-viewed";
      $.post(url, function(response){
        // Do nothing
        console.log(response);
      })
  });

});
