/**
 * Helps with accessibility for keyboard only users.
 * Learn more: https://git.io/vWdr2
 */
(function() {
	var isIe = /(trident|msie)/i.test( navigator.userAgent );

	if ( isIe && document.getElementById && window.addEventListener ) {
		window.addEventListener( 'hashchange', function() {
			var id = location.hash.substring( 1 ),
				element;

			if ( ! ( /^[A-z0-9_-]+$/.test( id ) ) ) {
				return;
			}

			element = document.getElementById( id );

			if ( element ) {
				if ( ! ( /^(?:a|select|input|button|textarea)$/i.test( element.tagName ) ) ) {
					element.tabIndex = -1;
				}

				element.focus();
			}
		}, false );
	}
})();

function toggler(menuVar){
    $('#'+menuVar).toggle();
}

// var bLazy = new Blazy({
// 	breakpoints: [{
// 		width: 420, // Max-width
// 	  	src: 'data-src'
// 	}], 
// 	success: function(element){
// 		setTimeout(function(){
// 			// We want to remove the loader gif now.
// 			// First we find the parent container
// 			// then we remove the "loading" class which holds the loader image
// 			var parent = element.parentNode;
// 			parent.className = parent.className.replace(/\bloading\b/,'');
// 		}, 200);
// 	}
// });

(function() {
	var bLazy = new Blazy();
})();