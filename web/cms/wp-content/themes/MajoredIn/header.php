<?php global $kernel; ?>
<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php wp_title( '-', true, 'right' ); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
	
	<?php
	    echo $kernel->getContainer()->get('templating')->render('::favicon.html.twig');
	    echo $kernel->getContainer()->get('templating')->render('::stylesheets.html.twig');
	    echo $kernel->getContainer()->get('templating')->render('::analytics.html.twig');
	    echo $kernel->getContainer()->get('templating')->render('::globals.html.twig');
	?>

    <?php wp_head(); ?>
</head>
<body>
<div id="fb-root"></div>
<?php $kernel->getContainer()->get('mi_main.layout_controller')->headerAction()->sendContent(); ?>
