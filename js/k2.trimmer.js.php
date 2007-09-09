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

/*	Thank you Drew McLellan for starting us off
	with http://24ways.org/2006/tasty-text-trimmer	*/

var k2Trimmer = {
	minValue: 0,
	maxValue: 100,
	chunks: false,
	prevValue: 0,

	setup: function(value) {
		k2Trimmer.chunks = false;

		if (value >= k2Trimmer.maxValue) {
			k2Trimmer.curValue = k2Trimmer.maxValue;
		} else if (value < k2Trimmer.minValue) {
			k2Trimmer.curValue = k2Trimmer.minValue;
		} else {
			k2Trimmer.curValue = value;
		}

		var initSlider = true;
		jQuery('#trimmertrack').Slider({
			accept: '#trimmerhandle',
			values: [[1000, 0]],
			fractions: 5,
			onSlide: function(xpct) {
				if (initSlider) {
					k2Trimmer.sliderOffset = this.dragCfg.gx;
				} else {
					k2Trimmer.doTrim(Math.round(xpct));
				}
			},
			onChange: function(xpct) {
				k2Trimmer.doTrim(Math.round(xpct));
			}
		});
		initSlider = false;

		jQuery('#trimmermore').click(function() {
			jQuery('#trimmertrack').SliderSetValues([
				[ k2Trimmer.sliderOffset, 0 ]
			]);

			return false;
		});

		jQuery('#trimmerless').click(function() {
			jQuery('#trimmertrack').SliderSetValues([
				[ -k2Trimmer.sliderOffset, 0 ]
			]);;

			return false;
		});
	},

	trimAgain: function() {
		k2Trimmer.loadChunks();
		k2Trimmer.doTrim(k2Trimmer.curValue);
	},

    loadChunks: function() {
		var everything = jQuery('#dynamic-content .entry-content');

		k2Trimmer.chunks = [];

		for (i=0; i<everything.length; i++) {
			k2Trimmer.chunks.push({
				ref: everything[i],
				html: jQuery(everything[i]).html(),
				text: jQuery.trim(jQuery(everything[i]).text())
			});
		}
	},

    doTrim: function(interval) {
		/* Spit out the trimmed text */
		if (!k2Trimmer.chunks)
			k2Trimmer.loadChunks();

		/* var interval = parseInt(interval); */
		k2Trimmer.curValue = interval;

		for (i=0; i<k2Trimmer.chunks.length; i++) {
			if (interval == k2Trimmer.maxValue) {
				jQuery(k2Trimmer.chunks[i].ref).html(k2Trimmer.chunks[i].html);
			} else if (interval == k2Trimmer.minValue) {
				jQuery(k2Trimmer.chunks[i].ref).html('');
			} else {
				var a = k2Trimmer.chunks[i].text.split(' ');
				a = a.slice(0, Math.round(interval * a.length / 100));
				jQuery(k2Trimmer.chunks[i].ref).html('<p>' + a.join(' ') + '&nbsp;[...]</p>');
			}
		}

		/* Add 'trimmed' class to <BODY> while active */
		if (k2Trimmer.curValue != k2Trimmer.maxValue) {
			jQuery('#dynamic-content').addClass("trimmed");
		} else {
			jQuery('#dynamic-content').removeClass("trimmed");
		}
	}
};