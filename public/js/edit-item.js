/**
 * Created by ryan on 18/04/17.
 */

// This file just does all the autofill stuff, ready for the queries to kick in.
// Rest of the functionality is dealt with in the add-item.js file.

function fillInTheGaps() {
    $.get('https://wastetopia-pr-17.herokuapp.com/api/items/view/')
}

$(document).ready(function() {
    if ($('.grid-body').data('mode') === "edit") {
        fillInTheGaps();
    }
});