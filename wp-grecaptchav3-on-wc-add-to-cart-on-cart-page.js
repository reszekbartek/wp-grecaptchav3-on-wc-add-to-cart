document.addEventListener("DOMContentLoaded", function(event) { 
		grecaptcha.ready(function() {
				grecaptcha.execute(php_vars.sitekey, {action: 'submit'}).then(function(token) {
						jQuery.ajaxSetup({ beforeSend: function(xhr, settings) {
								// Sprawdzamy, czy URL zawiera "/?wc-ajax=add_to_cart"
								if (settings.url.includes("/?wc-ajax=add_to_cart")) {
										// Dodajemy nowy parametr do wysyłanych danych
										if (typeof settings.data === "string") {
												settings.data += "&token=" + token;
											} else if (typeof settings.data === "object") {
												settings.data.token = token;
											}
									}
							}});
					});
			});
	});