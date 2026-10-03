<?php


session_unset();
session_destroy();

header('Location: LandingPageMua.php');
exit;
