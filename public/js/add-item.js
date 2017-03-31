

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
    console.log("Attempting image upload");
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
            console.log("success in upload");
            var items = JSON.parse(res);
            items.forEach(function(item) {
                showUploadedItem(items.url, items.id);
            });
        }
    });

}

$('#image-upload').change(function() {
    imageUpload();
})

