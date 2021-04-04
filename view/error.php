<!DOCTYPE html>
<html>

<head>
    <link href="css/main.css" rel="stylesheet" />
    <link href="css/error.css" rel="stylesheet" />
</head>

<body>
    <main>
        <div>
            <div>
                <h3>Il semblerait qu'une erreur se soit produite.</h3>
                <div>
                    <p>Message d'erreur :</p>
                    <cite>
                        <?php echo htmlspecialchars($message); ?>
                    </cite>
                </div>
            </div>
            <?php
                if (!isset($returnLink) || !isset($returnMessage)) {
                    $returnLink = "./";
                    $returnMessage = "Retour à l'accueil";
                }
                echo "<a class='return' href='". rawurlencode($returnLink)."'>".htmlspecialchars($returnMessage)."</a>";
            ?>
        </div>
    </main>
</body>

</html>