<?php

require_once '../vendor/autoload.php';


use Klein\Klein;
use Wastetopia\Config\CurrentConfig;
use Wastetopia\Controller\ConversationListController;
use Wastetopia\Controller\LoginController;
use Wastetopia\Controller\ProfilePageController;
use Wastetopia\Controller\SearchController;
use Wastetopia\Controller\MessageController;

use Wastetopia\Controller\RecommendationController;

use Wastetopia\Model\RequestModel;
use Wastetopia\Model\PopularityModel;

use Wastetopia\Controller\SearchPageController;


use Wastetopia\Controller\RegistrationController;

use Wastetopia\Model\RegistrationModel; // For verification

use Wastetopia\Controller\AddItemController;
use Wastetopia\Controller\ViewItemController;

use Wastetopia\Model\NotificationModel;

use Wastetopia\Controller\AnalysisController;



/**
 * Function to be called at start of routing for any route where user must be logged in to access
 * @return - Nothing, either redirects user to login page and exits or just returns to function that called it
 */


// check if we should use production? Otherwise, use community.
$mode = $_ENV['MODE'];
$config = new CurrentConfig();
$config->loadConfig($mode);
$base  = dirname($_SERVER['PHP_SELF']);

function forceLogin($destRoute) {
    if(!\Wastetopia\Controller\Authenticator::isAuthenticated()) {
        //redirect to the current route
        header("Location: " . $_ENV['ROOT_BASE'] . '/login?dest='. urlencode($_SERVER['REQUEST_URI']));
        exit();
    }
    return true;
}
//// Update request when we have a subdirectory
if(ltrim($base, '/')){
    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen($base));
}
// Dispatch as always
$klein = new Klein();

$klein->respond("GET", "/?", function() {
  return "HomePage";
});


$klein->respond("GET", "/notifications/update", function($request, $response){
   $model = new NotificationModel();
   return $model->getAll(1); // Get as JSON
});

$klein->with('/api', function () use ($klein) {

    $klein->respond('GET', '/search/page/[**:param]', function ($request, $response) {
        $searchController = new SearchController();
        $paramArr = explode("/", $request->param);
        $lat = $paramArr[0];
        $long = $paramArr[1];
        $search = $paramArr[2];
        $tagsArr = explode("+",$paramArr[3]);
        $notTagsArr = explode("+",$paramArr[4]);
        $distanceLimit = $paramArr[5];
        $pageNumber = $paramArr[6];
        $response->sendHeaders('Content-Type: application/jpg');
        return $searchController->JSONSearch($lat, $long, $search, $tagsArr, $notTagsArr, $distanceLimit, $pageNumber);
    });
    $klein->respond('GET', '/search/map/[**:param]', function ($request, $response) {
        $searchController = new SearchController();
        $paramArr = explode("/", $request->param);
        $lat = $paramArr[0];
        $long = $paramArr[1];
        $search = $paramArr[2];
        $tagsArr = explode("+",$paramArr[3]);
        $notTagsArr = explode("+",$paramArr[4]);
        $distanceLimit = $paramArr[5];
        $response->sendHeaders('Content-Type: application/jpg');
        return $searchController->MAPSearch($lat, $long, $search, $tagsArr, $notTagsArr, $distanceLimit);
    });
});

$klein->respond('GET', '/search/[:search]?', function ($request, $response) {
    $controller = new SearchPageController();
    return $controller->render($request->search);
});


$klein->respond("GET", "/login", function($request, $response) {
  header("Cache-Control: no-store, must-revalidate, max-age=0");
  error_log($request->dest);
  $controller = new LoginController();
  return $controller->index($response, urldecode($request->dest));
});

$klein->respond("GET", "/logout", function($request, $response) {
    header("Cache-Control: no-store, must-revalidate, max-age=0");
    setcookie("gpwastetopiadata", null);
    return "You have been logged out. You can now close the browser.";
});

$klein->with('/register', function() use ($klein){
  
  $klein->respond("GET", "/?", function($request, $response) {
    $controller = new RegistrationController();
    return $controller->generatePage();
  });
  
  $klein->respond("POST", "/add-user", function($request, $response){
      $forename = $request->forename;
      $surname = $request->surname;
      $email = $request->email;
      $password = $request->password;
      $passwordConfirm = $request->passwordConfirm;
      $pictureURL = $request->pictureURL;
      
      $controller = new RegistrationController();
      return $controller->addUser($forename, $surname, $email, $password, $passwordConfirm, $pictureURL);
  });
    
    $klein->respond("GET","/verify/[:verificationCode]", function($request, $response){
        $verificationCode = $request->verificationCode;
        $model = new RegistrationModel(); // Put function in controller?
        $result = $model->verifyUser($verificationCode);
        if (!($result)){
            // Verification didn't work, what do we do now??
            return "It didn't work";
        }else{
            // Verirification worked, send them to login page??
            return "Verification successful: your account is now active";
        }

    });
    
    // Only for testing purposes
    $klein->respond("GET", "/delete/[:firstName]/[:lastName]", function($request, $response){
       $firstName = $request->firstName;
        $lastName = $request->lastName;
        $model = new RegistrationModel();
        return $model->deleteUserByName($firstName, $lastName);
    });
  
});



// Used for charts - needs testing
$klein->with("/analysis", function() use ($klein){

    $klein->respond('GET', '/?', function($request, $response){
        forceLogin($request->uri());
        $controller = new AnalysisController();
        return $controller->generatePage();
    });

    $klein->respond('GET', '/categories', function($request, $response){
        forceLogin($request->uri());
        $controller = new AnalysisController();
        return $controller->getCategoryDetailsJSON();
    });


    $klein->respond('GET', '/get-request-tags/[:categoryID]', function($request, $response){
        forceLogin($request->uri());
       $categoryID = $request->categoryID;
       $categoryIDs = array();
       array_push($categoryIDs, $categoryID);
       $controller = new AnalysisController();
       return $controller->getTagFrequenciesForTransactionsJSON($categoryIDs);
    });


    $klein->respond('GET', '/get-sending-tags/[:categoryID]', function($request, $response){
        forceLogin($request->uri());
        $categoryID = $request->categoryID;
        $categoryIDs = array();
        array_push($categoryIDs, $categoryID);
        $controller = new AnalysisController();
        return $controller->getTagFrequenciesForListingsJSON($categoryIDs);
    });


    $klein->respond('GET', '/get-request-names', function($request, $response){
        forceLogin($request->uri());
        $controller = new AnalysisController();
        return $controller->getTotalNameFrequenciesReceiving();
    });


    $klein->respond('GET', '/get-sending-names', function($request, $response){
        forceLogin($request->uri());
        $controller = new AnalysisController();
        return $controller->getTotalNameFrequenciesSending();
    });
});


$klein->with("/profile", function() use ($klein) {

   $klein->respond('GET', '/?', function($request, $response){
       forceLogin($request->uri());
        $controller = new ProfilePageController(1); //View own profile
        return $controller->generatePage();
    });
    
    $klein->respond('GET', '/user/[:userID]', function($request, $response){
        $controller = new ProfilePageController(0, $request->userID); //View other user's profile
        return $controller->generatePage();
    });
    
    $klein->respond('GET', '/update/[:userID]', function($request, $response){
        forceLogin($request->uri());
       $controller = new ProfilePageController(0, $request->userID);
       return $controller->generateProfileContentHTML(); 
    });
    
    $klein->respond('GET', '/load-home-tab/[:userID]', function($request, $response){

        forceLogin("/profile/load-home-tab/" . $request->userID);
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
    
    
    $klein->respond('POST', '/set-listing-transaction-hidden', function($request, $response){
       $giverOrReceiver = $request->giverOrReceiver;
        $transactionID = $request->transactionID;
        $value = 1;
        $controller = new ProfilePageController(1); // Own profile
        return $controller-> setListingTransactionHiddenFlag($giverOrReceiver, $transactionID, $value);
    });
    
    // Needs testing
    $klein->respond('POST', '/change-password', function($request, $response){
        forceLogin($request->uri());
        $oldPassword = $request->oldPassword;
        $newPassword = $request->newPassword;
        $controller= new ProfilePageController(1);
        return $controller->changePassword($oldPassword, $newPassword);    
    });
    
//    // Needs testing
//    $klein->respond('POST', '/reset-password', function($request, $response){
//       $controller = new ProfilePageController(1);
//       return $controller->resetPassword();
//    });
    
    // Needs testing
    $klein->respond('POST', '/change-profile-picture', function($request, $response){
        forceLogin($request->uri());
        $files = $request->files();        
        $controller = new ProfilePageController(1);
        return $controller->changeProfilePicture($files);
    });
   
    //Needs testing
    $klein->respond('POST', '/change-email', function($request, $response){
        forceLogin($request->uri());
        $oldEmail = $request->oldEmail;
        $newEmail = $request->newEmail;
        $controller = new ProfilePageController(1);
        return $controller->changeEmail($oldEmail, $newEmail);
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
        forceLogin($request->uri());
        // Show a single user
        $listingID = $request->listingID;
        $quantity = $request->quantity;
        $model = new RequestModel();
        return $model->requestItem($listingID, $quantity);
    });
    
    $klein->respond('POST', '/confirm-request/?', function($request, $response){
        forceLogin($request->uri());
        $listingID = $request->listingID; // Might not have this information
        $transactionID = $request->transactionID; // Can use this to get listingID
        $quantity = $request->quantity; // Assume it is given by default
        $model = new RequestModel();
        return $model->confirmRequest($listingID, $transactionID, $quantity);
    });
    
    $klein->respond('POST', '/reject-request/?', function($request, $response){
        forceLogin($request->uri());
        $listingID = $request->listingID; // Might not have this information
        $transactionID = $request->transactionID; // Can use this to get listingID
        $model = new RequestModel();
        return $model->rejectRequest($listingID, $transactionID);
    });
    
    $klein->respond('POST', '/cancel-request/?', function($request, $response){
        forceLogin($request->uri());
        $transactionID = $request->transactionID; // Can use this to get listingID
        print_r($transactionID);
        $model = new RequestModel();
        return $model->withdrawRequest($transactionID);
    });
    
    $klein->respond('POST', '/cancel-request-listing/?', function($request, $response){
        forceLogin($request->uri());
        $listingID = $request->listingID;
        $model = new RequestModel();
        $transactionID = $model->getTransactionIDFromListingID($listingID);
        return $model->withdrawRequest($transactionID);
    });
    
    $klein->respond('POST', '/renew-listing/?', function($request, $response){
        forceLogin($request->uri());
        $listingID = $request->listingID;
        $newQuantity = $request->quantity;
        $newUseByDate = $request->useByDate;
        $model = new RequestModel();
        return $model->renewListing($listingID, $newQuantity, $newUseByDate);
    });
    
    $klein->respond('POST', '/remove-listing/?', function($request, $response){
        forceLogin($request->uri());
        $listingID = $request->listingID;
        $model = new RequestModel();
        return $model->withdrawListing($listingID);
    });
    
    // Not sure whether to move this to profile page as this will be where it is used
    $klein->respond('POST', '/rate-user/?', function($request, $response){
        forceLogin($request->uri());
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
        $dest = $request->dest;
        return $controller->login($username, $password, $dest, $response);
    });
});
// todo authenticate on messages. Must be logged in to view correct messages.
$klein->with('/messages', function () use ($klein) {
    $klein->respond('GET', '/?', function ($request, $response) {
        forceLogin($request->uri());
      // Show all conversations
      $controller = new ConversationListController();
      return $controller->generatePage();
    });
    // these are the API based messaging tasks
    // todo: error/failure in response.
    $klein->respond('POST', '/send', function ($request, $response) {
        forceLogin($request->uri());
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
        forceLogin($request->uri());
      $controller = new ConversationListController();
      $conversationID = $request->conversationID;
      $controller->deleteConversation($conversationID);
      return "";
    });
    $klein->respond('GET', '/poll-sending', function ($request, $response) {
        forceLogin($request->uri());
      $controller = new ConversationListController();
      return $controller->generateSendingTabHTML();
    });
    $klein->respond('GET', '/poll-receiving', function ($request, $response) {
        forceLogin($request->uri());
      $controller = new ConversationListController();
      return $controller->generateReceivingTabHTML();
    });
    $klein->respond('GET', '/poll-messages/[:conversationID]', function ($request, $response) {
        forceLogin($request->uri());
      $conversationID = $request->conversationID;
      $controller = new MessageController();
      return $controller->generateMessageDisplay($conversationID);
    });

    $klein->respond('GET', '/conversation/[:listingID]', function ($request, $response) {
        forceLogin($request->uri());
        // view a specific conversation
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
        'Oh no, a bad error happened that caused a ' . $code
      );
    }
});
$klein->dispatch();
