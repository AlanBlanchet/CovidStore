<div>
    <a href="index.php">Accueil</a>
</div>
<div>
    <?php
    if (!Session::isConnected()) {
        echo '<a href="?controller=user&action=connect">Se connecter</a>';
        echo '<a href="?controller=user&action=create">S\'enregistrer</a>';
    } else {
        // On regarde si l'utilisateur est un admin
        if (Session::isAdmin()) {
            echo "<a href='?controller=user&action=showAdminPanel'>Panneau Administrateur</a>";
            echo "<a href='?controller=user&action=showAdminPanel'>Ajouter un produit</a>";
        }
        $usr = UserModel::select(Session::getUser());
        $login = $usr->get('userLogin');
        $firstName = $usr->get('userFirstName');
        $lastName = $usr->get('userLastName');
        echo "<a href='?controller=user&action=update&userLogin=" . rawurlencode($login) . "'>" . htmlspecialchars("$firstName $lastName") . "</a>";
        echo "<a href='?controller=user&action=deconnect'>Se déconnecter</a>";
        // On regarde si l'utilisateur est un client
        if (Session::isClient()) {
            $basketAmount = 0;
            $basketCounter = '';
            if ($basketAmount != 0) {
                $basketCounter = "<div class='headerBasketCounter'>" . htmlspecialchars($basketAmount) . "</div>";
            }
            $basketIcon = "<i class='fas fa-shopping-basket'></i>";
            echo "<a class='headerBasket' href='?controller=product&action=basket'>" . htmlspecialchars("${basketIcon}${basketCounter}") . "</a>";
        }
    }
    ?>
</div>