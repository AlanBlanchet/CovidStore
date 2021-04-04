<form method="<?php Conf::echoFormMethod() ?>">
    <input type="text" name="controller" value="user" hidden />
    <input type="text" name="action" value="sendMail" hidden />
    <input type="text" name="target" value="<?php echo $user->get('userLogin') ?>" hidden />
    <button type="submit">Envoyer un mail</button>
</form>