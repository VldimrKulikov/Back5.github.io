<?php

session_start();
session_destroy();
setcookie('name_value', '', 100000);
setcookie('year_value', '', 100000);
setcookie('email_value', '', 100000);
setcookie('limbs_value', '', 100000);
setcookie('gender_value', '', 100000);
setcookie('ability_value', '', 100000);
setcookie('biography_value', '', 100000);
setcookie('checkbox_value', '', 100000);
header('Location: ./');
exit();