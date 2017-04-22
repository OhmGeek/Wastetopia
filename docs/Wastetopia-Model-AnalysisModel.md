Wastetopia\Model\AnalysisModel
===============

Class AnalysisModel - Functions to get frequencies of Tags and Names




* Class name: AnalysisModel
* Namespace: Wastetopia\Model







Methods
-------


### __construct

    mixed Wastetopia\Model\AnalysisModel::__construct()

AnalysisModel constructor.



* Visibility: **public**




### getUserID

    string Wastetopia\Model\AnalysisModel::getUserID()

Returns the ID of the user currently logged in



* Visibility: **private**




### getTagFrequenciesForListings

    array Wastetopia\Model\AnalysisModel::getTagFrequenciesForListings($userID, $categoryIDArray)

Gets a list of Tag Names along with their frequencies for current user's listings (includes current quantity and transactions quantity)



* Visibility: **public**


#### Arguments
* $userID **mixed**
* $categoryIDArray **mixed** - &lt;ul&gt;
&lt;li&gt;Array of CategoryIDs to match: Optional - defaults to empty array =&gt; checks all category IDs&lt;/li&gt;
&lt;/ul&gt;



### getTagFrequenciesForTransactions

    array Wastetopia\Model\AnalysisModel::getTagFrequenciesForTransactions($categoryIDArray)

Gets a list of Tag Names along with their frequencies for items the user has received



* Visibility: **public**


#### Arguments
* $categoryIDArray **mixed** - &lt;p&gt;(Optional - defaults to empty array =&gt; checks all category IDs. Array of CategoryIDs to match)&lt;/p&gt;



### getTotalNameFrequenciesSending

    array Wastetopia\Model\AnalysisModel::getTotalNameFrequenciesSending($userID)

Returns the frequencies of Names of items user is giving away
Frequncy calculated as SUM of quantities for successful transactions + SUM of current quantity left



* Visibility: **public**


#### Arguments
* $userID **mixed**



### getTotalNameFrequenciesReceiving

    array Wastetopia\Model\AnalysisModel::getTotalNameFrequenciesReceiving()

Returns the frequencies of Names of items user is giving away
Frequncy calculated as SUM of quantities for successful transactions + SUM of current quantity left



* Visibility: **public**




### getCategoryNamesAndIDs

    array Wastetopia\Model\AnalysisModel::getCategoryNamesAndIDs()

Returns array of category names and IDs



* Visibility: **public**




### getTagNamesFromCategory

    array Wastetopia\Model\AnalysisModel::getTagNamesFromCategory($categoryID)

Returns array of TagNames for the given category



* Visibility: **public**


#### Arguments
* $categoryID **mixed**



### getNumberOfCompletedGiving

    Integer Wastetopia\Model\AnalysisModel::getNumberOfCompletedGiving($year, $month, $timespan)

Gets the total number of completed listings the user has given
If neither year nor month are specified then all completed listings are evaluated, regardless of date



* Visibility: **public**


#### Arguments
* $year **mixed** - &lt;p&gt;The subject year.&lt;/p&gt;
* $month **mixed** - &lt;p&gt;The subject month&lt;/p&gt;
* $timespan **mixed** - &lt;p&gt;How many months to evaluate, default 1&lt;/p&gt;



### getNumberOfCompletedReceived

    Integer Wastetopia\Model\AnalysisModel::getNumberOfCompletedReceived($year, $month, $timespan)

Gets the total number of completed listings the user has received
If neither year nor month are specified then all completed listings are evaluated, regardless of date



* Visibility: **public**


#### Arguments
* $year **mixed** - &lt;p&gt;The subject year&lt;/p&gt;
* $month **mixed** - &lt;p&gt;The subject month&lt;/p&gt;
* $timespan **mixed** - &lt;p&gt;How many months to evaluate, default 1&lt;/p&gt;


