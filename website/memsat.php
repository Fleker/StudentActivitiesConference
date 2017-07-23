
<div class="content-wrapper clearfix">

	<div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>MemSat the Game</h1>
				<h2>Can you beat the high score?</h2>
			</div>
		</div>

		<div class="clear"></div>
        
        <p>
            Move around and shoot enemy. Each enemy will have different voltages and frequencies. Make sure you use the right weapon. The player with the highest score wins a special prize. On mobile, you can use your finger to slide around.
            <ul>
                <li>Left/Right - Rotate your ship</li>
                <li>Up - Move your ship forward in the direction of rotation</li>
                <li>Space - Shoots a resistor, a simple component without any voltage or frequency dependence</li>
                <li>C - Shoots a capacitor, a component that does not affect low frequency signals</li>
                <li>I/L - Shoots an inductor, a component that does not affect high frequency signals</li>
                <li>M - Shoots a memristor, a component that has high or low resistance. Its value changes whenever it is toggled with high voltage.</li>
            </ul>
            <button onclick='onscreen()'>Toggle on-screen controls</button>
            <button onclick='onmusic()' id='music'>Disable Audio</button>
        </p>
        
        <style>
            #game, #cr-stage {
                width: 960px;
                height: 500px;
            }
            
            .immune {
                opacity: 0.6;
            }
        </style>

        <div id="game"></div>
        <div id='controls' style='margin-top: 24px;'>
            <script> touchAvailable = false; </script>
           <!-- <button ontouchstart="touchAvailable=true; pressKey(37);" onmousedown="if(!touchAvailable) pressKey(37);" ontouchend="touchAvailable; releaseKey(37)" onmouseup="if(!touchAvailable) releaseKey(37)">CCW</button>
            <button ontouchstart="touchAvailable=true; pressKey(39);" onmousedown="if(!touchAvailable) pressKey(39);" ontouchend="touchAvailable; releaseKey(39)" onmouseup="if(!touchAvailable) releaseKey(39)">CW</button>
            <button ontouchstart="touchAvailable=true; pressKey(38);" onmousedown="if(!touchAvailable) pressKey(38);" ontouchend="touchAvailable; releaseKey(38)" onmouseup="if(!touchAvailable) releaseKey(38)">FW</button>
            --><button ontouchstart="touchAvailable=true; pressKey(32);" onmousedown="if(!touchAvailable) pressKey(32);" ontouchend="touchAvailable; releaseKey(32)" onmouseup="if(!touchAvailable) releaseKey(32)">Resistor</button>
            <button ontouchstart="touchAvailable=true; pressKey(73);" onmousedown="if(!touchAvailable) pressKey(73);" ontouchend="touchAvailable; releaseKey(73)" onmouseup="if(!touchAvailable) releaseKey(73)">Inductor</button>
            <button ontouchstart="touchAvailable=true; pressKey(67);" onmousedown="if(!touchAvailable) pressKey(67);" ontouchend="touchAvailable; releaseKey(67)" onmouseup="if(!touchAvailable) releaseKey(67)">Capacitor</button>
            <button ontouchstart="touchAvailable=true; pressKey(77);" onmousedown="if(!touchAvailable) pressKey(77);" ontouchend="touchAvailable; releaseKey(77)" onmouseup="if(!touchAvailable) releaseKey(77)">Memristor</button>
        </div>
    <em>Built by Nick Felker &amp; Nick Ambrose</em>

	</div><!-- END .container -->

</div><!-- END .homepage-content-wrapper -->
