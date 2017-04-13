$(document).ready(function(){
    var searchBox = $('#searchBox')
    var baseURL = $('#baseURL').attr('href')
    searchBox.on('keydown', function(event){
        if(event.which === 13)
        {
            var search = searchBox.val()
            location.href = baseURL + '/search/' + search;
        }
    });
});