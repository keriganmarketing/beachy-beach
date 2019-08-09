<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package KMA_DEMO
 */

$balance = (isset($_GET['balance']) ? $_GET['balance'] : 300000);
$down = (isset($_GET['downpayment']) ? $_GET['downpayment'] : null);

$percentdown = (isset($down) ? $down : $balance * .2);

$includeTaxes = (isset($_GET['includetaxes']) ? 
    ($_GET['includetaxes'] == 1 ? 'checked' : '') : 'checked');

$includeHOA = (isset($_GET['includehoa']) ? 
    ($_GET['includehoa'] == 1 ? 'checked' : '') : '');


get_header(); ?>
<div id="content">
    <div id="primary" class="content-area">
        <main id="main" class="site-main" >

            <?php while ( have_posts() ) : the_post();

                get_template_part( 'template-parts/content', 'page' );

            endwhile; ?>

        </main><!-- #main -->
    </div><!-- #primary -->
</div>
<div id="mortgage-calculator" style="width:100%;">
	<div class="container">
        <div id="mortgage-caculator-box" >
		<form name="mortcalc" id="mortform" >
            <div class="row justify-content-center no-gutters">
                <div id="col1" class="col-md-6 col-lg-4">
                    <label>Home Price</label> <input 
                            type="text" 
                            name="balance" 
                            id="balance" 
                            value="<?php echo isset($_GET['balance']) ? $_GET['balance'] : 300000; ?>"  ><br>
                    <label>Down payment</label> <input 
                            type="text" 
                            name="down_payment" 
                            id="downpayment" 
                            value="<?php echo $percentdown; ?>" 
                            onChange="updatePercent();" ><input 
                                type="text" 
                                name="down-pay_percent" 
                                id="downpaypercent" 
                                value="" 
                                onChange="updatePayment();" ><br>
                    <label>Interest rate</label> <input 
                            type="text" 
                            name="rate" 
                            id="rate" 
                            value="3.92" ><br>
                    <label>Loan term</label> <input 
                            type="text" 
                            name="term" 
                            id="term" 
                            value="<?php echo isset($_GET['term']) ? $_GET['term'] : 360; ?>" 
                            placeholder="360 months" >
                </div>
                <div id="col2" class="col-md-7 col-lg-4">
                    <canvas id="mortCal" style="min-height:200px;"></canvas>
                    <div id="payment"><span id="mon_payment">$<span id="payment_num">1,721</span></span></div>
                    <div id="labels">
                        <div id="left">
                            <div id="hoa"></div>
                            <div id="tax"></div>
                        </div>
                        <div id="right">
                            <div id="pi"></div>
                            <div id="ins"></div>
                        </div>
                    </div>
                    <div id="cal-button"><span class="calculate" onClick="mortCal();">Calculate</span></div>
                </div>
                <div id="col3" class="col-md-6 col-lg-4">
                    <label class="checkboxlabel">Include taxes</label> 
                        <span class="checkbox-container">
                            <input 
                                type="checkbox" 
                                name="include_taxes" 
                                id="includetaxes" 
                                <?php echo $includeTaxes; ?> >
                        </span><br>
                    <label>Property taxes</label> <input 
                        type="text" 
                        name="prop_taxes" 
                        id="proptaxes" 
                        value="1.2" ><br>
                    <label id="homeinsurancelabel">Home insurance</label> <input 
                        type="text" 
                        name="home_insurance" 
                        id="homeinsurance" 
                        value="<?php echo isset($_GET['insurance']) ? $_GET['insurance'] : 800; ?>" ><br>
                    <label class="checkboxlabel">Include HOA dues</label>
                        <span class="checkbox-container"><input 
                            type="checkbox" 
                            name="include_pmi" 
                            id="includepmi" 
                            <?php echo $includeHOA; ?> >
                        </span><br>
                    <label> HOA dues</label> <input 
                        type="text" 
                        name="hoa_dues" 
                        id="hoadues" 
                        value="<?php echo isset($_GET['hoa']) ? $_GET['hoa'] : null; ?>" >
                </div>
            </div>
			<input type="submit" onClick="mortCal();this.preventDefault();" style="visibility:hidden; display: block; height:1px; width:1px; margin:0; padding:0;"  >
		</form>
        </div>
	</div>
</div>
<?php 
wp_enqueue_script( 'chart-js' );
wp_enqueue_script( 'mortgage-calc' );
get_footer();