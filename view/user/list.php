<?php

    foreach ($users as $user) {
        echo 'Utilisateur : ' .  htmlspecialchars($user->get('userLogin'));
    }
