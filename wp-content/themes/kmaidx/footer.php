<?php
use Includes\Modules\Social\SocialSettingsPage;

?>
<script async src="https://i.simpli.fi/dpx.js?cid=151523&action=100&segment=RetargetingBeachyBeach&m=1&sifi_tuid=72047"></script>
    <div id="sticky-footer" class="unstuck">
        <div id="bot">
            <div class="container xwide">

                <div class="mortgage-mobile-button hidden-md-up text-center">
                    <button onclick="toggler('location-grid');" class="btn btn-lg btn-primary">Office Locations</button>
                </div>
                <div class="hidden-sm-down text-center">
                    <div class="switch-title"><span>Choose Your Beachy Beach</span></div>
                </div>

                <div id="location-grid" class="row justify-content-center align-items-center" style="display: none;">
	                <?php get_template_part( 'template-parts/content', 'location' ); ?>
                </div>
                <div class="switch-title text-center mb-2 mt-3">
                    <a target="_blank" href="http://www.youtube.com/user/TheBeachShow" >
                        <img 
                            class="img-fluid lazy" 
                            src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                            data-src="<?php echo get_template_directory_uri() ?>/img/beachshow-horizontal.png" 
                            alt="The Beach Show"
                        >
                    </a>
                </div>
                <div id="botnav" class="row no-gutters justify-content-center ">
                    <nav class="navbar">
			            <?php wp_nav_menu(
				            array(
					            'theme_location'  => 'menu-2',
					            'container_class' => '',
					            'container_id'    => 'navbar-footer',
					            'menu_class'      => 'nav justify-content-center',
					            'fallback_cb'     => '',
					            'menu_id'         => 'menu-2',
					            'walker'          => new WP_Bootstrap_Navwalker(),
				            )
			            ); ?>
                    </nav>
                </div>
            </div>
        </div>
        <div id="bot-bot">
            <div class="container xwide">
                <div class="row no-gutters justify-content-center justify-content-lg-start align-items-middle">
                    <div class="col-md-3 my-auto text-center text-md-left">
                        <div class="social">
                            <?php
                            $socialLinks = new SocialSettingsPage();
                            $socialIcons = $socialLinks->getSocialLinks('svg', 'circle');
                            if (is_array($socialIcons)) {
                                foreach ($socialIcons as $socialId => $socialLink) {
                                    echo '<a class="' . $socialId . '" href="' . $socialLink[0] . '" target="_blank" >' . $socialLink[1] . '</a>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col-md-6 my-auto mx-auto justify-content-center text-center">
                        <p class="copyright">&copy;<?php echo date('Y'); ?> Beachy Beach Real Estate. All Rights Reserved. <a style="text-decoration: underline;" href="/privacy-policy/" >Privacy Policy</a> <span class="line">
                            <img 
                                class="lazy"
                                src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                                data-src="<?php echo get_template_directory_uri() ?>/img/realtors-association-logo-small.png" 
                                alt="Realtors Associattion" 
                                id="realtors-association"
                            > 
                            <img 
                                class="lazy"
                                src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                                data-src="<?php echo get_template_directory_uri() ?>/img/equal-housing-logo-small.png" 
                                alt="Equal Housing Opportunity" 
                                id="equal-housing"
                            >
                        </span></p>
                    </div>
                    <div class="col-md-3 my-auto justify-content-center justify-content-sm-end text-center text-sm-right">
                        <p class="siteby"><svg version="1.1" id="kma" height="16" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 12.5 8.7" style="enable-background:new 0 0 12.5 8.7;" xml:space="preserve">
                                <path fill="#b4be35" class="kma" d="M6.4,0.1c0,0,0.1,0.3,0.2,0.9c1,3,3,5.6,5.7,7.2l-0.1,0.5c0,0-0.4-0.2-1-0.4C7.7,7,3.7,7,0.2,8.5L0.1,8.1
                            c2.8-1.5,4.8-4.2,5.7-7.2C6,0.4,6.1,0.1,6.1,0.1H6.4L6.4,0.1z"></path>
                        </svg> <a href="https://keriganmarketing.com">Site by KMA</a>.</p>
                    </div>
                </div>
            </div><!-- .container -->
        </div>
    </div>
</div><!-- #page -->

<?php wp_footer(); ?>

<script>



function stickFooter(){

    var body = $('body'),
        bodyHeight = body.height(),
        windowHeight = $(window).height(),
        selector = $('#sticky-footer');


    if ( bodyHeight < windowHeight ) {

        body.addClass("full");
        selector.addClass("stuck");
        selector.removeClass("unstuck");
    }else{

        body.removeClass("full");
        selector.removeClass("stuck");
        selector.addClass("unstuck");
    }
}

$(window).scroll(function() {
    if ($(this).scrollTop() > 10){
        $('#top').addClass("smaller");
    }else{
        $('#top').removeClass("smaller");
    }
});

$(window).load(function() {
    stickFooter();

    $(function() {
        $('.lazy').Lazy();
    });

});


</script>
<?php if(is_page(7221) || is_page(6784)){ ?>
	<script>

        function toggleSelect(){
            var agentSelectOption = document.getElementById('select-an-agent');
            var agentSelectDD = document.getElementById('agent-select-dd');
            if(agentSelectOption.checked == true){
                agentSelectDD.style.display = "inline-block";
            }else{
                agentSelectDD.style.display = "none";
            }
        }

        window.onload = function() {
            toggleSelect();
        }

    </script>
<?php } ?>
<?php if(is_front_page()){ ?>
    <script>
        /* for slider */

        function setNewNum(num,id){
            if(num.indexOf("000000") > 0 ){
                //var convertedtext = num.substring("000000", num.length -6);
                //$( id ).html(convertedtext + "M");
                $( id ).html(num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));

            }else if(num.indexOf("000") > 0 ){
                //var convertedtext = num.substring("000", num.length -3);
                //$( id ).html(convertedtext + "K");
                $( id ).html(num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
            }
        }

        window.onload = function() {
            $("#slider-range").slider({
                range: true,
                animate: true,
                min: 0,
                max: 5000000,
                values: [0, 5000000],
                slide: function (event, ui) {
                    $("#ihf-minprice-homes").val(Math.round(ui.values[0] / 10000) * 10000);
                    setNewNum(" $" + Math.round(ui.values[0] / 10000) * 10000, "#num1");

                    $("#ihf-maxprice-homes").val(Math.round(ui.values[1] / 10000) * 10000);
                    setNewNum(" $" + Math.round(ui.values[1] / 10000) * 10000, "#num2");

                    var text1 = "$" + Math.round($("#slider-range").slider("values", 0) / 10000) * 10000;
                    var text2 = "$" + Math.round($("#slider-range").slider("values", 1) / 10000) * 10000;

                    if ($("#ihf-maxprice-homes").val() == 5000000) {
                        text2 = 5000000 + "+";
                        $("#ihf-maxprice-homes").val("");
                        $("#num2").html("5,000,000+");

                    }

                    if ($("#ihf-minprice-homes").val() <= Number(50000)) {
                        text2 = "$0";
                        $("#ihf-minprice-homes").val("");
                        $("#num1").html("$0");

                    }

                    setNewNum(text1, "#num1");

                }

            });
        }

        function setMin(number) {
            $("#ihf-minprice-homes").val(number);
        }

        function setMax(number) {
            $("#ihf-maxprice-homes").val(number);
        }

        $('#min-price').on('change', function() {
            setMin( this.value );
        })
        $('#max-price').on('change', function() {
            setMax( this.value );
        })
    </script>
<?php } ?>

<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-54612119-1', 'auto');
    ga('send', 'pageview');

</script>
<script type="text/javascript" src="//cdn.callrail.com/companies/428231643/23b9e018b0e471ffb9ff/12/swap.js"></script> 
</body>
</html>
