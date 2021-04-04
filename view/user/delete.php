<form method="<?php Conf::echoFormMethod();?>">
    <input type="hidden" name="action" value="delete" />
    <input type="hidden" name="userLogin"
        value="<?php echo $user->get('userLogin'); ?>" />
    <div class="userSubmitDelete">
        <button type="submit">Supprimer le compte</button>
    </div>
</form>