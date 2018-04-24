(function( $ ) {
 
    $.fn.progress = function( action ) {
 
        if ( action === "open") {
           $(this).loading('start');
        }
 
        if ( action === "close" ) {
            $(this).loading('stop');
        }
 
    };
 
}( jQuery ));