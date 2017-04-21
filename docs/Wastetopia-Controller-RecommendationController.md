Wastetopia\Controller\RecommendationController
===============

Class RecommendationController - Used to generate HTML sections for Recommendations and Predictions




* Class name: RecommendationController
* Namespace: Wastetopia\Controller







Methods
-------


### __construct

    mixed Wastetopia\Controller\RecommendationController::__construct()

RecommendationController constructor.



* Visibility: **public**




### getUserID

    integer Wastetopia\Controller\RecommendationController::getUserID()

Returns the ID of the user currently logged in



* Visibility: **private**




### isUserLoggedIn

    boolean Wastetopia\Controller\RecommendationController::isUserLoggedIn()

Returns True if getUserID doesn't return "" or null



* Visibility: **public**




### generateRecommendedSection

    mixed Wastetopia\Controller\RecommendationController::generateRecommendedSection()

Generates the HTML for the cards in a recommended section



* Visibility: **public**




### generatePredictionSection

    \Wastetopia\Controller\HTML Wastetopia\Controller\RecommendationController::generatePredictionSection($userID)

Generates the HTML for the cards in a prediction of similar items you may give away section



* Visibility: **public**


#### Arguments
* $userID **mixed**


