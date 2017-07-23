<style>
    .rules-iframe {
        width: 80%;
        margin-left: auto;
        margin-right: auto;
        display: block;
        height: 60vh;
        transition: 0.5s all;
    }
    
    @media (max-width:1024px) {
        .rules-iframe {
            width: 80%;
            margin-left: auto;
            margin-right: auto;
            display: block;
            height: 80vh;
        }
    }
    
    @media (max-width:800px) {
        .tabs-content img.alignleft {
            float: right;
            padding: 4px;
            width: 80px;
        }   
    }
</style>

<div class="content-wrapper clearfix">

	<div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>SAC 2017 Competitions</h1>
			</div>
		</div>

		<div class="clear"></div>
				
		<div class="sixteen columns">		
			<div class="divider"><span class="divider-line"></span><span class="divider-color"></span></div>
            
            <?php 
                // Use data from PHP to develop competition tabs
                foreach (COMPETITIONS as $i => $key) { ?>
                    <h3>
                        <?php if (isset($key['Icon'])) { ?>
                        <img src='<?php echo $key['Icon']; ?>' style='width:22pt !important' />
                        <?php } ?>
                        <a href="#"><?php echo $i; ?></a>
                    </h3>
                    <div class="tabs">
                        <ul class="tabs-nav">
                            <?php if (isset($key['About'])) { ?>
                                <li><a href="#tabs-1"><i class="icon-book"></i> About</a></li>
                            <?php }
                                  if (isset($key['Rules'])) { ?>
                                <li><a href="#tabs-2"><i class="icon-exclamation-sign"></i> Rules</a></li>
                            <?php } 
                                  if (isset($key['Rubric'])) { ?>
                                <li><a href="#tabs-3"><i class="icon-check"></i>Rubric</a></li>
                            <?php } ?>
                            
                        </ul>
                        <ul class="tabs-content">
                            <?php if (isset($key['About'])) { ?>
                                <li id="tabs-1">
                                    <?php if (isset($key['Photo'])) { ?>
                                    <img src='<?php echo $key['Photo']; ?>' width='150px' class="alignleft" />
                                    <?php } ?>

                                <?php echo $key['About']; ?>
                                <div class="clear"></div>
                                </li>
                            <?php } ?>
                            <li id="tabs-2">
                                <?php if (isset($_GET['app'])) { ?>
                                <a href='<?php echo $key['Rules']; ?>'>View Rules</a>
                                <?php } else { ?>
                                <iframe class='rules-iframe' src="<?php echo $key['Rules']; ?>"></iframe>
                                <?php } ?>
                            </li>
                            <?php if (isset($key['Rubric'])) { ?>
                            <li id="tabs-3">
                                <?php if (isset($_GET['app'])) { ?>
                                <a href='<?php echo $key['Rubric']; ?>'>View Rubric</a>
                                <?php } else { ?>
                                <iframe class='rules-iframe' src="<?php echo $key['Rubric']; ?>"></iframe>
                                <?php } ?>
                            </li>
                            <?php } ?>
                            
                        </ul><!-- END .tabs-content -->
                    </div>
                    <div class="divider"><span class="divider-line"></span><span class="divider-color"></span></div>
                <?php } ?>			
            
			<p>Any questions not answered on this page? Please contact <a href="mailto:ieee@rowan.edu?subject=SAC 2017 Competitions Question">ieee@rowan.edu</a>!</p>
        
        </div>	

		</div><!-- END .page-content -->

	</div><!-- END .container -->

</div><!-- END .content-wrapper -->