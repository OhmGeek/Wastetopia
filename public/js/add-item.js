function imageUpload() {
    // go through and get the images
    $('#upload').AjaxFileUpload({
        action: 'https://wastetopia-pr-17.herokuapp.com/api/items/addimage',
        valid_extensions : ['jpg','png', 'tif', 'gif'],
        onComplete: function(response) {
            console.log(response);
            var items = JSON.parse(response);
            items.forEach(function(element) {
                showUploadedItem(element.url, element.id);
            });
        }
    });

}

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


$("#upload").change(function() {
    imageUpload();
});