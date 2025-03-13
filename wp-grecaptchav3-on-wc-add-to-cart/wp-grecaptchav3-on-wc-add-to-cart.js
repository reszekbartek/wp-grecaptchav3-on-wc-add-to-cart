document.addEventListener("DOMContentLoaded", function(event) { 

		grecaptcha.ready(function() {
				grecaptcha.execute(php_vars.sitekey, {action: 'submit'}).then(function(token) {
						
						jQuery( '.add_to_cart_button' ).attr( 'data-token', token );
		
						var input = document.createElement("input");
						input.setAttribute("type", "hidden");
						input.setAttribute("name", "token");
						input.setAttribute("value", token);
						jQuery( '.summary form.cart' ).append(input);
					});
			});

		(function($){
				$(document.body).on('added_to_cart', function( event, fragments, cart_hash, button ) {
					grecaptcha.ready(function() {
							grecaptcha.execute(php_vars.sitekey, {action: 'submit'}).then(function(token) {
									jQuery( '.add_to_cart_button' ).attr( 'data-token', token );
								});
						});
					});
			})(jQuery);

	});