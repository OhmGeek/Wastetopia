/**
 * Created by Stephen on 14/04/2017.
 */
 var myRequestChart; // Chart for tags on items user Requests
 var mySendingChart; // Chart for tags on items user Gives away
$(function() {

    //Chart.defaults.global.maintainAspectRatio = false;
    //Chart.defaults.global.title.display = true; // Display the title

    var baseURL = window.location.protocol + "//" + window.location.host;
    var categoryID = 1; // Start with category 1 by default


    // Only make the charts when the user clicks on the link
    $(document).on('click', 'a[href="#analysis"]', function(event){

        // Create request chart and add buttons
        createTagsChart(categoryID, 1); // Create first Requests chart
        createChartButtons("requestOption", "requestRadioButtons"); // Create the radio buttons for the chart

        // Create sending chart and add buttons
        createTagsChart(categoryID, 0); // Create first sending chart
        createChartButtons("sendingOption", "sendingRadioButtons"); // Create the radio buttons for the chart
        // month Chart!
        monthChart();
    });

    // Remove charts when not on this page
    $(document).on('click', 'a[href="#home"]', function(event){
       if($("#analysis").hasClass("active")){
           // Remove the charts
           mySendingChart.destroy();
           myRequestChart.destroy();
       }
    });


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


    // Creates a pie chart
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

    // Creates radio buttons underneath chart
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
                    var html = "<div class = 'input-group'><input type='radio' name=" + optionName + " value = " + id + " checked>  <label>" + name + "</label></div>";
                }else {
                    var html = "<div class = 'input-group'><input type='radio' name=" + optionName + " value = " + id + ">  <label>" + name + "</label></div>";
                }

                radioButtonsHTML += html;
            });

            $("#"+location).html(radioButtonsHTML);
        });
    }

    // Click handler for radio buttons
    $(document).on('click', 'input[type="radio"]', function(event){
        var categoryValue = $(this).val(); // Value of button clicked

        var form = $(this).parent(".input-group").parent('form'); // Form it's in
        var formID = form.attr('id'); // ID of form (requestRadioButtons or sendingRadioButtons)

        // Update appropriate chart
        if (formID === "requestRadioButtons"){
            updateChart(myRequestChart, 1, categoryValue); // Try new function
        }else{
            updateChart(mySendingChart, 0, categoryValue);
        }
    });


    // Updates the given chart
    // Chart - chart to be updated (myRequestChart or mySendingChart)
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


            chart.config.data.datasets[0].data = data; // Change the data
            chart.config.data.labels = labels; // Change the labels
            chart.config.data.datasets[0].backgroundColor = chartBackgroundColours; // Change the colours
            chart.config.data.datasets[0].borderColor = chartBorderColours; // Change the colours

            chart.update(); // Redraw with new data
        });

    }

    function monthChart() {
        var url = baseURL + '/analysis/get-contributions-send/';
        $.getJSON(url, function(json) {
            console.log("Get send json");
            console.log(json);
            var data = {
                labels : ["Month1","Month2","Month3","Month4"],
                datasets :[
                    {
                        label : "Your Contributions",
                        fillColor :"rgba(255, 51, 204,0.75)",
                        strokeColor :"rgba(23,12,102,0.75)",
                        pointColor: "rgba(1,200,200,1)",
                        pointStrokeColor : "#fff",
                        pointHighLightFill: "#fff",
                        pointHighLightStroke: "rgba(200,200,200,1)",
                        data: [23,34,13,1]
                    },
                    {
                        label : "# of items taken",
                        fillColor :'rgba(0, 255, 255,0.75)',
                        strokeColor :'rgba(23,12,102,0.75)',
                        pointColor: 'rgba(200,2,200,1)',
                        pointStrokeColor : '#fff',
                        pointHighLightFill: '#fff',
                        pointHighLightStroke: 'rgba(200,200,200,1)',
                        data: json
                    }
                ]
            };
            var option  = {};

            var ctx = document.getElementById("cvsMonthChart").getContext("2d");
            var linechart = new Chart(ctx).Line(data,option);
        });
    }
});
