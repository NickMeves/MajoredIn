<?php global $kernel; ?>
<?php get_header(); ?>

<div class="superflag">
    <div class="container">
        <div class="row">
            <div class="span8 offset4">
                <?php dynamic_sidebar( 'Homepage Jumbotron' ); ?>
            </div>
        </div>
    </div>
</div>
<div class="social-wrapper-front">
    <div class="container">
        <div class="row">
        <?php
    	    echo $kernel->getContainer()->get('templating')->render('MajoredInMainBundle:Social:socialshare.html.twig', array('shareUrl' => 'http://www.majoredin.com'));
    	?>
        </div>
    </div>
</div>
<div id="content">
    <div class="container">
        <div class="row marketing">
        <?php
            if (is_active_sidebar('Homepage Marketing')) {
                dynamic_sidebar('Homepage Marketing'); 
            }
        ?>
        </div>
        <div class="row">
        <?php
            if (is_active_sidebar('Homepage Bottom')) {
                dynamic_sidebar('Homepage Bottom');
            }
        ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
