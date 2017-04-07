function showUploadedItem(url, id) {
    var item = $('div').html = '<div>' +
                                    '<div class="col-xs-4 col-sm-2 zero-padding">'+
                                        '<div class="row-action-primary checkbox img-checkbox">'+
                                            '<label><input type="checkbox"></label>'+
                                        '</div>'+
                                    '<div data-mh="my-group" class="upload-pic">'+
                                        '<img src="' + url + '" data-imgid="' + id + '"</div>'+
                                    '</div>'+
                                '</div>';

    //now we add the item div to the page.
    $('#image-demo').before(item);
}


function imageUpload() {
    // go through and get the images
    var formdata = new FormData($('#form-image')[0]);
    formdata.append('image', $('input[type=file]')[0].files[0]); // todo add all files
    $.ajax({
        url: 'https://wastetopia-pr-17.herokuapp.com/api/items/addimage',
        type: "POST",
        data: formdata,
        cache: false,
        processData: false,
        contentType: false,
        success: function (res) {
            var items = JSON.parse(res);
            items.forEach(function(item) {
                showUploadedItem(item.url, item.id);
            });
        }
    });

}

$('#form-image').change(function() {
    imageUpload();
});

function getImagesFromDOM() {
    var imageList = [];
    $('.upload-pic img').forEach(function(elem) {
        imageList.append(elem.href); //todo check this - we want to get the href of the image tag
    });
    return imageList;
}

function getSelectedDietaryReqs() {
    var requirementsList = [];
    // todo use filter to get this working nicely.
    $('.dietary-req').forEach(function(elem) {
        // if the selected checkbox is actually selected, add the item
        // to the requirements list. Otherwise, move to the next one.
        if(elem.val()) {
            requirementsList.append(elem.text); //get the text (or the contents of the tag).
        }
    });

    // check if the user has any other requirements that need fulfilling.
    var otherRequirements = $('#other-req').val();

    if(otherRequirements) {
        requirementsList.append(otherRequirements);
    }

    return requirementsList;
}
function getMayContainsDetails() {
    var mayContain = [];
    $('.may-contains-select option').forEach(function(elem) {
        if(elem.val()) {
            mayContain.append(elem.text);
        }
    });
    return mayContain;
}

function getLocationOfItem() {
    // todo use Google Maps/Ben's API
    var location = {
        "state": Alabama,
        "firstLineAddr": 23 Frances,
        "secondLineAddr" Postcode
    }
}
function serializeItem() {
    //todo: process expiry date (need more research into this)
    // todo: process item type properly.
    var item = {
        "name": $('#name').val(),
        "images": getImagesFromDOM(),
        "classification": itemType,
        "dietary": getSelectedDietaryReqs(),
        "contains": getMayContainsDetails(),
        "expires": expiryDate,
        "description": $('description').val(),
        "location": getLocationOfItem()
    }
}