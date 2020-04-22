<?php
/**
 * Plugin Name: NHG Rebate Calculator
 * Plugin URI: http://larasoftbd.com/
 * Description: NHG Calculator. used following shortcode in your content. Shortcode: [nhgcalculator]
 * Version: 1.0.0
 * Author: larasoft
 * Author URI: https://larasoftbd.net
 * Text Domain: NHGRebateCalculator
 * Domain Path: /languages
 * Requires at least: 4.0
 * Tested up to: 4.8
 *
 * @package     NHG_Rebate_Calculator
 * @category 	Core
 * @author 		LaraSoft
 */


add_action( 'wp_enqueue_scripts', 'calculator_script' );
function calculator_script(){
    wp_enqueue_style( 'nhgCalculatorCSS', plugins_url( ) . '/nhg_rebate_calculator/css/nhgCalculator.css', array(), true, 'all' );
}




if(!function_exists('nhgCalculatorCallback')){
	function nhgCalculatorCallback(){
		ob_start();
?>
	    <!-- <div id="sevingCalc" class="savings">
	        <div class="refund">
	            <div class="ours">
	                <div class="amount" id="our-refund"></div>
	                <div class="sans-serif">Our solution</div>
	            </div>	            
	        </div>
	        <div class="price">
	            <label for="price-slider" class="sans-serif">
	                <div id="price-value" class="amount"></div>
	                <span>Home price</span>
	            </label>
	            <input type="range" id="price-slider" class="slider" min="100000" max="2500000" value="100000" step="1500">
	        </div>
	        <div class="disclaimer sans-serif">*Estimated cost, talk to an agent for a complete quote. **Traditional agent cost is based on a 3% selling commission.</div>
		</div>
		 -->


		<section id="sevingCalc" class="calculation savings">
        <h4 class="sans-serif">
            <span>I want to</span>
            <ul class="selections">
                <li class="selection active" id="select-sell">sell</li>
                <li class="selection" id="select-buy">buy</li>
            </ul>
        </h4>
        <div>
			 <div id="sell">
                <h5 class="sans-serif">SEE HOW MUCH YOU CAN SAVE</h5>
                <div class="savings">
                    <div class="ours">
                        <div class="amount" id="our-savings"></div>                                                                  
                    </div>
                </div>
                <div class="price">
                    <label for="savings-price-slider" class="sans-serif">
                        <div id="savings-price-value" class="amount"></div>
                        <span>Home price</span>
                    </label>
                    <input type="range" id="savings-price-slider" class="slider" min="100000" max="2500000" value="100000" step="1000">
                </div>
				 <div class="cell" style="text-align:right">$2,500,000</div>				
                <div class="disclaimer sans-serif">*Estimate based on 3% commission paid to us on a sale.  Contact us on any specific home for exact amount.</div>
            </div>
            <div id="buy" style="display: none">
                <h5 class="sans-serif">HOW MUCH CAN YOU GET AT CLOSING?</h5>
                <div class="refund">
                    <div class="ours">
                        <div class="amount" id="our-refund"></div>                              
                    </div>
                </div>
                <div class="price">
                    <label for="refund-price-slider" class="sans-serif">
                        <div id="refund-price-value" class="amount"></div>
                        <span>Home price</span>
                    </label>
                    <input type="range" id="refund-price-slider" class="slider" min="100000" max="2500000" value="100000" step="1000">
                </div>
				<div class="cell" style="text-align:right">$2,500,000</div>
                <div class="disclaimer sans-serif">*We share the commission with you! Projection is based on a 3% buyer agent commission</div>
            </div>
        </div>
    </section>
	<script>
    window.addEventListener('load', function() {
        //script for switching tabs
        (function() {
            document.getElementById('select-sell').addEventListener('click', function() {
                this.className = 'selection active';
                document.getElementById('select-buy').className = 'selection';
                document.getElementById('buy').style.display = 'none';
                document.getElementById('sell').style.display = 'block';
            });

            document.getElementById('select-buy').addEventListener('click', function() {
                this.className = 'selection active';
                document.getElementById('select-sell').className = 'selection';
                document.getElementById('sell').style.display = 'none';
                document.getElementById('buy').style.display = 'block';
            });
        })();

        //script for the sell tab
        (function() {
            function computeSavigs(price) {
                var savingsPerPrice = [
                    { minprice: 0,       savings: 1500 },
                    { minprice: 250000,  savings: 3750 },
                    { minprice: 300000,  savings: 4500 },
                    { minprice: 350000,  savings: 5250 },
                    { minprice: 400000,  savings: 6000 },
                    { minprice: 450000,  savings: 6750 },
                    { minprice: 1000000, savings: 15000 },
                    { minprice: 2000000, savings: 30000 }
                ];

                for(var i = 0; i < savingsPerPrice.length; ++i) {
                    if(price < savingsPerPrice[i].minprice) {
                        return savingsPerPrice[i].savings;
                    }
                }

                return savingsPerPrice[savingsPerPrice.length - 1].savings;
            }

            var priceSlider = document.getElementById('savings-price-slider');

            //update savings amount whenever price value changes
            priceSlider.addEventListener('input', function() {
                // document.getElementById('our-savings').innerText = '$' + Number(computeSavigs(this.value)).toLocaleString('en', {
                document.getElementById('our-savings').innerText = '$' + Number(this.value * .015).toLocaleString('en', {
                    useGrouping: true
                });

                document.getElementById('savings-price-value').innerText = '$' + Number(priceSlider.value).toLocaleString('en', {
                    useGrouping: true
                });
            });

            //update savings amount according to default price value
            priceSlider.dispatchEvent(
                new Event('input', {
                    'bubbles': true,
                    'cancelable': true
                })
            );
        })();

        //script for the buy tab
        (function() {
            var priceSlider = document.getElementById('refund-price-slider');

            //update refund amount whenever price value changes
            priceSlider.addEventListener('input', function() {
                document.getElementById('our-refund').innerText = '$' + Number(this.value * .015).toLocaleString('en', {
                    useGrouping: true
                });

                document.getElementById('refund-price-value').innerText = '$' + Number(priceSlider.value).toLocaleString('en', {
                    useGrouping: true
                });
            });

            //update refund amount according to default price value
            priceSlider.dispatchEvent(
                new Event('input', {
                    'bubbles': true,
                    'cancelable': true
                })
            );
        })();
    });
</script>


<?php		
	$output = ob_get_clean();
		return $output;
	}
	add_shortcode( 'nhgcalculator', 'nhgCalculatorCallback' );
}
