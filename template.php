<div class="p-attendance-check" id="p-attendance-check-responsive" data-history = <?php echo ( isset($atts['history']) ? $atts['history'] : null ); ?>>
  <div class="p-attendance-check__header">
    <span class="p-attendance-check__header__pointer p-attendance-check__header__pointer--minusmonth">
      <i class="fas fa-2x fa-chevron-circle-left"></i>
    </span>
    <span class="p-attendance-check__header__monthname"></span>
    <span class="p-attendance-check__header__pointer p-attendance-check__header__pointer--addmonth">
      <i class="fas fa-2x fa-chevron-circle-right"></i>
    </span>
  </div>

  <ul class="p-attendance-check__weekday">
    <li>SUN</li><li>MON</li><li>TUE</li><li>WED</li><li>THU</li><li>FRI</li><li>SAT</li>
  </ul>

  <ul class="p-attendance-check__days">
  </ul>

  <div class="p-attendance-check__clone" style="display: none;">
    <li class="p-attendance-check__days__date p-attendance-check__days__date--no_seal">
      <span class="p-attendance-check__days__date__number">1</span>  
      <img src="<?php echo PAC_PLUGIN_URL; ?>assets/images/no_seal.png" alt="">
    </li>
    <li class="p-attendance-check__days__date p-attendance-check__days__date--yes_seal">
      <span class="p-attendance-check__days__date__number">2</span>  
      <img src="<?php echo PAC_PLUGIN_URL; ?>assets/images/seal.png" alt="">
    </li>
  </div>

  <div class="p-attendance-check__stamp_btn">
    <div class="inner">
      출석
    </div>
    <div class="sk-fading-circle">
      <div class="sk-circle1 sk-circle"></div>
      <div class="sk-circle2 sk-circle"></div>
      <div class="sk-circle3 sk-circle"></div>
      <div class="sk-circle4 sk-circle"></div>
      <div class="sk-circle5 sk-circle"></div>
      <div class="sk-circle6 sk-circle"></div>
      <div class="sk-circle7 sk-circle"></div>
      <div class="sk-circle8 sk-circle"></div>
      <div class="sk-circle9 sk-circle"></div>
      <div class="sk-circle10 sk-circle"></div>
      <div class="sk-circle11 sk-circle"></div>
      <div class="sk-circle12 sk-circle"></div>
    </div>
  </div>

</div>
