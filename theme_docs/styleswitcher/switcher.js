jQuery(document).ready(function(){
	jQuery(".sswp-lightdefault").click(function(){jQuery("#css_color").attr("href", "css/styles/lightdefault.css");return false;});
	jQuery(".sswp-darkdefault").click(function(){jQuery("#css_color").attr("href", "css/styles/darkdefault.css");return false;});
	jQuery(".sswp-lightblue1").click(function(){jQuery("#css_color").attr("href", "css/styles/lightblue1.css");return false;});
	jQuery(".sswp-darkblue1").click(function(){jQuery("#css_color").attr("href", "css/styles/darkblue1.css");return false;});
	jQuery(".sswp-lightblue2").click(function(){jQuery("#css_color").attr("href", "css/styles/lightblue2.css");return false;});
	jQuery(".sswp-darkblue2").click(function(){jQuery("#css_color").attr("href", "css/styles/darkblue2.css");return false;});
	jQuery(".sswp-lightblue3").click(function(){jQuery("#css_color").attr("href", "css/styles/lightblue3.css");return false;});
	jQuery(".sswp-darkblue3").click(function(){jQuery("#css_color").attr("href", "css/styles/darkblue3.css");return false;});
	jQuery(".sswp-lightbrown").click(function(){jQuery("#css_color").attr("href", "css/styles/lightbrown.css");return false;});
	jQuery(".sswp-darkbrown").click(function(){jQuery("#css_color").attr("href", "css/styles/darkbrown.css");return false;});
	jQuery(".sswp-lightgreen1").click(function(){jQuery("#css_color").attr("href", "css/styles/lightgreen1.css");return false;});
	jQuery(".sswp-darkgreen1").click(function(){jQuery("#css_color").attr("href", "css/styles/darkgreen1.css");return false;});
	jQuery(".sswp-lightgreen2").click(function(){jQuery("#css_color").attr("href", "css/styles/lightgreen2.css");return false;});
	jQuery(".sswp-darkgreen2").click(function(){jQuery("#css_color").attr("href", "css/styles/darkgreen2.css");return false;});
	jQuery(".sswp-lightorange1").click(function(){jQuery("#css_color").attr("href", "css/styles/lightorange1.css");return false;});
	jQuery(".sswp-darkorange1").click(function(){jQuery("#css_color").attr("href", "css/styles/darkorange1.css");return false;});
	jQuery(".sswp-lightorange2").click(function(){jQuery("#css_color").attr("href", "css/styles/lightorange2.css");return false;});
	jQuery(".sswp-darkorange2").click(function(){jQuery("#css_color").attr("href", "css/styles/darkorange2.css");return false;});
	jQuery(".sswp-lightpink").click(function(){jQuery("#css_color").attr("href", "css/styles/lightpink.css");return false;});
	jQuery(".sswp-darkpink").click(function(){jQuery("#css_color").attr("href", "css/styles/darkpink.css");return false;});
	jQuery(".sswp-lightpurple").click(function(){jQuery("#css_color").attr("href", "css/styles/lightpurple.css");return false;});
	jQuery(".sswp-darkpurple").click(function(){jQuery("#css_color").attr("href", "css/styles/darkpurple.css");return false;});
	jQuery(".sswp-lightred1").click(function(){jQuery("#css_color").attr("href", "css/styles/lightred1.css");return false;});
	jQuery(".sswp-darkred1").click(function(){jQuery("#css_color").attr("href", "css/styles/darkred1.css");return false;});
	jQuery(".sswp-lightred2").click(function(){jQuery("#css_color").attr("href", "css/styles/lightred2.css");return false;});
	jQuery(".sswp-darkred2").click(function(){jQuery("#css_color").attr("href", "css/styles/darkred2.css");return false;});
	jQuery(".sswp-lightsteel").click(function(){jQuery("#css_color").attr("href", "css/styles/lightsteel.css");return false;});
	jQuery(".sswp-darksteel").click(function(){jQuery("#css_color").attr("href", "css/styles/darksteel.css");return false;});
	jQuery(".sswp-lightyellow").click(function(){jQuery("#css_color").attr("href", "css/styles/lightyellow.css");return false;});
	jQuery(".sswp-darkyellow").click(function(){jQuery("#css_color").attr("href", "css/styles/darkyellow.css");return false;});
});

// Sliding Panel
jQuery(window).load(function() {
	jQuery("a#styleswitcher-panel-open").click(function(){
		var ml = jQuery("#styleswitcher-panel").css("margin-left");
		if (ml == "-200px"){
			jQuery("#styleswitcher-panel").animate({marginLeft: "0px"}, 200);
		} else {
			jQuery("#styleswitcher-panel").animate({marginLeft: "-200px"}, 200);
		}
		return false;
	})
});