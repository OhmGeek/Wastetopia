/**
 * This file deals with barcode stuff:
 *  - Using Quagga to read barcodes
 *  - API autofilling
 *
 * Created by ryan on 10/04/17.
*/

function autofill(data) {
        console.log("Start autofilling");
        if(data.product_name) {
            //auto fill item name
            console.log("Try autofilling name with:");
            console.log(data.product_name);
            $('#name').val(data.product_name);
        }

        if(data.expiration_date) {
            // auto fill expiration date
        }

        if(data.generic_name) {
            // auto fill the description
            console.log("Try autofilling description with:");
            console.log(data.generic_name);
            $('#description').val(data.generic_name);
        }

        // now add the images

        if(data.image_url) {
            showUploadedItem(data.image_url, 0); // we don't use the id anymore
        }

}



var scanBarcode = function() {
    var formdata = new FormData($('#barcode-scanner')[0]);
    formdata.append('file', $('#barcode-upload').prop('files')[0]); // todo add all files

    $.ajax({
        url: 'https://wastetopia.herokuapp.com/api/barcode/get',
        data: formdata,
        cache: false,
        contentType: false,
        processData: false,
        type: 'POST'

    }).done(function(data) {
        // now we need to strip out the barcode from the HTML response.
        var startIndex = data.indexOf("<");
        var htmlResponse = data.substring(startIndex);
        var html = $.parseHTML(htmlResponse);
        console.log(htmlResponse);
        console.log("Barcode");
        // use JQuery to get the barcode
        var barcode = $(htmlResponse).find("pre")[0].innerText;

        $.getJSON('https://world.openfoodfacts.org/api/v0/product/' + barcode + ".json", function (data) {
            console.log(data);
            if(data.status === 1) {
                return data.product;
            }
            else {
                return {};
            }
        }).done(function(barcodeinfo) {
            autofill(barcodeinfo.product);
        });

    });
};

$('#scan-barcode').on('click', scanBarcode);