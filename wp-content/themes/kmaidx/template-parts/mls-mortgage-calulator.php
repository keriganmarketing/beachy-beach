<?php
/**
 * Created by PhpStorm.
 * User: Bryan
 * Date: 5/16/2017
 * Time: 4:13 PM
 */
?>
<div id="waves">
<div id="mortgage-calculator" style="width:100%;">
	<div class="container">

        <div class="mortgage-mobile-button hidden-md-up text-center">
            <button onclick="toggler('mortgage-caculator-box'); mortCal();" class="btn btn-lg btn-primary mb-3">Open Mortgage Calculator</button>
        </div>
        <div class="hidden-sm-down text-center">
            <h2><span>Mortgage Calculator</span></h2>
        </div>
        <div id="mortgage-caculator-box" style="display:none" >
		<form name="mortcalc" id="mortform" >
            <div class="row justify-content-center no-gutters">
                <div id="col1" class="col-md-6 col-lg-4">
                    <label>Home Price</label> <input type="text" name="balance" id="balance" value="300000"  ><br>
                    <label>Down payment</label> <input type="text" name="down_payment" id="downpayment" value="60000" onChange="updatePercent();" ><input type="text" name="down-pay_percent" id="downpaypercent" value="" onChange="updatePayment();" ><br>
                    <label>Interest rate</label> <input type="text" name="rate" id="rate" value="3.92" ><br>
                    <label>Loan term</label> <input type="text" name="term" id="term" value="360" placeholder="360 months"  >
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
                    <label class="checkboxlabel">Include taxes</label> <span class="checkbox-container"><input type="checkbox" name="include_taxes" id="includetaxes" checked="checked" ></span><br>
                    <label>Property taxes</label> <input type="text" name="prop_taxes" id="proptaxes" value="1.2" ><br>
                    <label id="homeinsurancelabel">Home insurance</label> <input type="text" name="home_insurance" id="homeinsurance" value="800" ><br>
                    <label class="checkboxlabel">Include HOA dues</label><span class="checkbox-container"><input type="checkbox" name="include_pmi" id="includepmi" checked="checked" ></span><br>
                    <label> HOA dues</label> <input type="text" name="hoa_dues" id="hoadues" value="200" >
                </div>
            </div>
			<input type="submit" onClick="mortCal();this.preventDefault();" style="visibility:hidden; display: block; height:1px; width:1px; margin:0; padding:0;"  >
		</form>
        </div>
	</div>
</div>
</div>