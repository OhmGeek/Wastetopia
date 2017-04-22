/**
 * Created by ryan on 16/04/17.
 */

$(document).ready(function() {
    // first we need to make a request to the API to get the item images.

   var barcodeStr = $('#barcode-details-ajax').attr("barcode");
   var barcode = parseInt(barcodeStr);
   console.log(barcodeStr);
   console.log(barcode);

   if(!isNaN(barcode)) {
       // now load in the barcode details
       $.getJSON('https://world.openfoodfacts.org/api/v0/product/' + barcode + ".json", function (data) {
           console.log(data);
           if(data.status === 1) {
               return data.product;
           }
           else {
               return {};
           }
       }).done(function(barcodeinfo) {
           loadInDetails(barcodeinfo);
       });
   }

});

function showUploadedItem(url, id) {
    var $item = $('<div class="grid-item col-xs-4 col-sm-2 zero-padding">'+
        '<div class="row-action-primary checkbox img-checkbox">'+
        '<label><input type="checkbox"></label>'+
        '</div>'+
        '<div data-mh="my-group" class="upload-pic">'+
        '<img src="'+ url +'" data-imgid="' + id + '"</div>'+
        //'</div>'+
        '</div>');

    // prepend items to grid
    $grid.prepend( $item )
    // add and lay out newly prepended items
        .isotope( 'prepended', $item );
}

function loadInDetails(info) {
    /*
        info.serving_size = "255 g"
        // we could load these in using a table.
        info.image_nutrition_url = url for the nutition info
        info.image_ingredients_url = url for the ingredients image
        info.nutrient_levels = {
            "salt": "moderate"
            "sugars": "low"
            "fat": "high"
            "saturated-fat": "moderate"
        }
        info.nutriments = {
             "proteins_serving":0.576,
             "fiber":3.5,
             "carbohydrates_serving":5.23,
             "energy":"1622",
             "nutrition-score-fr_100g":"-5",
             "fat":3.3,
             "fat_100g":3.3,
             "proteins_value":"8.6",
             "saturated-fat":0.7,
             "salt_100g":0.01,
             "salt_serving":0.00067,
             "salt_unit":"g",
             "nutrition-score-fr":"-5",
             "salt":0.01,
             "saturated-fat_value":"0.7",
             "fat_unit":"g",
             "sugars_value":"0.6",
             "sugars":0.6,
             "fat_value":"3.3",
             "sugars_unit":"g",
             "proteins":8.6,
             "fat_serving":0.221,
             "saturated-fat_100g":0.7,
             "sugars_serving":0.0402,
             "saturated-fat_unit":"g",
             "proteins_unit":"g",
             "salt_value":"0.01",
             "sodium_value":"0.003937007874015748",
             "sodium_100g":0.00393700787401575,
             "carbohydrates_value":"78",
             "fiber_value":"3.5",
             "energy_100g":"1622",
             "carbohydrates":"78",
             "sodium_unit":"g",
             "nutrition-score-uk":"-5",
             "sodium_serving":0.000264,
             "energy_value":"1622",
             "sodium":0.00393700787401575,
             "sugars_100g":0.6,
             "carbohydrates_unit":"g",
             "proteins_100g":8.6,
             "fiber_100g":3.5,
             "energy_serving":"109",
             "saturated-fat_serving":0.0469,
             "nutrition-score-uk_100g":"-5",
             "fiber_unit":"g",
             "energy_unit":"kJ",
             "carbohydrates_100g":"78",
             "fiber_serving":0.235
        }


        Also need a disclaimer on there that this stuff is from OpenFoodFacts.org
        And therefore shouldn't be fully trusted.
     */

}