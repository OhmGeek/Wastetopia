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
    var subTabID = "";
    var otherSubTab = "";
    
    var relativeURL = "";
    if (tabID == "#listings"){
      relativeURL = "load-listings-tab";
      tabID = "#listing"; // So it doesn't load Divs inside the other two tabs
    }else if (tabID == "#available-listing"){
      relativeURL = "load-listings-tab";
      subTabID = tabID;
      otherSubTab = "#out-of-stock-listing";
      tabID = "#listing"; // So it doesn't load Divs inside the other two tabs
    }else if (tabID == "#out-of-stock-listing"){
      relativeURL = "load-listings-tab";
      subTabID = tabID;
      otherSubTab = "#available-listing";
      tabID = "#listing"; // So it doesn't load Divs inside the other two tabs
    }else if (tabID == "#requests" ){
      relativeURL = "load-requests-tab";
      tabID = "#requests";
    }else if (tabID == "#completed-request" ){
      relativeURL = "load-requests-tab";
      subTabID = tabID;
      otherSubTab = "#pending-request";
      tabID = "#requests";
    }else if(tabID == "#pending-request" ){
      relativeURL = "load-requests-tab";
      subTabID = tabID;
      otherSubTab = "#comleted-request";
      tabID = "#requests";
    }else if(tabID == "#offers"){
     relativeURL = "load-offers-tab";
      tabID = "#offers";
    }else if(tabID == "#completed-transaction"){
      console.log("Completed!");
     relativeURL = "load-offers-tab";
      subTabID = tabID;
      otherSubTab = "#pending-transaction";
      tabID = "#offers";
    }else if(tabID == "#pending-transaction" ){
     relativeURL = "load-offers-tab";
     subTabID = tabID;
      otherSubTab = "#completed-transaction";
     tabID = "#offers";
    }else if(tabID == "#watchList"){
     relativeURL = "load-watchlist-tab";
    }else if(tabID == "#home"){
     relativeURL = "load-home-tab";
    }else{
      return;
    }
    console.log(subTabID);
    console.log(otherSubTab);
    console.log(tabID);
    
    reloadTab(tabID, relativeURL, userID, subTabID, otherSubTab);

  });
  
  // Reloads the content of the given tab, for the given user, from the given relative URL
  function reloadTab(tabID, relativeURL, userID, subTabID, otherSubTab){
    var url = window.location.protocol + "//" + window.location.host + "/profile/" + relativeURL +"/" + userID;
    
    $.get(url, function(response){
      
       var div = $(tabID);
       div.replaceWith(response);
      
      $(tabID).addClass("in active"); // Make it visible?

      if(!(subTabID === "" )){
         $(subTabID).addClass("in active"); // Make it visible?
        $(subTabID).parent("li").addClass("active");
        $('a[href="'+subTabID+'"]').parent("li").addClass("active");
         $(otherSubTab).removeClass("in");
        $(otherSubTab).removeClass("active");
        $('a[href="'+otherSubTab+'"]').parent("li").removeClass("active");
       }
      console.log("Successful");
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
