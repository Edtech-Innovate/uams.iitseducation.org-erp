<?php
  $hostname = "localhost";
  $username = "iits_newiits";
  $password = "#yrzImIJ3y5Jdaqd";
  $database = "iits_newiits";
  $conn = mysqli_connect("$hostname", "$username", "$password", "$database");
  include('config.php');
  define('ERP_URL', "https://uams.iitseducation.org/");