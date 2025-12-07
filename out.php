<?php
SESSION_START();
session_destroy();
header("location:index.php");//清除session退出登录

