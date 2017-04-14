/**
 * Created by Stephen on 14/04/2017.
 */

$(function() {
    var baseURL = window.location.protocol + "//" + window.location.host;
    var categoryID = 1; // Start with category 1

    // 6 colours for graph background
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

    createRequestTagsChart(categoryID); // Create first Requests chart
    createRequestChartButtons(); // Create the radio buttons for the chart

    // Get categories from /analysis/categories in JSON format
    // $.each(json, function(id, name){
    //      // Add id and name to radio group of buttons #requestChartRadioButtons
    // });


    // categoryIDs is array of categoryIDs to search for when getting tags
    // will only ever contain one categoryID
    function createRequestTagsChart(categoryID) {
        // Get request tags frequency data from the analysis controller
        var url = baseURL + "/analysis/get-request-tags/" + categoryID;
        console.log(url);
        $.getJSON(url, function(json){
            console.log(json);

            var labels = []; // Labels of bars
            var data = []; // Number for each bar
            var indexCounter = 0; // index into colour arrays (taken mod 6)
            var chartBackgroundColours = [];
            var chartBorderColours = [];

            // Extract details from JSON
            $.each(json, function (key, value) {
                labels.push(key);
                data.push(value);
                chartBackgroundColours.push(backgroundColours[indexCounter]);
                chartBorderColours.push(borderColours[indexCounter]);
                indexCounter = (indexCounter + 1) % 6; // Taken mod 6 to loop through available colours
            });

            console.log(labels);
            console.log(data);

            // Use data to populate chart
            var ctx = $("#requestTagsChart"); // Need to put this in the twig file

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
                },
            });

        });
    }


    function createRequestChartButtons(){
        // Get request tags frequency data from the analysis controller
        var url = baseURL + "/analysis/categories";

        $.getJSON(url, function(json){
            console.log("CATEGORIES");
            console.log(json);
            // Put category data onto radio buttons
            $.each(json, function(id, name){
                console.log(id);
                console.log(name);
                var radioLabel = $("label");
                radioLabel.addClass("radio-inline");
                var html = "<input type='radio' name='requestOption' value = "+id+">"+name;
                console.log(html);
                radioLabel.html(html);

                $("#requestRadioButtons").append(radioLabel);
            });
        });
    }
});

// Bar chart version
// var myChart = new Chart(ctx, {
//     type: 'bar',
//     data: {
//         labels: labels,
//         datasets: [{
//             label: '# of occurences',
//             data: data,
//             backgroundColor: chartBackgroundColours,
//             borderColor: chartBorderColours,
//             borderWidth: 1
//         }]
//     },
//     options: {
//         scales: {
//             yAxes: [{
//                 ticks: {
//                     beginAtZero: true,
//                     callback: function(value) {if (value % 1 === 0) {return value;}}
//                 }
//             }]
//         }
//     }
// });