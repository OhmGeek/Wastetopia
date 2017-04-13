<?php

require_once '../vendor/autoload.php';

use Klein\Klein;
use Wastetopia\Config\CurrentConfig;
use Wastetopia\Controller\ConversationListController;
use Wastetopia\Controller\Login_Controller;
use Wastetopia\Controller\ProfilePageController;
use Wastetopia\Controller\SearchController;
use Wastetopia\Controller\MessageController;
use Wastetopia\Controller\RecommendationController;

use Wastetopia\Model\RequestModel;
use Wastetopia\Model\PopularityModel;



// check if we should use production? Otherwise, use community.
$mode = $_ENV['MODE'];
$config = new CurrentConfig();
$config->loadConfig($mode);

$base  = dirname($_SERVER['PHP_SELF']);

// Update request when we have a subdirectory
if(ltrim($base, '/')){
    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen($base));
}

// Dispatch as always
$klein = new Klein();

$klein->respond("GET", "/", function() {
    return "HomePage";
});


$klein->with('/search', function () use ($klein) {

    $klein->respond('GET', '/[**:param]', function ($request, $response) {
        $searchController = new SearchController();
        $paramArr = explode("/", $request->param);
        $lat = $paramArr[0];
        $long = $paramArr[1];
        $search = $paramArr[2];
        $tagsArr = explode("+",$paramArr[3]);
        $pageNumber = $paramArr[4];
        $response->sendHeaders('Content-Type: application/jpg');
        return $searchController->JSONSearch($lat, $long, $search, $tagsArr, $pageNumber);
    });
});

$klein->respond("GET", "/login", function($request, $response) {
    $controller = new LoginController();
    return $controller->index($response);
});

$klein->respond("GET", "/register", function() {
   return "Registering page";
});



$klein->respond("GET", "/get-env", function() {
   $envStr = "DB Host: " . $_ENV['DB_HOST'] . "\n";
    $envStr .= "DB_NAME " . $_ENV['DB_NAME'] . "\n";
    $envStr .= "DB_USER " . $_ENV['DB_USER'] . "\n";
    $envStr .= "DB_PASS " . $_ENV['DB_PASS'] . "\n";
    echo "Printing stuff now \n";
    return $envStr;
});

$klein->with("/profile", function() use ($klein) {

   $klein->respond('GET', '/?', function($request, $response){
        $controller = new ProfilePageController(1); //View own profile
        return $controller->generatePage();
    });
    
    $klein->respond('GET', '/user/[:userID]', function($request, $response){
        $controller = new ProfilePageController(0, $request->userID); //View other user's profile
        return $controller->generatePage();
    });
    
    $klein->respond('GET', '/update/[:userID]', function($request, $response){
       $controller = new ProfilePageController(0, $request->userID);
       return $controller->generateProfileContentHTML(); 
    });
    
    $klein->respond('GET', '/load-home-tab/[:userID]', function($request, $response){
        $controller = new ProfilePageController(0, $request->userID);
        return $controller->generateHomeSection(); 
    });
                    
    $klein->respond('GET', '/load-listings-tab/[:userID]', function($request, $response){
        $controller = new ProfilePageController(0, $request->userID);
        return $controller->generateListingsSection(); 
    });
    
    $klein->respond('GET', '/load-offers-tab/[:userID]', function($request, $response){
        $controller = new ProfilePageController(0, $request->userID);
        return $controller->generateOffersSection(); 
    });
    
    $klein->respond('GET', '/load-requests-tab/[:userID]', function($request, $response){
        $controller = new ProfilePageController(0, $request->userID);
        return $controller->generateRequestsSection(); 
    });
    
    $klein->respond('GET', '/load-watchlist-tab/[:userID]', function($request, $response){
        $controller = new ProfilePageController(0, $request->userID);
        return $controller->generateWatchListSection(); 
    });
    
    $klein->respond('POST', '/toggle-watch-list/?', function($request, $response){
       $controller = new ProfilePageController(1);
       $response = $controller->toggleWatchListListing($request->listingID);
       return $response;
    });
    
    $klein->respond('GET', '/recommended', function($request, $response){
        $controller = new RecommendationController();
        return $controller->generateRecommendedSection();
    });
    
    // Needs testing
    $klein->respond('POST', '/set-pending-viewed', function($request, $response){
       $controller = new ProfilePageController(1);
       return $controller->setAllPendingAsViewed();
    });
    
    // Needs testing
    $klein->respond('POST', '/set-listing-transaction-hidden', function($request, $response){
       $giverOrReceiver = $request->giverOrReceiver;
        $listingID = $request->listingID;
        $value = 1;
        $controller = new ProfilePageController(1); // Own profile
        return $controller-> setListingTransactionHiddenFlag($giverOrReceiver, $listingID, $value);
    });
    
    // Needs testing
    $klein->respond('POST', '/change-password', function($request, $response){
        $oldPassword = $request->oldPassword;
        $newPassword = $request->newPassword;
        $controller= new ProfilePageController(1);
        return $controller->changePassword($oldPassword, $newPassword);    
    });
    
    // Needs testing
    $klein->respond('POST', '/reset-password', function($request, $response){
       $controller = new ProfilePageController(1);
       return $controller->resetPassword(); 
    });
   
});



$klein->with('/items', function () use ($klein) {
    $klein->respond('GET', '/?', function ($request, $response) {
        // Generic Items Page
        return "Main Item Page";
    });
    
    $klein->respond('GET', '/view/[:id]', function($request, $response){
        $itemID = $request->id;
        return "Show item ".$itemID;
    });
    
    $klein->respond('POST', '/request/?', function ($request, $response) {
        // Show a single user
        $listingID = $request->listingID;
        $quantity = $request->quantity;
        $model = new RequestModel();
        return $model->requestItem($listingID, $quantity);
    });
    
    $klein->respond('POST', '/confirm-request/?', function($request, $response){
        $listingID = $request->listingID; // Might not have this information
        $transactionID = $request->transactionID; // Can use this to get listingID
        $quantity = $request->quantity; // Assume it is given by default
        $model = new RequestModel();
        return $model->confirmRequest($listingID, $transactionID, $quantity);
    });
    
    $klein->respond('POST', '/reject-request/?', function($request, $response){
        $listingID = $request->listingID; // Might not have this information
        $transactionID = $request->transactionID; // Can use this to get listingID
        $model = new RequestModel();
        return $model->rejectRequest($listingID, $transactionID);
    });
    
    $klein->respond('POST', '/cancel-request/?', function($request, $response){
        $transactionID = $request->transactionID; // Can use this to get listingID
        print_r($transactionID);
        $model = new RequestModel();
        return $model->withdrawRequest($transactionID);
    });
    
    $klein->respond('POST', '/cancel-request-listing/?', function($request, $response){
        $listingID = $request->listingID;
        $model = new RequestModel();
        $transactionID = $model->getTransactionIDFromListingID($listingID);
        return $model->withdrawRequest($transactionID);
    });
    
    $klein->respond('POST', '/renew-listing/?', function($request, $response){
        $listingID = $request->listingID;
        $newQuantity = $request->quantity;
        $useByDate = $request->useByDate;
        $model = new RequestModel();
        return $model->renewListing($listingID, $newQuantity, $newUseByDate);
    });
    
    $klein->respond('POST', '/remove-listing/?', function($request, $response){
        $listingID = $request->listingID;
        $model = new RequestModel();
        return $model->withdrawListing($listingID);
    });
    
    // Not sure whether to move this to profile page as this will be where it is used
    $klein->respond('POST', '/rate-user/?', function($request, $response){
        $transactionID = $request->transactionID;
        $rating = $request->rating;
        $model = new PopularityModel();
        return $model->rateTransaction($transactionID, $rating);
    });
    
});



$klein->with('/api', function () use ($klein) {
    $klein->respond('POST', '/verify-login', function ($request, $response) {
        $controller = new LoginController();
        $username = $request->email;
        $password = $request->password;
        $dest = $_ENV['ROOT_BASE'];
        return $controller->login($username, $password, $dest, $response);
    });

    $klein->respond('GET', '/[:id]', function ($request, $response) {
        // Show a single user
        $itemID = $request->id;
        return "Show Item " . $itemID;
    });

});


// todo authenticate on messages. Must be logged in to view correct messages.
$klein->with('/messages', function () use ($klein) {

    $klein->respond('GET', '/?', function ($request, $response) {
        // Show all conversations
        $controller = new ConversationListController();
        return $controller->generatePage();
    });


    // these are the API based messaging tasks
    // todo: error/failure in response.
    $klein->respond('POST', '/send', function ($request, $response) {
        //send a message
        //we need the conversationID and the message.
        $conversationID = $request->conversationID;
        $message = $request->message;

        console.log($conversationID);
        console.log($message);
        $controller = new MessageController();
        $controller->sendMessage($conversationID,$message);
        return "";
    });

    $klein->respond('POST', '/delete-conversation', function ($request, $response) {
        $controller = new ConversationListController();
        $conversationID = $request->conversationID;
        $controller->deleteConversation($conversationID);
        return "";
    });

    $klein->respond('GET', '/poll-sending', function ($request, $response) {
        $controller = new ConversationListController();
        return $controller->generateSendingTabHTML();
    });

    $klein->respond('GET', '/poll-receiving', function ($request, $response) {
        $controller = new ConversationListController();
        return $controller->generateReceivingTabHTML();
    });
    $klein->respond('GET', '/poll-messages/[:conversationID]', function ($request, $response) {
        $conversationID = $request->conversationID;
        $controller = new MessageController();
        return $controller->generateMessageDisplay($conversationID);
    });

    $klein->respond('GET', '/conversation/[:listingID]', function ($request, $response) {
        // view a specific conversation
        console.log("Getting conversation");
        $listingID = $request->listingID;

        $controller = new MessageController();
        return $controller->generatePageFromListing($listingID);
    });
});


$klein->onHttpError(function ($code, $router) {
    switch ($code) {
        case 404:
            $router->response()->body(
                'Y U so lost?!'
            );
            break;
        case 405:
            $router->response()->body(
                'You can\'t do that!'
            );
            break;
        default:
            $router->response()->body(
                'Oh no, a bad error happened that caused a '. $code
            );
    }
});


$klein->dispatch();
