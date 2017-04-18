// /**
//  * Created by ryan on 18/04/17.
//  */
//
// // This file just does all the autofill stuff, ready for the queries to kick in.
// // Rest of the functionality is dealt with in the add-item.js file.
var Fill = function(data) {
    var data = data;

  this.fillType = function() {
      $('#type option').each(function(index, elem) {
          var selected = false;
          for(var i = 0; i < data.type.length; i++) {
              if(data.type[i].name === elem.text()) {
                  selected = true;
              }
          }
          if(selected) {
              elem.attr('selected', 'selected');
          }
      });
  };

  this.fillState = function() {
        //go through all possible checkboxes, and select if they are in the list
      $('#state option').each(function(index, elem) {
          var selected = false;
          for(var i = 0; i < data.state.length; i++) {
              if(data.state[i].name === elem.text()) {
                  selected = true;
              }
          }
          if(selected) {
              elem.attr('selected', 'selected');
          }
      });
  };

  this.fillDietary = function() {
      $('#type option').each(function(index, elem) {
          var selected = false;
          for(var i = 0; i < data.dietary.length; i++) {
              if(data.dietary[i].name === elem.text()) {
                  selected = true;
              }
          }
          if(selected) {
              elem.attr('selected', 'selected');
          }
      });
  };

  this.fillContains = function() {
      $('#content option').each(function(index, elem) {
          var selected = false;
          for(var i = 0; i < data.contains.length; i++) {
              if(data.contains[i].name === elem.text()) {
                  selected = true;
              }
          }
          if(selected) {
              elem.attr('selected', 'selected');
          }
      });
  };

  this.fillOther = function() {
      $('#type option').each(function(index, elem) {
          var selected = false;
          for(var i = 0; i < data.other.length; i++) {
              if(data.other[i].name === elem.text()) {
                  selected = true;
              }
          }
          if(selected) {
              elem.attr('selected', 'selected');
          }
      });
  };
};
function fillInTheGaps() {
    var listingID = $('.grid-body').data('listingID');
    $.getJSON('https://wastetopia-pr-17.herokuapp.com/api/items/view/', data)
        .done(function(data) {
            var filler = new Fill(data);
            filler.fillType();
            filler.fillContains();
            filler.fillDietary();
            filler.fillState();
            //filler.fillOther();
        });
}

$(document).ready(function() {
    if ($('.grid-body').data('mode') === "edit") {
        fillInTheGaps();
    }
});