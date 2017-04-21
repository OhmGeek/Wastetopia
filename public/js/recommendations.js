var grid;
$(function () {
  $('.grid').imagesLoaded().progress( function() {
      grid = $('.grid').isotope({
        itemSelector: '.grid-item',
        percentPosition: true,
        layoutMode: 'masonry'
      });
      grid.isotope('layout');
    });
});
