<?php //QUICK SEARCH BOX ?>
<div id="smart-search-box">
	<div class="row">
		<form action="/property-search/" class="form-inline" method="get">
            <div class="search-control"></div>
			<input type="hidden" name="qs" value="true" >
            <input type="hidden" name="status[]" value="active" >
			<div class="col-12">
				<div class="input-container smart-select">
					<div class="input-group input-group-lg">
						<select class="area-select form-control select2-omni-field" name="omniField" id="id-area-select" >
                            <option value="">City, address, subdivision or zip</option>
                        </select>
						<span class="input-group-btn">
                            <button type="submit" class="btn btn-primary " ><img src="<?php echo getSvg('searchicon'); ?>" alt="Search" ></button>
                        </span>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="input-container property-type-select">
                    <select class="form-control form-control-lg select2-property-type" name="propertyType" >
                        <option value="">Property type</option>
                        <option value="Single Family Home">Single Family Home</option>
                        <option value="Condo / Townhome">Condo / Townhome</option>
                        <option value="Commercial">Commercial</option>
                        <option value="Lots / Land">Lots / Land</option>
                        <option value="Multi-Family Home">Multi-Family Home</option>
                        <option value="Manufactured">Manufactured</option>
                        <option value="Farms / Agricultural">Farms / Agricultural</option>
                        <option value="Other">Other</option>
                    </select>
				</div>
			</div>
			<div class="col-md-8 hidden-sm-down">
                <label>Price Range</label>
				<div id="slider-range"></div>
				<p class="range-text">from <span class="slider-num" id="num1">$0</span> to <span class="slider-num" id="num2">5,000,000+</span></p>
			</div>
            <div class="col-6 col-md-4 hidden-md-up">
                <div class="input-container property-type-select">
                    <select id="min-price" class="select-other form-control"   >
                        <option value="" >Min Price</option>
	                    <?php for($i = 50000; $i < 5000000; $i+=50000){
		                    echo '<option value="' . $i . '">$' . number_format( $i, 0, ".", ",") . '</option>';
	                    } ?>
                    </select>
                </div>
            </div>
            <div class="col-6 col-md-4 hidden-md-up">
                <div class="input-container property-type-select">
                    <select id="max-price" class="select-other form-control"   >
                        <option value="" >Max Price</option>
	                    <?php for($i = 50000; $i < 10000000; $i+=50000){
		                    echo '<option value="' . $i . '">$' . number_format( $i, 0, ".", ",") . '</option>';
	                    } ?>
                    </select>
                </div>
            </div>
            <input hidden="hidden" id="ihf-minprice-homes" name="minPrice" class="form-control ihf-search-form-input" type="hidden" value=""/>
            <input hidden="hidden" id="ihf-maxprice-homes" name="maxPrice" class="form-control ihf-search-form-input" type="hidden" value=""/>
            <input type="hidden" name="sortBy" value="date_modified">
            <input type="hidden" name="orderBy" value="DESC">
		</form>
	</div>
</div>

