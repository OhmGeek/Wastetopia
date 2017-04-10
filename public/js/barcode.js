/**
 * This file deals with barcode stuff:
 *  - Using Quagga to read barcodes
 *  - API autofilling
 *
 * Created by ryan on 10/04/17.
*/

function autofill(data) {
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
    }
}

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
Quagga.init({
    inputStream : {
        name : "Test",
        type : "ImageStream",
        length: 10,
        size: 800
    },
    decoder : {
        readers : ["ean_reader", "code_128_reader"]
    }
}, function(err) {
    if (err) {
        console.log(err);
        return;
    }
    console.log("Initialization finished. Ready to start");
    Quagga.start();
});

$('#scan-barcode').on('click', function() {
    // this is the callback called once decoding has occurred
    Quagga.decodeSingle({
        decoder: {
            readers: ["code_128_reader"] // List of active readers
        },
        locate: true, // try to locate the barcode in the image
        // get the first image uploaded using jQuery
        src: 'data:image/jpg;base64,' + $('#barcode-upload').prop('files')[0]
    },autofill);
});