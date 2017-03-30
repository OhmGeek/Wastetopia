function imageUpload() {
    // go through and get the images
    var formdata = $('#form-image').serialize();

    $.ajax({
        url: 'https://wastetopia-pr-17.herokuapp.com/api/items/addimage',
        type: "POST",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (res) {
            var items = JSON.parse(res);
            items.forEach(function(item) {
               showUploadedItem(items.url, items.id);
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