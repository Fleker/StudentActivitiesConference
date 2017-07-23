<?php 
include 'firebase_include_js.php';
?>

<div class="content-wrapper clearfix">

	<div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>Photo Gallery</h1>
				<span>Photos from the 2017 IEEE Region 2 Student Activities Conference. </span>
			</div>
		</div>

		<div class="clear"></div>
		
		<ul class="sixteen columns gallery">
            <script>
                var storage = firebase.storage();
                var storageRef = storage.ref();
                var images = firebase.database().ref('/release/images').once('value').then(function(snapshot) {
                            console.log(snapshot.val());
                    for (var id in snapshot.val()) {
                        console.log(id);
                        console.log(snapshot.val()[id]);
                        console.log("Approved: " + snapshot.val()[id].approved);
                        if (snapshot.val()[id].approved) 
                        {                         
                        $('.gallery').append("<a class='fancybox-effects-b' href='' id='" + id + "' data-fancybox-group='gallery' title= '"+ snapshot.val()[id].caption + "'>" + "<img src='' />");
                         
                            
             $(".fancybox-effects-b").fancybox({
				openEffect  : 'none',
				closeEffect	: 'none',

				helpers : {
					title : {
						type : 'over'
					}
				},
            <?php if(isset($_GET['tv'])) { ?> autoPlay:true <?php } ?>
			
                });

			// Set custom style, close if clicked, change title type and overlay color
			$(".fancybox-effects-c").fancybox({
				wrapCSS    : 'fancybox-custom',
				closeClick : true,

				openEffect : 'none',

				helpers : {
					title : {
						type : 'inside'
					},
					overlay : {
						css : {
							'background' : 'rgba(238,238,238,0.85)'
						}
					}
				}
			});

			// Remove padding, set opening and closing animations, close if clicked and disable overlay
			$(".fancybox-effects-d").fancybox({
				padding: 0,

				openEffect : 'elastic',
				openSpeed  : 150,

				closeEffect : 'elastic',
				closeSpeed  : 150,

				closeClick : true,

				helpers : {
					overlay : null
				}
			});

			/*
			 *  Button helper. Disable animations, hide close button, change title type and content
			 */

			$('.fancybox-buttons').fancybox({
				openEffect  : 'none',
				closeEffect : 'none',

				prevEffect : 'none',
				nextEffect : 'none',

				closeBtn  : false,

				helpers : {
					title : {
						type : 'inside'
					},
					buttons	: {}
				},

				afterLoad : function() {
					this.title = 'Image ' + (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');
				}
			});


			/*
			 *  Thumbnail helper. Disable animations, hide close button, arrows and slide to next gallery item if clicked
			 */

			$('.fancybox-thumbs').fancybox({
				prevEffect : 'none',
				nextEffect : 'none',

				closeBtn  : false,
				arrows    : false,
				nextClick : true,

				helpers : {
					thumbs : {
						width  : 50,
						height : 50
					}
				}
			});

			/*
			 *  Media helper. Group items, disable animations, hide arrows, enable media and button helpers.
			*/
			$('.fancybox-media')
				.attr('rel', 'media-gallery')
				.fancybox({
					openEffect : 'none',
					closeEffect : 'none',
					prevEffect : 'none',
					nextEffect : 'none',

					arrows : false,
					helpers : {
						media : {},
						buttons : {}
					}
				});

			/*
			 *  Open manually
			 */

			$("#fancybox-manual-a").click(function() {
				$.fancybox.open('1_b.jpg');
			});

			$("#fancybox-manual-b").click(function() {
				$.fancybox.open({
					href : 'iframe.html',
					type : 'iframe',
					padding : 5
				});
			});

			$("#fancybox-manual-c").click(function() {
				$.fancybox.open([
					{
						href : '1_b.jpg',
						title : 'My title'
					}, {
						href : '2_b.jpg',
						title : '2nd title'
					}, {
						href : '3_b.jpg'
					}
				], {
					helpers : {
						thumbs : {
							width: 75,
							height: 50
						}
					}
				});
			});

                        
                        <?php
                        if(isset($_GET["tv"])) {?>
                       // $('.fancybox-effects-b').prettyPhoto({slideshow:5000, autoplay_slideshow:true,show_title:true,allow_resize:false,allow_expand:true});
                        autoplay=true;
                        <?php } ?>
                            
                        getUrlAndThen(id, snapshot.val()[id].path, function(id, url) {
                            console.log(id, url);
                            $('#' + id + ' img').attr('src', url);
                            $('#' + id + '').attr('href', url);
                        });
                        
                            getNameAndThen(id, snapshot.val()[id].uid , function(id, name){
                                console.log(name);
                                $('#' + id + ' small').html(name);
                                var nameStr = name ? ' - ' + name : "";
                                $('#' + id).attr('title', $('#' + id).attr('title') + nameStr);
                            });
                            
                        }
                       
                    }
                });
                
                
                
                function getUrlAndThen(id, relativePathFromTheDatabase, toRunAfter) {
                    var photo = firebase.storage().ref().child(relativePathFromTheDatabase);
                    photo.getDownloadURL().then(function(url) {toRunAfter(id, url); })
                }
                
                function getNameAndThen(id, uid, callback){
                    console.log('/release/attendees'+ uid);
                    var attendee = firebase.database().ref('/release/attendees/' + uid).once('value').then(function(snapshot){
                        console.log(snapshot.val());
                        callback(id, snapshot.val().name);
                    });
                }
                
            
                
            </script>

            <style>
                .gallery {
                    columns: 4;
                }
                
                
                .gallery img{
                    object-position: center;
                    object-fit: cover;
                    height: 220px;
                    width: 220px;
                    display: block;
                }
                
                .fancybox-wrap {
                    position: fixed !important;
                    top: 10px !important;
                }

/*                .fancybox-image {
                    height: 80vh;
                    width: inherit;
                }

                .fancybox-inner {
                    width: inherit !important;
                    height: inherit !important;
                }*/

                @media (max-width: 800px) {
                    .gallery {
                        columns: 2;
                    }
                    .gallery img {
                        margin-bottom: 4px;
                        text-align: center;
                    }
                }
                
                @media (max-width: 400px) {
                    .gallery {
                        columns: 1;
                    }
                    .gallery img {
                        margin-bottom: 4px;
                        text-align: center;
                        width: calc(100% - 40px);
                    }
                }
                
                .gallery div{
                    display: inline-block;
                    padding: 8px;
                    
                }
                
                .gallery small{
                    font-size: 11pt;
                    color: dimgrey;
                    padding-bottom: 5px;
                    font-family: "Open Sans", Arial, sans-serif;
                    display: list-item;
                }
            </style>
		</ul><!-- END .gallery -->

	</div><!-- END .container -->

</div><!-- END .homepage-content-wrapper -->
<?php
if(isset($_GET['tv'])) {
echo "
<style>
    div.pp_pic_holder {
        top: -37px;
        left: -13px;
        display: block;
        width: 100%;
        height: 100%;   
    }
    #pp_full_res {
        background-color: black;
        width:100%;
        z-index: 99;
        position: fixed;
        left: 0px;
        top: 0px;
        text-align: center;
        min-height: 100%;
        max-width: 100%;
        padding-top: 3%;
            }
    #fullResImage {
        max-height: 100%;
        max-width: 100%;   
    }
    .pp_description {
        display: block;
        z-index: 100;
        position: fixed;
        top: calc(100% - 67px);
        background-color: rgba(0,0,0,.5);
        width: 100%;
        left: 0px;
        font-size: 34pt;
        text-align: center;
        color: white;
        height: 67px;
        margin-top: 5px;
        vertical-align: bottom;   
    }
    .pp_expand {
        display:none;
    }
    div.pp_default .pp_description {
        font-size: 31px;
        color: white;
        font-weight: 700;
        line-height: 14px;
        margin: 5px 50px 5px 0;
    }
</style>";
}
?>
