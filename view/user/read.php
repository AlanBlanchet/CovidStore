<div class="userForm">
    <div>
        <h1>
            <?php
                $firstName = $user->get('userFirstName');
                $lastName = $user->get('userLastName');
                echo htmlspecialchars("$firstName $lastName");
            ?>
        </h1>
        <div>
            <label for="userLogin">Login</label>
            <input type="text" name="userLogin" id="userLogin" placeholder="Ex : pascualito"
                value="<?php echo htmlspecialchars($user->get('userLogin')) ?>"
                readonly
            />
        </div>
        <div>
            <label for="userLastName">Nom</label>
            <input type="text" placeholder="Ex : Montard" name="userLastName" id="userLastName"
                value="<?php echo htmlspecialchars($user->get('userLastName')) ?>"
                readonly />
        </div>
        <div>
            <label for="userFirstName">Prénom</label>
            <input type="text" placeholder="Ex : Kévin" name="userFirstName" id="userFirstName"
                value="<?php echo htmlspecialchars($user->get('userFirstName')) ?>"
                readonly />
        </div>
        <div>
            <label for="userMail">Mail</label>
            <input type="email" name="userMail" id="userMail"
                value="<?php echo htmlspecialchars($user->get('userMail')) ?>"
                readonly />
        </div>
        <?php
            if(Session::isAdmin()) {
                require File::buildPath('view', 'user', 'mailRead.php');
            }
        ?>
        <?php 
            if($user->get('userRole')!="admin"&&(Session::isAdmin())){
                require_once File::buildPath('view','user','roleForm.php');
                require File::buildPath('view','user','delete.php');
            }
        ?>
    </div>
</div>