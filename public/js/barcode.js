/**
 * This file deals with barcode stuff:
 *  - Using Quagga to read barcodes
 *  - API autofilling
 *
 * Created by ryan on 10/04/17.
*/

function process(data) {
    console.log("On Processed");
    console.log(data);
    if(data.codeResult) {
        var barcode = data.codeResult.code;
        console.log(barcode);
        var data = getBarcodeInfo(barcode);

        // item name
        if(data.product_name) {
            //auto fill item name
            $('#name').val(data.product_name);
        }

        if(data.expiration_date) {
            // auto fill expiration date
        }

        if(data.generic_name) {
            // auto fill the description
            $('#description').val(data.generic_name);
        }

        // now add the images

        if(data.image_url) {
            showUploadedItem(data.image_url, 0); // we don't use the id anymore
        }

    } else {
        // no autofill.
        // provide some feedback saying it couldn't be detected.
        console.log("not detected");
    }
};


function getBarcodeInfo(barcode) {
    $.getJSON('https://world.openfoodfacts.org/api/v0/product/' + barcode + ".json", function (resp) {
        var data = JSON.parse(resp);
        console.log(data);
        if(data.status === 1) {
            return data.product;
        }
        else {
            return {};
        }
    });
}

var scanBarcode = function() {
    // first, get the image data as URL
    // var file = $('#barcode-upload').prop('files')[0];
    // var reader = new FileReader();
    //
    // reader.addEventListener("load", function() {
    //     console.log(reader.result); // url
    //     Quagga.decodeSingle({
    //         decoder: {
    //             readers: ["upc_reader"]
    //         },
    //         locate: true, // try to locate the barcode in the image
    //         src: reader.result
    //     }, function(result){
    //         console.log(result);
    //         if(result.codeResult) {
    //             console.log("result", result.codeResult.code);
    //         } else {
    //             console.log("not detected");
    //         }
    //     });
    // }, false);
    // reader.readAsDataURL(file);
    var formdata = new FormData($('#barcode-scanner')[0]);
    formdata.append('file', $('#barcode-upload').prop('files')[0]); // todo add all files

    $.ajax({
        url: 'https://wastetopia-pr-17.herokuapp.com/api/barcode/get',
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
            console.log(barcodeinfo);
        });

    });
};

$('#scan-barcode').on('click', scanBarcode);