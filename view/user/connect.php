<div class="userForm">
    <form method="<?php Conf::echoFormMethod();?>">
        <input type="hidden" name="action"
            value="<?php echo  $formAction ?>" />
        <h1>
            Se connecter
        </h1>
        <div>
            <label for="userLogin">Login</label>
            <input type="text" name="userLogin" id="userLogin" placeholder="Ex : pascualito" required />
        </div>
        <div>
            <label for="userPassword">Mot de passe</label>
            <input type="password" placeholder="Ex : 10gakH10g1" name="userPassword" id="userPassword" required />
        </div>
        <div class="userValidate">
            <button type="submit"><?php echo  htmlspecialchars($message) ?></button>
        </div>
    </form>
</div>