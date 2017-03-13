/**
 * Created by ryan on 08/03/17.
 */




/*
 //Extract item information
 $itemName = $item["itemName"];
 $itemDescription = $item["itemDescription"];
 $useByDate = $item["useByDate"];
 $quantity = $item["quantity"];
 $itemID = $this->addToItemTable($itemName, $itemDescription, $useByDate); //Add the item

 $this->addAllTags($itemID, $tags); //Add the tags and link to item
 $this->addAllImages($itemID, $images); //Add the images and link to item

 //Extract location information
 $locationName = $location["locationName"];
 $postCode = $location["postCode"];
 $locationID = $this->addToLocationTable($locationName, $postCode); //Add the location to the database

 //Extract barcode information
 $barcodeNumber = $barcode["barcodeNumber"];
 $barcodeType = $barcode["barcodeType"];
 $this->addToBarcodeTable($itemID, $barcodeNumber, $barcodeType); //Add barcode and link to item

 //Add the whole listing
 $this->addToListingTable($locationID, $itemID, $quantity);
 */


function serializeForm() {
    // this basically goes through the document and gets all of the entered details
    var details = {
        'itemName': "Item",
        'itemDescription': "description",
        'useByDate': 'useBy',
        'quantity': '1'

    }




}

function addItem(url) {
    var itemDetails = serializeForm();

    $.post(url, itemDetails)
        .done(function() {
            //on success, display an alert saying YES
        })
        .fail(function() {
            // on failure, display error message saying NO, with message
        });
}
