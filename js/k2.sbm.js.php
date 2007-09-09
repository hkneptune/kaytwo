<?php
	// check to see if the user has enabled gzip compression in the WordPress admin panel
	if ( ob_get_length() === FALSE and !ini_get('zlib.output_compression') and ini_get('output_handler') != 'ob_gzhandler' and ini_get('output_handler') != 'mb_output_handler' ) {
		ob_start('ob_gzhandler');
	}

	// The headers below tell the browser to cache the file and also tell the browser it is JavaScript.
	header("Cache-Control: public");
	header("Pragma: cache");

	$offset = 60*60*24*60;
	$ExpStr = "Expires: ".gmdate("D, d M Y H:i:s",time() + $offset)." GMT";
	$LmStr = "Last-Modified: ".gmdate("D, d M Y H:i:s",filemtime(__FILE__))." GMT";

	header($ExpStr);
	header($LmStr);
	header('Content-Type: text/javascript; charset: UTF-8');
?>

jQuery.noConflict();

jQuery('document').ready(
	function($) {
		// Next available module ID
		var lastModuleID = $('#next_id').text();
		
		// Set class as 'current sidebar' hack
		$('.sortable').children().attr('class', function () { return 'module ' + $(this).parent().attr('id') });

		// Set up drop zones for adding available modules
		$('.droppable').Droppable({
			accept:			'availablemodule', 
			activeclass:	'hovering', 
			tolerance:		'pointer',
			onHover:		function (drag) {
				// Show the temp 'result' marker
				$(drag)
					.clone()
					.attr('class', 'module marker')
					.css({ position: "static" })
					.append('<span class="type">'+$(drag).children().text()+'</span><a href="#" class="optionslink"> </a>')
					.appendTo($(this).children());
			},
			onOut: function (drag) {
				// Remove temp 'result' markers
				$(this).children().children('.marker').remove();
			},
			onDrop:	function (drag) {
				// Fetch the needed module info
				var module = $(drag).children('span.name').text();
				var type = $(drag).attr('id');
				var sidebar = $(this).children('ul').attr('id');

				// Create new module
				var newModule = $(drag).clone().empty()
									.html('<div><span class="name">'+$(drag).children().text()+'</span><span class="type">'+$(drag).children().text()+'</span><a href="#" class="optionslink"> </a></div>')
									.attr('id', 'module-' + (lastModuleID++))
									.attr('class', 'module ' + sidebar)
									.css({ position: "static" });

				// Show spinner on marker module
				$('.marker').addClass('spinner');

				// Submit new module info
				$.ajax({
					type: "POST",
					processData: false,
					url: sbm_baseUrl + '?action=add',
					data: "add_name=" + module + "&add_type=" + type + "&add_sidebar=" + sidebar,
					error: function(){
						// Remove temp markers
						$('.marker').remove();

						// Show an error message
//						$('#msg').text('An error occurred while adding module. Please try again.');
					},
					success: function(request, status){
						// Remove temp markers
						$('.marker').remove();

						// Clone dropped module to new home
						$('#'+sidebar).append(newModule);

						// Reinitialize the sortable lists
						destroySortables();
						initSortables();
					}
				});

			}
		});


		// Set up available modules as draggable
		$('.availablemodule').Draggable({ ghosting:	true, revert: true });


		// Config sortable lists
		var sortableLists = '';
		function initSortables() {
			sortableLists = $('ul.sortable').Sortable({
				accept: 		'module',
				activeclass:	'hovering',
				helperclass:	'sorthelper',
				tolerance:		'pointer',
				opacity:		0.5,
				onHover:		function(drag) {
					$('.sorthelper')
						.removeAttr('style')
						.html( $(drag).html() );
				},
				onChange:		function(serial) {
					// If something is being trashed
					var trashedModule = $.SortSerialize('trash').o.trash[0];
					console.log($('#'+trashedModule+' .name').text());

					// Show feedback
					if (trashedModule != undefined) {
						$("#msg")
							.text("'" + $('#'+trashedModule+' .name').text() + "' was trashed")
							.fadeIn(1000);

						setTimeout( function() { $('#msg').fadeOut('3000') }, 4000);

						// Get the trashed module's parent list
						var trashedFromList = $('#'+trashedModule).attr('class').split(' ')[1];

						// Fade trashed module
						$('#trash').children()
							.fadeOut('fast', function() {
								$('#trash').empty();
							});

						// Remove from database
						$.post(sbm_baseUrl + "?action=remove", {
							module_id:		trashedModule,
							sidebar_id:		trashedFromList
						}, function() {
							$("#loader").fadeOut(10000).empty();
						});

					// If the order has been changed
					} else {
						// Build New World Order
						var orderData = '';
						var lists = $('.reorderable');
						for (var j = 0; j < lists.length; j++) {
							var modules = $(lists[j]).children();

							for (var i = 0; i < modules.length; i++) {
								orderData += 'sidebar_ordering[' + $(lists[j]).attr('id') + '][' + i + ']=' + $(modules[i]).attr('id');

								if (i < modules.length - 1) orderData += "&";
							}

							if (j < lists.length - 1) orderData += "&";
						}

						// Submit New World Order to db
						$.ajax({
							type: "POST",
							processdata: false,
							url: sbm_baseUrl + '?action=reorder',
							data: orderData
					 	});
						
					}
				}
			});

			// Initialize the option links for each module
			initOptionLinks();
		};


		function tabSystem() {
			var tabContainer = $('.tabs');
			
			$(tabContainer)
				.children()
				.click(function() {
					$(this).addClass('selected')
						.siblings().removeClass('selected');
					
					$('.tabcontent').hide();
					
					// Show the tabs' content
					$('#' + $(this).attr('id') + '-content').show();

					return false;
				});

			$('#closelink')
				.click(closeOptions);
		}

		tabSystem();


		function destroySortables() {
			$('ul.sortable').SortableDestroy();
		}

		initSortables();


		// Options Stuff
		var curOptModule = '';
		var curOptSidebar = '';
		var curOptName = '';

		function initOptionLinks() {
			// Set up options buttons
			$('a.optionslink').each(function() {
				$(this).unbind();
				$(this).click(function() {
					curOptModule = $(this).parent().parent().attr('id');
					curOptSidebar = $(curOptModule).parent().attr('id');
					curOptName = $(this).siblings('.name').text();
					openOptions(curOptModule);
					return false;
				});
			});

			// Set up options submit process 
			$('#submit').unbind();
			$('#submit').click(function() {
				$(this).parents('form').trigger('submit');
				return false;
			});

			$('#submitclose').unbind();
			$('#submitclose').click(function() {
				$(this).parents('form').trigger('submit');
				closeOptions();
				return false;
			});

			$('#module-options-form').unbind();
			$('#module-options-form').submit(function() {
				$.ajax({
					type: "POST",
					processdata: false,
					url: sbm_baseUrl + '?action=update',
					data: "sidebar_id=" + curOptSidebar + "&module_id=" + curOptModule + "&" + $('#module-options-form').formSerialize(),
					success: function() {
						$('#'+curOptModule+' .name').text($('#module-name').val());
						$('#msg').text("Options for '" + $("#"+curOptModule+" .name").text() + "' saved successfully").fadeIn('1000');
						setTimeout( function() { $('#msg').fadeOut('3000'); }, 4000);
						cropTitles();
					}
				});

	        	return false;
	        });
		}


		// Auto-resize lists on window resize
		var secretFormula;
		function calculateSecretFormula() {
			// Calculate best width for lists
			secretFormula = parseInt($('.wrap').width() / $('.container').length)
				- ( parseInt($('.wrap').css('paddingRight')) + parseInt($('.wrap').css('paddingLeft')) )
				- ( parseInt($('.container').css('borderRightWidth')) + parseInt($('.container').css('borderLeftWidth')) ) - 2;

			// Ensure minimum and maximum sizes
			if (secretFormula < 150 ) { secretFormula = 150 }
			else if (secretFormula > 270 ) { secretFormula = 270 }
		}
		calculateSecretFormula();

		function resizeLists() {
			calculateSecretFormula();
			$('.container').width(secretFormula);
			cropTitles();
		}

		function cropTitles() {
			$('.croppedname').remove();
			$('.sortable>.module>div>.name').each(function() {
				var availableWidth = $(this).parents('li').width() - parseInt($(this).parents('li').css('paddingRight')) - parseInt($(this).parents('li').css('paddingRight')) - $(this).siblings('a.optionslink').width() - 10;
				var nameWidth = $(this).width();

				// If name doesn't fit
				if (nameWidth > availableWidth) {

					// Prepare cropped name
					$(this)
						.hide()
						.clone()
						.removeClass('name')
						.addClass('croppedname')
						.insertAfter( $(this) )
						.show()
						.each(function() {
							var crank = $(this).text();
							var life = '';
							
							// Resize name to fit
							while (life != 42) {
								crank = crank.substring(0, crank.length-1);
								$(this).html(crank+'&hellip;');

								// Are we done yet?
								if ($(this).width() < availableWidth) life = 42; 
							} // End While
						}); // End close & prep

				} // End if
			});
		} // End function
		
		$(window).resize(resizeLists);
		$('.container').width(secretFormula);
		cropTitles();

		

		// Options UI
		function openOptions(module) {
			var moduleID = '#' + module;

			var originalPosition = $(moduleID).offset({ margin:false, border:false });
			var originalWidth = $(moduleID).width()-8;
			var originalHeight = $(moduleID).height();
			var optionsWidth = 400;
			var optionsHeight = 350;
			var optionsX = ($(window).width()) / 2 - ((optionsWidth)/2);
			var optionsY = ($(window).height()) / 2 - (optionsHeight/2);
			var originalName = $(moduleID).children('.name').text();
			curOptModule = $(moduleID).attr('id');
			curOptSidebar = $(moduleID).parent().attr('id');

			// Dim screen
			$('#overlay').css({ zIndex: '500' }).fadeTo('normal', 0.5);

			$('#optionswindow')
				.addClass('optionsspinner')
				.show()
				.css({
					position: 'fixed',
					top: originalPosition.top,
					left: originalPosition.left,
					width: originalWidth,
					height: originalHeight,
					zIndex: '1000',
					opacity: '0'
				})
				.css({ top: optionsY, left: optionsX, width: optionsWidth, height: optionsHeight, opacity: 1 });

			// Get the options via AJAX
			$.post(
				sbm_baseUrl + "?action=control-show",
				{
					module_id: $(moduleID).attr('id')
				},
				function (data) {
					$('#options').empty().append(data);
					$('#module-name').focus();
					$('#optionswindow').removeClass('optionsspinner');
				}
			);
		}

		function closeOptions() {
			// Reset the tab system
			$('.tabs').children().removeClass('selected');
			$('#optionstab').addClass('selected');

			$('#options').empty();
			$('#optionswindow').hide();
			// Dim overlay
			$('#overlay').fadeTo('normal', 0, function() { $(this).css({ zIndex: '-100' }) });
			return false;
		}

		// Ready overlay
		$('#overlay').fadeTo('normal', 0);

		$('#msg').hide();

		// Remove any new messages on load
/*		function messageHandler() {
			var messageContainer = $('#msg');
			if ($(messageContainer).text() == '') {
				$(messageContainer).hide();
			} else {
				$(messageContainer).fadeOut(10000).text()
			}
		}
		messageHandler();*/
	}
)