<?php  
  if(in_array($_SERVER['HTTP_HOST'],["b2b.vidyaplanet.in","partner.vidyaplanet.in"])){
    $app_title = "Admission Portal";
    $organization_name = "Vidya Planet";
    $default_center_code_suffix = "VP_";
    $logged_in_users = "";
    $not_able_to_logged_in_users = " AND Code NOT LIKE 'SAU/%'";
    $dark_logo = "/assets/img/vp/logo.png";
    $dark_logo_ratina = "/assets/img/vp/logo_2x.png";
    $light_logo = "/assets/img/vp/logo_white.png";
    $light_logo_retina = "/assets/img/vp/logo_white_2x.png";
    $login_cover = "/assets/img/vp/cover.jpg";
  }elseif($_SERVER['HTTP_HOST']=="lmslogin.sikkimalpineuniversity.edu.in"){
    $app_title = "Sikkim Alpine University";
    $organization_name = "Sikkim Alpine University";
    $logged_in_users = " AND Code LIKE 'SAU/%'";
    $not_able_to_logged_in_users = "";
    $default_center_code_suffix = "VP_";
    $dark_logo = "/assets/img/sikkim/logo.png";
    $dark_logo_ratina = "/assets/img/sikkim/logo.png";
    $light_logo = "/assets/img/sikkim/logo_white.png";
    $light_logo_retina = "/assets/img/sikkim/logo_white.png";
    $login_cover = "/assets/img/sikkim/cover.jpg";
  }else{
    $app_title = "Admission Portal";
    $organization_name = "";
    $default_center_code_suffix = "";
    $logged_in_users = "";
    $not_able_to_logged_in_users = "";
    $dark_logo = "";
    $dark_logo_ratina = "";
    $light_logo = "";
    $light_logo_retina = "";
    $login_cover = "";
  }
