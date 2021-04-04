<!DOCTYPE html>
<html>

<head>
	<?php
        require_once File::buildPath('view', 'head.php');
    ?>
</head>

<body>
	<header>
		<?php
			require_once File::buildPath('view', 'header.php');
        ?>
	</header>
	<main>
		<?php
            require_once File::buildPath('view', static::$object, $view);
        ?>
	</main>
	<footer>
		<?php
            require_once File::buildPath('view', 'footer.php');
        ?>
	</footer>
</body>

</html>