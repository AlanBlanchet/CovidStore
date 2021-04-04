<?php
    if(isset($pageMessage)) echo "<div class='stateInformation'>".rawurlencode($pageMessage)."</div>";
?>
<div class="userAdministratorPanel">
    <div>
        <h3>Administration des utilisateurs</h3>
        <hr style="width:100%">
        <div>
            <?php
                foreach($users as $key => $value) {
                    $userLogin = htmlspecialchars($value->get('userLogin'));
                    $urlEncodedLogin = rawurlencode($userLogin);
                    echo "<a href='?controller=user&action=read&userLogin=$urlEncodedLogin'>$userLogin</a>";
                }
            ?>
        </div>
        <a href="?controller=user&action=readAll">Gérer les utilisateurs</a>
    </div>
    <div>
        <h3>Ventes réalisées</h3>
        <hr style="width:100%">
        <div>
        </div>
    </div>
</div>