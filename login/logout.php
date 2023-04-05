<?php
session_start();
session_regenerate_id(true);

unset($_SESSION['user']);
unset($_SESSION['msg']);
unset($_SESSION['post']);

header('Location: ./');
exit;
