<div class="userForm">
    <form method="<?php Conf::echoFormMethod();?>">
        <input type="hidden" name="action"
            value="<?php echo $formAction ?>" />
        <h1>
            <?php
                echo htmlspecialchars($headMessage);
            ?>
        </h1>
        <div>
            <label for="userLogin">Login</label>
            <input type="text" name="userLogin" id="userLogin" placeholder="Ex : pascualito"
                value="<?php echo htmlspecialchars($user->get('userLogin')) ?>"
                <?php echo $requirement ?>
            />
        </div>
        <div>
            <label for="userMail">Mail</label>
            <input type="email" placeholder="Ex : xxx@xxx.xx" name="userMail" id="userMail"
                value="<?php echo htmlspecialchars($user->get('userMail')) ?>"
                required />
        </div>
        <div>
            <label for="userLastName">Nom</label>
            <input type="text" placeholder="Ex : Montard" name="userLastName" id="userLastName"
                value="<?php echo htmlspecialchars($user->get('userLastName')) ?>"
                required />
        </div>
        <div>
            <label for="userFirstName">Prénom</label>
            <input type="text" placeholder="Ex : Kévin" name="userFirstName" id="userFirstName"
                value="<?php echo htmlspecialchars($user->get('userFirstName')) ?>"
                required />
        </div>
        <div>
            <label for="userPassword">Mot de passe</label>
            <input type="password" placeholder="Ex : 10gakH10g1" name="userPassword" id="userPassword"
            <?php if($requirement=="required") echo $requirement ?> />
        </div>
        <div>
            <label for="confirmPassword">Mot de passe</label>
            <input type="password" name="confirmPassword" id="confirmPassword"
            <?php if($requirement=="required") echo $requirement ?> />
        </div>
        
        <div class="userValidate">
            <button type="submit"><?php echo htmlspecialchars($message) ?></button>
        </div>
    </form>
    <?php 
        if($formAction == 'updated') {
            require File::buildPath('view','user','delete.php');
        }
    ?>
</div>