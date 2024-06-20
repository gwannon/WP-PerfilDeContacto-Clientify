var qsRegex;
var selectedSelects = [];

var iso = jQuery('.asociadas-grid').isotope({
  // options
  itemSelector: '.asociadas-item',
  layoutMode: 'fitRows',
  filter: function() {
    if(qsRegex) {
      if((jQuery(this).text()+jQuery(this).data("search")).match( qsRegex )) {
        if(selectedSelects.length === 0) {
          if (quicksearch.value.length >= 3) jQuery(this).addClass('showimage');
          return true;
        } else {
          var control = 0;  
          selectedSelects.forEach((element) => {
            if (jQuery(this).hasClass(element)) control++;
          });
          if(control == selectedSelects.length) {
            if(quicksearch.value.length >= 3 && control > 0) jQuery(this).addClass('showimage');
            return true;
          } else return false;
        }
      } else {
        return false;
      }
    } else {
      if(selectedSelects.length === 0) {
        return true;
      } else {
        console.log();
        var control = 0;  
        selectedSelects.forEach((element) => {
          if (jQuery(this).hasClass(element)) control++;
        });
        if(control == selectedSelects.length) {
          if(control > 0) jQuery(this).addClass('showimage');
          return true;
        } else return false;
      }
      return true;
    } 
  },
});

//chequeamos los valores elegidos en los selectes
document.querySelectorAll(".filters-button-group select").forEach((element) => {
  element.addEventListener('change',function(){
    jQuery("#noresults").removeClass("show");
    selectedSelects = [];
    document.querySelectorAll(".filters-button-group select").forEach((element) => {
      if(element.value != '') selectedSelects.push(element.value);
    });
    iso.isotope();
  });
});


jQuery(window).on("resize", function(event){
  iso.isotope();
});


function layoutComplete() {
  var totalfiltereds = jQuery('.asociadas-grid').data('isotope').filteredItems.length;
  jQuery("#numberresults > b").text(totalfiltereds);
}
iso.on( 'layoutComplete', layoutComplete );

jQuery(window).on("resize", function(event){
  iso.isotope();
});


// use value of search field to filter
var quicksearch = document.querySelector('.quicksearch');
quicksearch.addEventListener( 'keyup', debounce( function() {
  qsRegex = new RegExp( quicksearch.value.toLowerCase().normalize("NFD").replace(/\p{Diacritic}/gu, ""), 'gi' );
  iso.isotope();
}, 200 ) );

// debounce so filtering doesn't happen every millisecond
function debounce( fn, threshold ) {
  var timeout;
  threshold = threshold || 100;
  return function debounced() {
    clearTimeout( timeout );
    var args = arguments;
    var _this = this;
    function delayed() {
      fn.apply( _this, args );
    }
    timeout = setTimeout( delayed, threshold );
  };
}

//LAZY load de imagenes de asocaidas
jQuery(window).load(function() {
  lazyLoad();
});

function lazyLoad() {
  jQuery(".asociadas-grid > div.asociadas-item.showimage  + div.asociadas-item:not(.showimage)").addClass("showimage");
  setTimeout(lazyLoad, 200);
}