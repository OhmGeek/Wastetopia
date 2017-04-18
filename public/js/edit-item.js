/**
 * Created by ryan on 18/04/17.
 */

// This file just does all the autofill stuff, ready for the queries to kick in.
// Rest of the functionality is dealt with in the add-item.js file.
var Fill = function(data) {
  var fillName = function() {
      $('name').val(data.name);
  };
  var fillDescription = function() {
      data.description;
  };

  var fillDate = function() {
      data.expires;
  };

  var fillImages = function() {
    data.images (autofill these);
  };

  var fillLocation = function() {
      data.location
          .postcode
          .name

  };

  var fillType = function() {

  };

  var fillState = function() {

  };

  var fillDietary = function() {

  };

  var fillContains = function() {

  };

  var fillOther = function() {

  };
};
function fillInTheGaps() {
    var listingID = $('.grid-body').data('listingID');
    $.getJSON('https://wastetopia-pr-17.herokuapp.com/api/items/view/', data)
        .done(function(data) {
            var filler = new Fill(data);
            filler.fill
        });
}

$(document).ready(function() {
    if ($('.grid-body').data('mode') === "edit") {
        fillInTheGaps();
    }
});