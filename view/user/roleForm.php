<form method="<?php Conf::echoFormMethod()?>">
    <input type="hidden" name="action"
    value="updatedRole" />
    <input type="hidden" name="userLogin"
    value="<?php echo $user->get('userLogin'); ?>" />
    <input type="hidden" name="userRole"
    value="admin" />
    <div class="userValidate">
        <button type="submit">Passer l'utilisateur admin</button>
    </div>
</form>