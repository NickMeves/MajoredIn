<?php

get_header(); ?>

<div id="content">
    <div class="container error404">
        <div class="row">
            <div id="main" class="span8 offset2">
            
                <h1 class="article-title">404. Are you lost?</h1>
                <p>Whoops, we have no idea where you were trying to go.  Whatever this page is, we don't know about it.</p>
                <p>Try hitting the back button or using the links on this page to navigate back to real pages.</p>
                <p>If a broken link sent you here please <a href="mailto:support@majoredin.com">email us</a> which link it was.  We'll try to fix it ASAP.</p>

            </div>
        </div>
    </div>
</div>

<?php
    global $kernel;
    echo $kernel->getContainer()->get('templating')->render('::javascripts.html.twig');
?>
<?php wp_footer(); ?>
</body>
</html>