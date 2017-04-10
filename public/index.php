<?php

require_once '../vendor/autoload.php';
use Klein\Klein;
use Wastetopia\Controller\AddItemController;
use Wastetopia\Controller\ConversationListController;
use Wastetopia\Controller\LoginController;
use Wastetopia\Controller\ViewItemController;
use Wastetopia\Config\CurrentConfig;
use Wastetopia\Controller\MessageController;

// check if we should use production? Otherwise, use community.
$mode = $_ENV['MODE'];
$config = new CurrentConfig();
$config->loadConfig($mode);

$base  = dirname($_SERVER['PHP_SELF']);

//// Update request when we have a subdirectory
if(ltrim($base, '/')){
    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen($base));
}

// Dispatch as always
$klein = new Klein();

$klein->respond("GET", "/?", function() {
  return "HomePage";
});


$klein->respond("GET", "/login", function($request, $response) {
  $controller = new LoginController();
  return $controller->index($response);
});

$klein->with("/register", function() use $(klein){
  $klein->respond("GET", "/?", function() {
//    $controller = new RegistrationController();
//    return $controller->generatePage():
  });

  $klein->respond("POST", "/add-user", function($request,$response){
    $firstName = $request->firstName;
    $lastName = $request->lastName;
    $email = $request->email;
    $password = $request->password;
    $passwordConfirm = $request->passwordConfirm;
    $pictureURL = $request->pictureURL;

//    $controller = new RegistrationController();
//    return $controller->addUser($firstName, $lastName, $email, $password, $passwordConfirm, $pictureURL);
  });
});


$klein->respond("GET", "/get-env", function() {
  $envStr = "DB Host: " . $_ENV['DB_HOST'] . "\n";
  $envStr .= "DB_NAME " . $_ENV['DB_NAME'] . "\n";
  $envStr .= "DB_USER " . $_ENV['DB_USER'] . "\n";
  $envStr .= "DB_PASS " . $_ENV['DB_PASS'] . "\n";
  echo "Printing stuff now \n";
  return $envStr;
});

$klein->with('/items', function () use ($klein) {

  $klein->respond('GET', '/add/?', function($request, $response) {
    $control = new AddItemController();
    return $control->renderAddPage();
  });


  //    $klein->respond('GET', '/?', function ($request, $response) {
  //        // Generic Items Page
  //        return "Main Item Page";
  //    });
  //
  //
  //    $klein->respond('GET', '/[:id]', function ($request, $response) {
  //        // Show a single user
  //        $itemID = $request->id;
  //        $controller = new ViewItemController();
  //        return $controller->getListingPage($itemID);
  //
  //    });

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

  // NOW DEAL WITH API STUFF
  // This just returns JSON versions of the views, useful for javascript/testing.
  $klein->respond('POST', '/items/add', function ($request, $response, $service, $app) {
    // todo validate each field server side (and return false if not with an error message
    // Take in a JSON of things needed to add items
    // make a post request to add this item, and return whether it was successful or not (TODO return success from DB).
    $details = array(
      "itemname" => $request->itemname,
      "itemtype" => $request->itemType,
      "itemtypeother" => $request->otherreq
    );
    $control = new AddItemController();
    $control->addItem($details);
  });

  $klein->respond('POST', '/items/addimage', function($request,$response) {
    $files = $request->files();
    $controller = new AddItemController();
    $jsonOut = $controller->addItemImage($files);
    return $jsonOut; //this lists all image urls and their ids.
  });

  $klein->respond('GET', '/items/view/[:id]', function ($request, $response) {
    $itemID = $request->id;
    $controller = new ViewItemController();
    return $controller->getListingDetailsAsJSON($itemID);
  });

  // todo authenticate on messages. Must be logged in to view correct messages.
  $klein->with('/messages', function () use ($klein) {

    $klein->respond('GET', '/?', function ($request, $response) {
      // Show all conversations
      $controller = new ConversationListController();
      return $controller->generatePage();
    });

    $klein->respond('GET', '/[:conversationID]', function ($request, $response) {
      // view a specific conversation
      $conversationID = $request->conversationID;
      $controller = new MessageController();
      return $controller->generatePage($conversationID);
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

  $klein->respond('GET', '/conversation/[:listingID]', function ($request, $response) {
    // view a specific conversation
    console.log("Getting conversation");
    $listingID = $request->listingID;

    $controller = new MessageController();
    return $controller->generatePageFromListing($listingID);
  });
});

$klein->dispatch();
