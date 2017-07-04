<!-- Start Timer -->
<div id="mtSound"><audio id="mtaudio" src="" preload="auto"  ></audio></div>
<div id="mtBody">
    <div id="mtBodyInner">
        <div id="mtTimerPanel">
            <a class="mtSettingArrow icon-arrow-up" id="mtSettingTimeMinutesArrowUp" href="#"></a>
            <a class="mtSettingArrow icon-arrow-down" id="mtSettingTimeMinutesArrowDown" href="#"></a>
            <a class="mtSettingArrow icon-arrow-up" id="mtSettingTimeSecondsArrowUp" href="#"></a>
            <a class="mtSettingArrow icon-arrow-down" id="mtSettingTimeSecondsArrowDown" href="#"></a>
            <span id="mtOverallTime"></span>
            <span id="mtIntervalTime"></span>
            <ul id="mtSettings">
                <li>Prepare<br /> <span id="mtPrepareTimeSetting"></span></li>
                <li>Meditation<br /> <span id="mtMeditationTimeSetting"></span></li>
                <li class="last">Interval<br /> <span id="mtIntervalTimeSetting"></span></li>
            </ul>
            <a class="mtControlButton" id="mtStartButton" href="#"></a>
            <a class="mtControlButton" id="mtStopButton" href="#"></a>
            <a class="mtControlButton" id="mtPauseButton" href="#"></a>
            
            
        </div>
    </div>
</div>
<!-- End Timer -->

  <script type="text/javascript">
  
    /*jQuery(document).ready(function(){
        
        var audio = document.getElementById('mtaudio');
        
        var $next_song = 0;
    
        jQuery("#mtOverallTime").click(function(e){
            
            // Play Next
             $next_song = ( ( parseInt($next_song)+1 ) > 3 )?1:(parseInt($next_song)+1);
            alert($next_song);
            
            audio.src = 'http://dmpc.com/Scripts/HTML5-Audio-Android/test1/sound'+$next_song+'.mp3';
            
            audio.play();
            
        });
        
        
    });*/
 
    
    
    
  </script>