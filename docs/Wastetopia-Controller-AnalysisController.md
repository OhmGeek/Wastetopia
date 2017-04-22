Wastetopia\Controller\AnalysisController
===============

Class AnalysisController - Used to analyse the user&#039;s listings and transactions




* Class name: AnalysisController
* Namespace: Wastetopia\Controller







Methods
-------


### __construct

    mixed Wastetopia\Controller\AnalysisController::__construct()

AnalysisController constructor.



* Visibility: **public**




### generatePage

    mixed Wastetopia\Controller\AnalysisController::generatePage()

Generates the HTML for the analysis page



* Visibility: **public**




### getTagFrequenciesForListingsJSON

    \Wastetopia\Controller\JSON Wastetopia\Controller\AnalysisController::getTagFrequenciesForListingsJSON($categoryIDArray)

Gets a list of Tag Names along with their frequencies for current user's listings in JSON format



* Visibility: **public**


#### Arguments
* $categoryIDArray **mixed** - &lt;p&gt;(Optional - defaults to empty array =&gt; checks all category IDs. Array of CategoryIDs to match)&lt;/p&gt;



### getTagFrequenciesForTransactionsJSON

    \Wastetopia\Controller\JSON Wastetopia\Controller\AnalysisController::getTagFrequenciesForTransactionsJSON($categoryIDArray)

Gets a list of Tag Names along with their frequencies for items the user has received in JSON format



* Visibility: **public**


#### Arguments
* $categoryIDArray **mixed** - &lt;p&gt;(Optional - defaults to empty array =&gt; checks all category IDs. Array of CategoryIDs to match)&lt;/p&gt;



### getCategoryDetailsJSON

    string Wastetopia\Controller\AnalysisController::getCategoryDetailsJSON()

Get category names and IDs in JSON format



* Visibility: **public**




### getTotalNameFrequenciesSending

    array Wastetopia\Controller\AnalysisController::getTotalNameFrequenciesSending($userID)

Returns array of top 5 names on items user has given away



* Visibility: **public**


#### Arguments
* $userID **mixed**



### getTotalNameFrequenciesReceiving

    array Wastetopia\Controller\AnalysisController::getTotalNameFrequenciesReceiving()

Returns array of top 5 names on items user has received



* Visibility: **public**




### getMostFrequentItemNameSent

    mixed Wastetopia\Controller\AnalysisController::getMostFrequentItemNameSent()

Gets the most frequent item name given away



* Visibility: **public**




### getMostFrequentTypeTagSent

    mixed Wastetopia\Controller\AnalysisController::getMostFrequentTypeTagSent()

Gets the most frequent tag for Type category on items user gives away



* Visibility: **public**




### get4MonthSendArray

    mixed Wastetopia\Controller\AnalysisController::get4MonthSendArray()

Returns an array of the number of items given in the last 4 months



* Visibility: **public**




### get4MonthReceiveArray

    mixed Wastetopia\Controller\AnalysisController::get4MonthReceiveArray()

Returns an array of the number of items given in the last 4 months



* Visibility: **public**



