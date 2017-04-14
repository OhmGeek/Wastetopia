/**
 * Created by Stephen on 14/04/2017.
 */
// 6 colours for graph background
$(function() {
    //Chart.defaults.global.maintainAspectRatio = false;
    Chart.defaults.global.title.display = true; // Display the title

    var myRequestChart;
    var mySendingChart;

    var baseURL = window.location.protocol + "//" + window.location.host;
    var categoryID = 1; // Start with category 1

    // Create request chart and add buttons
    createTagsChart(categoryID, 1); // Create first Requests chart
    createChartButtons("requestOption", "requestRadioButtons"); // Create the radio buttons for the chart

    // Create sending chart and add buttons
    createTagsChart(categoryID, 0); // Create first sending chart
    createChartButtons("sendingOption", "sendingRadioButtons"); // Create the radio buttons for the chart

    // NEED MORE DISTINCT COLOURS (Perhaps generate dynamically depending on size of group)
    var backgroundColours = [
        'rgba(255, 99, 132, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(255, 206, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(255, 159, 64, 0.2)'
    ];

// 6 colours for borders - matches background colours
    var borderColours = [
        'rgba(255,99,132,1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)'
    ];

// categoryIDs is array of categoryIDs to search for when getting tags
// will only ever contain one categoryID
// requestsOrOffers - boolean (1 for request chart, 0 for Offers chart)
    function createTagsChart(categoryID, requestsOrOffers) {

        // Get appropirate tags frequency data from the analysis controller
        var relativeURL = requestsOrOffers ? "/analysis/get-request-tags/" : "/analysis/get-sending-tags/"
        var url = baseURL + relativeURL + categoryID;


        $.getJSON(url, function(json){
            var labels = []; // Labels of bars
            var data = []; // Number for each bar
            var indexCounter = 0; // index into colour arrays (taken mod 6 so it loops through colours)
            var chartBackgroundColours = [];
            var chartBorderColours = [];

            // Extract details from JSON
            $.each(json, function (key, value) {
                labels.push(key);
                data.push(value);
                chartBackgroundColours.push(backgroundColours[indexCounter]); // Get background colour
                chartBorderColours.push(borderColours[indexCounter]);   // Get border colour
                indexCounter = (indexCounter + 1) % 6; // Taken mod 6 to loop through available colours
            });



            // Get correct canvas
            var canvasID = requestsOrOffers ? "requestTagsChart" : "sendingTagsChart";
            var action = requestsOrOffers ? "request" : "give away"; // Action to put in title

            var ctx = $("#"+canvasID); // Get correct canvas

            // Use data to populate chart
            var myChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '# of occurences',
                        data: data,
                        backgroundColor: chartBackgroundColours,
                        borderColor: chartBorderColours,
                        borderWidth: 1
                    }]
                }
            });

            // Pass reference to correct place
            if(requestsOrOffers){
                myRequestChart = myChart;
            }else{
                mySendingChart = myChart;
            }

        });
    }

// optionName - name for the "name" parameter of radio buttons (requestOption) or (senderOption)
// location - id of div for the buttons to go in
    function createChartButtons(optionName, location){

        // Get request tags frequency data from the analysis controller
        var url = baseURL + "/analysis/categories";

        $.getJSON(url, function(json){
            var radioButtonsHTML = "";

            // Put category data onto radio buttons
            $.each(json, function(id, name){
                if (id == 1){
                    var html = "<input type='radio' name=" + optionName + " value = " + id + " checked>  <label>" + name + "</label> <br>";
                }else {
                    var html = "<input type='radio' name=" + optionName + " value = " + id + ">  <label>" + name + "</label> <br>";
                }

                radioButtonsHTML += html;
            });

            $("#"+location).html(radioButtonsHTML);
        });
    }

    // Click handler for radio buttons
    $(document).on('click', 'input[type="radio"]', function(event){
        var categoryValue = $(this).val(); // Value of button clicked

        var form = $(this).parent('form'); // Form it's in
        var formID = form.attr('id'); // ID of form (requestRadioButtons or sendingRadioButtons)


        if (formID === "requestRadioButtons"){
          // myRequestChart.destroy(); // Destroy so it can be redrawn
          // createTagsChart(categoryValue, 1); // Create the requests tag chart
            updateChart(myRequestChart, 1, categoryValue); // Try new function
        }else{
            // Create sending tags chart
            mySendingChart.destroy(); // Destroy so it can be redrawn
            createTagsChart(categoryValue, 0); // Create the sending tag chart
        }
    });


    // Changes the values of the chart and redraws it
    // Chart - chart to be updated
    // requestsOrOffers - boolean (1 for requests, 0 for offers)
    // categoryID - int, new category value to search for
    function updateChart(chart, requestsOrOffers, categoryID){
        // Get appropirate tags frequency data from the analysis controller
        var relativeURL = requestsOrOffers ? "/analysis/get-request-tags/" : "/analysis/get-sending-tags/"
        var url = baseURL + relativeURL + categoryID;



        $.getJSON(url, function(json) {
            var labels = []; // Labels of bars
            var data = []; // Number for each bar
            var indexCounter = 0; // index into colour arrays (taken mod 6 so it loops through colours)
            var chartBackgroundColours = [];
            var chartBorderColours = [];

            // Extract details from JSON
            $.each(json, function (key, value) {
                labels.push(key);
                data.push(value);
                chartBackgroundColours.push(backgroundColours[indexCounter]); // Get background colour
                chartBorderColours.push(borderColours[indexCounter]);   // Get border colour
                indexCounter = (indexCounter + 1) % 6; // Taken mod 6 to loop through available colours
            });

            chart.datasets.data = data; // Change the data
            chart.labels = labels; // Change the labels

            chart.update(); // Redraw with new data
        });

    }
});

