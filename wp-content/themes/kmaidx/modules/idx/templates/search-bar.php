<div class="search-bar-container">
    <form class="form-inline" method="post" >
        <div class="search-bar">
            <div class="row">
                <input type="hidden" name="cmd" value="search" >
                <div class="col-md-6">
                    <div class="input-container">
                    <select class="area-select form-control" name="AREA[]" id="id-area-select" multiple="multiple"></select>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="input-container">
                    <select class="prop-type-input form-control" name="PROP_TYPE[]" multiple="multiple"></select>
                    </div>
                </div>
                <div class="col text-right">
                    <div class="input-container">
                    <div class="button-group">
                        <button type="button" class="btn btn-default dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onclick="toggler('advanced-menu');">Advanced</button>
                        <button type="submit" class="btn btn-primary " >Search</button>
                    </div>
                    </div>
                </div>
            </div>
	    </div>
        <div id="advanced-menu" class="advanced-menu hidden">
            <div class="row">
                <div class="col-md-4 col-lg-6">
                    <div class="row">
                    <div class="col-xs-6 col-md-12 col-lg-6">
                        <label class="sr-only" for="PRICE_MIN">Min Price</label>
                        <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                            <div class="input-group-addon">Min Price</div>
                            <select name="PRICE_MIN" id="PRICE_MIN" class="form-control select-other" >
                            <option value="" >Any</option>
                            <?php foreach($mls->priceArray as $key => $value){
                                if($key == $mls->variables['PRICE_MIN']){
                                    echo '<option value="'.$key.'" selected>'.$value.'</option>';
                                }else{
                                    echo '<option value="'.$key.'" >'.$value.'</option>';
                                }
                            } ?>
                            </select>
                        </div>
                        <p></p>
                    </div>
                    <div class="col-xs-6 col-md-12 col-lg-6">
                        <label class="sr-only" for="PRICE_MAX">Max Price</label>
                        <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                            <div class="input-group-addon">Max Price</div>
                            <select name="PRICE_MAX" id="PRICE_MAX" class="form-control select-other" >
                            <option value="" >Any</option>
                            <?php foreach($mls->priceArray as $key => $value){
                                if($key == $mls->variables['PRICE_MAX']){
                                    echo '<option value="'.$key.'" selected>'.$value.'</option>';
                                }else{
                                    echo '<option value="'.$key.'" >'.$value.'</option>';
                                }
                            } ?>
                            </select>
                        </div>
                        <p></p>
                    </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6 col-md-12 col-lg-6">
                            <label class="sr-only" for="TOT_HEAT_SQFT">Total H/C Sqft</label>
                            <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                                <div class="input-group-addon">Total H/C Sqft</div>
                                <select name="TOT_HEAT_SQFT" id="TOT_HEAT_SQFT" class="form-control select-other" >
                                    <option value="" >Any</option>
                                    <?php foreach($mls->sqftArray as $key => $value){
                                        if($key == $mls->variables['TOT_HEAT_SQFT']){
                                            echo '<option value="'.$key.'" selected>'.$value.'</option>';
                                        }else{
                                            echo '<option value="'.$key.'" >'.$value.'</option>';
                                        }
                                    } ?>
                                </select>
                            </div>
                            <p></p>
                        </div>
                        <div class="col-xs-6 col-md-12 col-lg-6">
                            <label class="sr-only" for="ACREAGE">Acreage</label>
                            <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                                <div class="input-group-addon">Acreage</div>
                                <select name="ACREAGE" id="ACREAGE" class="form-control select-other" >
                                    <option value="" >Any</option>
                                    <?php foreach($mls->acreageArray as $key => $value){
                                        if($key == $mls->variables['ACREAGE']){
                                            echo '<option value="'.$key.'" selected>'.$value.'</option>';
                                        }else{
                                            echo '<option value="'.$key.'" >'.$value.'</option>';
                                        }
                                    } ?>
                                </select>
                            </div>
                            <p></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="radio-box">
                    <div class="row">
                        <label class="sr-only" for="BEDROOMS">Beds</label>
                        <div class="col-12 col-md-2 label input-group-addon" >Beds</div>
                        <div class="col form-check form-check-inline">

                        <?php foreach($mls->bedArray as $key => $value){
                            echo '<label class="custom-control custom-radio">';
                            if($key == $mls->variables['BEDROOMS']){
                                echo '<input type="radio" name="BEDROOMS" value="'.$key.'" checked class="custom-control-input" >';
                            }else{
                                echo '<input type="radio" name="BEDROOMS" value="'.$key.'" class="custom-control-input" >';
                            }

                            echo '<span class="custom-control-indicator"></span>
                              <span class="custom-control-description">'.$value.'</span>
                              </label>';
                        } ?>
                        </div>
                    </div></div>
                    <div class="radio-box">
                    <div class="row">
                        <label class="sr-only" for="BATHS">Baths</label>
                        <div class="col-12 col-md-2 label input-group-addon" >Baths</div>
                        <div class="col form-check form-check-inline">
                            <?php foreach($mls->bathArray as $key => $value){
                                echo '<label class="custom-control custom-radio">';
                                if($key == $mls->variables['BATHS']){
                                    echo '<input type="radio" name="BATHS" value="'.$key.'" checked class="custom-control-input" >';
                                }else{
                                    echo '<input type="radio" name="BATHS" value="'.$key.'" class="custom-control-input" >';
                                }

                                echo '<span class="custom-control-indicator"></span>
                              <span class="custom-control-description">'.$value.'</span>
                              </label>';
                            } ?>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

	<?php if(is_array($area) || is_array($propertytype) || $minprice!='' || $maxprice!='' || $beds!='' || $baths!='' || $sqft!='' || $acreage!='' ){ ?>
        <div class="search-criteria">
			<?php
			if(isset($area) && $area!= '') {

				echo '<a class="btn btn-default btn-sm" ';
				if(is_array($area)){
					for($i=0;$i<count($area);$i++){
						echo ' data-call="area|' . urlencode(trim($area[$i])) . '" >AREA: ' . $area[$i];
					}
				}
				echo '</a> ';

			}

			if(isset($propertytype) && $propertytype!= '') {

				echo '<a class="btn btn-default btn-sm"';
				if(is_array($propertytype)){
					for($i=0;$i<count($propertytype);$i++){
						echo ' data-call="propertytype|' . urlencode(trim($propertytype[$i])) . '" >TYPE: ' . $propertytype[$i];
					}
				}
				echo '</a> ';

			}

			if(isset($minprice) && $minprice!= '') {
				echo '<a class="rembutton btn btn-default btn-sm" data-call="minprice" ><span class="select2-selection__choice__remove" role="presentation">×</span> Min Price: $' . number_format($minprice) . '</a> ';
			}
			if(isset($maxprice) && $maxprice!= '') {
				echo '<a class="rembutton btn btn-default btn-sm" data-call="maxprice" ><span class="select2-selection__choice__remove" role="presentation">×</span> Max Price: $' . number_format($maxprice) . '</a> ';
			}
			if(isset($beds) && $beds!= '') {
				echo '<a class="rembutton btn btn-default btn-sm" data-call="beds" ><span class="select2-selection__choice__remove" role="presentation">×</span> Bedrooms: ' . $beds . '+ </a> ';
			}
			if(isset($baths) && $baths!= '') {
				echo '<a class="rembutton btn btn-default btn-sm" data-call="baths" ><span class="select2-selection__choice__remove" role="presentation">×</span> Bathrooms: ' . $baths . '+ </a> ';
			}
			if(isset($sqft) && $sqft!= '') {
				echo '<a class="rembutton btn btn-default btn-sm" data-call="sqft" ><span class="select2-selection__choice__remove" role="presentation">×</span> H/C Sqft: ' . $sqft . '</a> ';
			}
			if(isset($acreage) && $acreage!= '') {
				echo '<a class="rembutton btn btn-default btn-sm" data-call="acreage" ><span class="select2-selection__choice__remove" role="presentation">×</span> Acreage: ' . $acreage . '+</a> ';
			}

			echo '<a class="rembutton btn btn-danger btn-sm pull-right" data-call="' . $_SERVER['SELF'] . '?remove=all" >reset</a>';

			?>
        </div>
	<?php } ?>

</div>
<script type="text/javascript">
	window.onload = function(){
		$('.area-select').select2();
		$('.prop-type-input').select2();
        $('.select-other').select2({
            width: '100%',
            tags: true
        });
	}
</script>
