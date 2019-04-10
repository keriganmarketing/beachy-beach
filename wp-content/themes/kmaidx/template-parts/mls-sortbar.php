<?php
?>
<div class="search-bar-container">
    <div class="search-criteria">
        <div class="row">
            <div class="col">
                <?php echo (isset($totalResults) ? '<em>showing ' . number_format($totalResults) . ' results</em>' : ''); ?>
            </div>
            <div class="col">
                <form class="form form-inline text-right" method="get" >
                <?php
                if(isset($_GET['omniField']) && $_GET['omniField']!= '') {
                    echo '<input type="hidden" name="omniField" value="'.$_GET['omniField'].'" > ';
                }
                if(isset($_GET['propertyType']) && $_GET['propertyType']!= '') {
                    echo '<input type="hidden" name="propertyType" value="'.$_GET['propertyType'].'" > ';
                }
                if(isset($_GET['min_price']) && $_GET['min_price']!= '') {
                    echo '<input type="hidden" name="min_price" value="'.$_GET['min_price'].'" > ';
                }
                if(isset($_GET['max_price']) && $_GET['max_price']!= '') {
                    echo '<input type="hidden" name="max_price" value="'.$_GET['max_price'].'" > ';
                }
                if(isset($_GET['bedrooms']) && $_GET['bedrooms']!= '') {
                    echo '<input type="hidden" name="bedrooms" value="'.$_GET['bedrooms'].'" > ';
                }
                if(isset($_GET['bathrooms']) && $_GET['bathrooms']!= '') {
                    echo '<input type="hidden" name="bathrooms" value="'.$_GET['bathrooms'].'" > ';
                }
                if(isset($_GET['sq_ft']) && $_GET['sq_ft']!= '') {
                    echo '<input type="hidden" name="sq_ft" value="'.$_GET['sq_ft'].'" > ';
                }
                if(isset($_GET['acreage']) && $_GET['acreage']!= '') {
                    echo '<input type="hidden" name="acreage" value="'.$_GET['acreage'].'" > ';
                }

                $sort = isset($_GET['sortBy']) ? $_GET['sortBy'] : 'date_modified';

                ?>
                    <div id="sortbox" class="ml-auto">
                        <div class="input-group input-group-sm">
                            <input type="hidden" name="qs" value="search" >
                            <select class="form-control form-control-sm" name="sortBy" >
                                <option value="date_modified" <?php if($sort=='date_modified'){ echo 'selected'; } ?> >Updated</option>
                                <option value="price" <?php if($sort=='price'){ echo 'selected'; } ?>>Cheapest</option>
                                <!--<option value="list_date">Newest</option>-->
                            </select>
                            <span class="input-group-btn"><button type="submit" class="btn btn-sm btn-default" >Sort</button></span>
                        </div>
                        <a class="rembutton btn btn-primary btn-sm pull-right" href="/property-search/" >property search</a>
                    </div>
                    <input type="hidden" name="orderBy" value="ASC">
                    <input type="hidden" name="q" value="search" >
                </form>
            </div>
        </div>
    </div>

</div>
