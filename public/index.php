<?php

require_once '../vendor/autoload.php';
use Klein\Klein;
use Wastetopia\Controller\AddItemController;
use Wastetopia\Controller\ConversationListController;
use Wastetopia\Controller\EditItemController;
use Wastetopia\Controller\LoginController;
use Wastetopia\Controller\ViewItemController;
use Wastetopia\Config\CurrentConfig;
use Wastetopia\Controller\MessageController;
use Wastetopia\Model\NotificationModel;


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


$klein->with("/register", function() use ($klein){
    $klein->respond("GET", "/?", function() {
        $controller = new RegistrationController();
        return $controller->generatePage();    
    });
    
    $klein->respond("POST", "/add-user", function($request,$response){
       $firstName = $request->firstName;
        $lastName = $request->lastName;
        $email = $request->email;
        $password = $request->password;
        $passwordConfirm = $request->passwordConfirm;
        $pictureURL = $request->pictureURL;
        
        $controller = new RegistrationController();
        return $controller->addUser($firstName, $lastName, $email, $password, $passwordConfirm, $pictureURL);
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

  $klein->respond('GET', '/edit/[:id]', function($request, $response) {
        $control = new EditItemController($request->id);
        return $control->renderEditPage();
    });


    $klein->respond('GET', '/view/[:id]', function ($request, $response) {
          // Generic Items Page
          $itemID = $request->id;
          $controller = new ViewItemController();
          return $controller->getListingPage($itemID);

      });


//      $klein->respond('GET', '/?', function ($request, $response) {
//          return "Main Items";
//      });

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
  $klein->respond('POST', '/items/additem', function ($request, $response, $service, $app) {
    // todo validate each field server side (and return false if not with an error message
    // Take in a JSON of things needed to add items
    // make a post request to add this item, and return whether it was successful or not (TODO return success from DB).

    $control = new AddItemController();
    $item = json_decode($request->body(),true);
    $control->addItem($item);
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

    $klein->respond('GET', '/notifications/update', function($request, $response){
        $model = new NotificationModel();
        return $model->getAll(1); // getAll in JSON format
    });

    $klein->respond('GET', '/conversation/[:listingID]', function ($request, $response) {
        // view a specific conversation
        console.log("Getting conversation");
        $listingID = $request->listingID;
        $controller = new MessageController();
        return $controller->generatePageFromListing($listingID);
    });
});

$klein->respond('POST', '/api/barcode/get', function($request, $response) {
    // first of all, get the file we just posted.
    $file = $_FILES['file'];
    $filename = $file['tmp_name'];
    // now let's define the things we need to send the request:
    $headers = array("Content-Type:multipart/form-data"); // cURL headers for file uploading
    $postfields = array(
        'f' => new \CURLFile($filename, urldecode($file['type']), $file['name'])
    );
    error_log(json_encode($request->files()));
    error_log(json_encode($file));
    error_log(json_encode($postfields));

    $curl = curl_init();
    $timeout = 1000;
    $ret = "";
    $url="https://zxing.org/w/decode";
    curl_setopt ($curl, CURLOPT_URL, $url);
    curl_setopt ($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt ($curl, CURLOPT_HEADER, true);
    curl_setopt ($curl, CURLOPT_HTTPHEADER, $headers);
    //curl_setopt ($curl, CURLOPT_MAXREDIRS, 20);
    curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt ($curl, CURLOPT_POSTFIELDS, $postfields);
    curl_setopt ($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5");
    //curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
    $text = curl_exec($curl);
    error_log(curl_getinfo($curl,CURLINFO_HTTP_CODE));
    error_log(curl_error($curl)); //get error
    curl_close($curl);
    error_log($text);
    return $text;
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
