/* START CUSTOM JQUERY =================================================================== */

jQuery(document).ready(function(){

/*----------------------------------*/
/*		    Navigation
/*----------------------------------*/
	
	jQuery('ul.sf-menu').supersubs({
		minWidth: 14,
		maxWidth: 28,
		extraWidth: 1
	}).superfish({
		delay: 0,
		speed: 'fast',
		disableHI: true,
		animation: {opacity:'show', height:'show'}
	});
							
	var $responsive_nav = jQuery("<select />");	
	jQuery("<option />", {"selected": "selected", "value": "", "text": "Select a page"}).appendTo($responsive_nav);
	$responsive_nav.appendTo(".navigation-wrapper");
	
	jQuery(".navigation-wrapper ul li a").each(function(){
		var nav_url = jQuery(this).attr("href");
		var nav_text = jQuery(this).text();
		if (jQuery(this).parents("li").length == 2) { nav_text = '- ' + nav_text; }
		if (jQuery(this).parents("li").length == 3) { nav_text = "-- " + nav_text; }
		if (jQuery(this).parents("li").length > 3) { nav_text = "--- " + nav_text; }
		jQuery("<option />", {"value": nav_url, "text": nav_text}).appendTo($responsive_nav)
	})
	
	field_id = ".navigation-wrapper select";
	jQuery(field_id).change(function()
	{
	   value = jQuery(this).attr('value');
	   window.location = value;

	});

/*----------------------------------*/
/*		   Image overlays
/*----------------------------------*/
	
	jQuery(".lightbox-photo .icon").css({ opacity: 0 });
	jQuery(".lightbox-photo .icon").css({ top: -100 });

	jQuery('.lightbox-photo a').bind('mouseenter',function(){
		var $ele = jQuery(this);
			// portfolio icon
		$ele.find('.icon').stop(true).animate({ 'opacity': 1, 'top':'50%' }, 300).andSelf()
			.find('img').stop(true).animate({ 'opacity': 0 }, 300).andSelf()
		
	}).bind('mouseleave',function(){
		var $ele = jQuery(this);
			// portfolio icon	
		$ele.find('.icon').stop(true).animate({ 'opacity': 0, 'top':'-100px' }, 200).andSelf()
			.find('img').stop(true).animate({ 'opacity': 1 }, 300).andSelf()
	});

	jQuery('.lightbox-content-photo a').bind('mouseenter',function(){
		var $ele = jQuery(this);
			// portfolio icon
		$ele.find('img').stop(true).animate({ 'opacity': 0.5 }, 300).andSelf()
		
	}).bind('mouseleave',function(){
		var $ele = jQuery(this);
			// portfolio icon	
		$ele.find('img').stop(true).animate({ 'opacity': 1 }, 300).andSelf()
	});

// For latest section and portfolio items

	jQuery(".item-hover .hlp-description, .item-hover .portfolio-item-description").css({ opacity: 0 });
	jQuery(".item-hover .hlp-description, .item-hover .portfolio-item-description").css({ top: -100 });

	jQuery('.item-hover').bind('mouseenter',function(){
		var $ele = jQuery(this);
			// portfolio description
		$ele.find('.hlp-description, .portfolio-item-description').stop(true).animate({ 'opacity': 1, 'top':'0px' }, 300).andSelf()
		
	}).bind('mouseleave',function(){
		var $ele = jQuery(this);
			// portfolio description	
		$ele.find('.hlp-description, .portfolio-item-description').stop(true).animate({ 'opacity': 0, 'top':'-100px' }, 200).andSelf()
	});

/*----------------------------------*/
/*	     jQuery UI Tools
/*----------------------------------*/

	jQuery(".accordion").accordion ({
		header: "h3"
	});

	jQuery(".toggle div").hide(); // hide div on default
	jQuery(".toggle h3").click(function(){ // set the trigger
		jQuery(this).toggleClass("active").next().slideToggle(300); // add class active and toggle speed
		return false;
	});

	jQuery(".tabs").tabs({ fx: { opacity: 'show' } });

/*----------------------------------*/
/*		  Gallery Shortcode
/*----------------------------------*/

	// adding data-rel="prettyPhoto[gallery]" to the gallery shortcode
	jQuery('.gallery ul li a').each(function(){
        jQuery(this).attr('data-rel','prettyPhoto[gallery]');
	});

/*----------------------------------*/
/*		PrettyPhoto plugin
/*----------------------------------*/

	jQuery("a[data-rel^='prettyPhoto']").prettyPhoto({
		overlay_gallery: false
	});

/*----------------------------------*/
/*		  FitVids plugin
/*----------------------------------*/

	jQuery(".container").fitVids();

/*----------------------------------*/
/*		  Content Slider
/*----------------------------------*/

	jQuery('.content-slider').carousel({
		nextSlide : '.next',
		prevSlide : '.prev',
		addNav : false
	});

/*----------------------------------*/
/*		     Tooltip
/*----------------------------------*/

	jQuery('.socials ul li').mouseenter(function(){
		jQuery(this).find('.tooltip').stop().fadeIn();
	});

	jQuery('.socials ul li').mouseleave(function(){
		jQuery(this).find('.tooltip').stop().fadeOut();
	});

/*----------------------------------*/
/*		    Scroll To Top
/*----------------------------------*/

	jQuery('.scrollup').click(function(){
        jQuery("html, body").animate({ scrollTop: 0 }, 600);
        return false;
    });

/*----------------------------------*/
/*		      Twitter
/*----------------------------------*/

	jQuery('#tweets').tweetable({
		username: 'themesymphony',
		time: true,
		limit: 1,
		replies: true,
		position: 'append'
	});

/*----------------------------------*/
/*		    Flickr Feed
/*----------------------------------*/

	jQuery('#flickr_badges ul').jflickrfeed({
		limit: 6,
		qstrings: {
			id: '67664457@N06'
		},
		itemTemplate:
		'<li>' +
			'<a data-rel="prettyPhoto[flickr-feed]" href="{{image_b}}" title="{{title}}">' +
				'<img src="{{image_s}}" alt="{{title}}" />' +
			'</a>' +
		'</li>'
	}, function(data) {
		jQuery("a[data-rel^='prettyPhoto']").prettyPhoto({
			overlay_gallery: false
		});
	});

/*----------------------------------*/
/*		    First Word Wrap
/*----------------------------------*/

	jQuery('h6').each(function(index) {
		var firstWord = jQuery(this).text().split(' ')[0];	
		var replaceWord = "<span class='first-word'>" + firstWord + "</span>";	
		var newString = jQuery(this).html().replace(firstWord, replaceWord);
		jQuery(this).html(newString);
	});

/*----------------------------------*/
/*		   Responsive table
/*----------------------------------*/

	var switched = false;
	var updateTables = function() {
	if ((jQuery(window).width() < 959) && !switched ){
		switched = true;
		jQuery("table.responsive-table").each(function(i, element) {
			splitTable($(element));
		});
		return true;
	}
	else if (switched && (jQuery(window).width() > 959)) {
		switched = false;
		jQuery("table.responsive-table").each(function(i, element) {
			unsplitTable($(element));
		});
	}
	};

	jQuery(window).load(updateTables);
	jQuery(window).bind("resize", updateTables);


	function splitTable(original)
	{
		original.wrap("<div class='table-wrapper' />");
		
		var copy = original.clone();
		copy.find("td:not(:first-child), th:not(:first-child)").css("display", "none");
		copy.removeClass("responsive-table");
		
		original.closest(".table-wrapper").append(copy);
		copy.wrap("<div class='pinned' />");
		original.wrap("<div class='scrollable' />");
	}

	function unsplitTable(original) {
	original.closest(".table-wrapper").find(".pinned").remove();
	original.unwrap();
	original.unwrap();
	}

/*----------------------------------*/
/*		  Portfolio Filterable
/*----------------------------------*/

	// BEGIN isotope filtering
	$container = jQuery('.portfolio-filterable');
	$container.imagesLoaded( function(){
		$container.isotope({
			itemSelector : '.portfolio-item',
			layoutMode   : 'fitRows'
		});
	});

	// filter items when filter link is clicked
	jQuery('#portfolio-filters li').click(function(){
	jQuery('#portfolio-filters li').removeClass('current');
		jQuery(this).addClass('current');
			var selector = jQuery(this).find('a').attr('data-filter');
			$container.isotope({ filter: selector });
		return false;
	});

	jQuery(".item-hover .portfolio-item-description").css({ opacity: 0 });
	jQuery(".item-hover .portfolio-item-description").css({ top: -100 });

	jQuery('.item-hover').bind('mouseenter',function(){
		var $ele = jQuery(this);
			// portfolio icon
		$ele.find('.portfolio-item-description').stop(true).animate({ 'opacity': 1, 'top':'0px' }, 300).andSelf()
		
	}).bind('mouseleave',function(){
		var $ele = jQuery(this);
			// portfolio icon	
		$ele.find('.portfolio-item-description').stop(true).animate({ 'opacity': 0, 'top':'-100px' }, 200).andSelf()
	});

/* END CUSTOM JQUERY =================================================================== */

});