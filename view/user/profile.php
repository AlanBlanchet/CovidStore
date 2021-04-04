<?php
    if(isset($pageMessage)) echo "<div class='stateInformation'>". htmlspecialchars($pageMessage)."</div>";
    if(Session::isUser($user->get('userLogin'))) {
        require File::buildPath('view','user','update.php');
    } else {
        require File::buildPath('view','user','read.php');
    }
?>