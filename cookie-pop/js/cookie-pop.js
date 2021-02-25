/* global cookie_pop_text */
(function ( $ ) {
  
    'use strict';
  
    if ( 'set' !== $.cookie( 'cookie-pop' ) ) {
  
        $('body').prepend(
            '<div class="cookie-pop">' + cookie_pop_text.message + '<button id="accept-cookie">' + cookie_pop_text.button + '</button> <a target="blank" href="/cookies/">' + cookie_pop_text.more + '</a></div>'
              
            );
  
        $( '#accept-cookie' ).click(function () {
  
            $.cookie( 'cookie-pop', 'set' );
            $( '.cookie-pop' ).remove();
  
        });
  
    }
  
}( jQuery ) );