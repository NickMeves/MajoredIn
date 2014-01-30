<?php
    global $kernel; 
    $kernel->getContainer()->get('mi_main.layout_controller')->footerAction()->sendContent();
    echo $kernel->getContainer()->get('templating')->render('::javascripts.html.twig');
?>
<?php wp_footer(); ?>
</body>
</html>