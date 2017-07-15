<?php
    //mail_test3.php?email=handnf@gmail.com&name=Fleker&subject=123&msg=Hi
    require 'mail_config.php';

    echo sendEmail($_POST['email'], $_POST['name'], $_POST['subject'], $_POST['msg']);
?>
