<?php
/**
 * Created by PhpStorm.
 * User: Bryan
 * Date: 5/22/2017
 * Time: 1:50 PM
 */

$i = 0;

$preferredImage = ($listingInfo->preferred_image != '' ? $listingInfo->preferred_image : get_template_directory_uri() . '/img/beachybeach-placeholder.jpg' );

$modalIndicators = '<ol class="carousel-indicators hidden-sm-down">';
$modalImages     = '<div class="carousel-inner" role="listbox">';
?>
<div class="listing-photo">
    <div class="embed-responsive embed-responsive-16by9">
        <div class="main-prop-photo" style="overflow:hidden;">
            <img 
                 src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                 data-src="<?php echo str_replace('http://','//', $preferredImage); ?>" 
                 class="embed-responsive-item lazy"
                 alt="MLS Property <?php echo $listingInfo->mls_account; ?>" style="width:100%"/>
        </div>
    </div>
</div>
<div class="row no-gutters">
    <?php

    foreach ($listingInfo->photos as $photo) {
        $photoUrl = str_replace('http://','//', $photo->url);
        if ($i > 0) {
            $active = '';
            if ($i + 2 <= count($listingInfo->photos)) {
                $nextImage = $i;
            } else {
                $nextImage = 0;
                $active    = 'active';
            }

            $modalIndicators .= '<li data-target="#myCarousel" data-slide-to="' . ($i-1) . '" class="' . $active . '"></li>';
            $modalImages .= '
                    <div class="carousel-item ' . $active . '" >
                      <img src="' . $photoUrl . '" alt="' . $photo->photo_description . '" style="width:100%; height:auto !important; display: block;">
                      <div class="carousel-caption">';

            if ($photo->photo_description != '') {
                $modalImages .= '<p class="hidden-sm-down">' . $photo->photo_description . '</p>';
            }

            $modalImages .= '
                      </div>
                    </div>';

            ?>
            <div class="hidden-sm-down col-sm-6 col-md-4 col-xl-3 listing-photo">
                <div class="embed-responsive embed-responsive-16by9">
                    <div class="sub-photo-container">
                        <a href="#" class="thumbnail" data-toggle="modal" data-target="#lightbox" data-slide-to="<?php echo $nextImage - 1; ?>">
                            <img src="<?php echo $photoUrl; ?>" data-src="<?php echo $photoUrl; ?>"
                                 class="embed-responsive-item" style="width:100%"
                                 alt="MLS Property <?php echo $listingInfo->mls_account; ?>"
                                 data-slide-to="<?php echo $nextImage - 1; ?>"/>
                        </a>
                    </div>
                </div>
            </div>
            <?php
        }
        $i++;
    } ?>
</div>

<?php
$modalIndicators .= '</ol>';
$modalImages .= '</div>';

$modalControl = '
            <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
              <div class="carousel-control-prev-icon" aria-hidden="true"></div>
              <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
              <div class="carousel-control-next-icon" aria-hidden="true"></div>
              <span class="sr-only">Next</span>
            </a>';

$modalContent = '<div id="myCarousel" class="carousel slide" style="display: block;" >' . $modalIndicators . $modalImages . $modalControl . '</div>';

?>

<div id="lightbox" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <button data-dismiss="modal" aria-hidden="true" style="padding: 0 8px 3px; position: fixed; top:0; right: 0; border-radius: 0 0 0 6px; z-index:9999999999; padding-top:.5rem;" class="btn btn-primary btn-lg" >close</button>
    <div class="modal-dialog modal-lg" style="display: block;">
        <div class="modal-content" style="display: block;">
            <div class="modal-body" style="display: block;">
                <!--<img src="" alt="" class="" />-->
                <?php echo $modalContent; ?>
            </div>
            <?php if (isset($listingInfo->videos)) { ?>
                <div class="modal-footer text-xs-center">
                    <a href="<?php echo $listingInfo->videos; ?>" target="_blank" class="btn btn-lg btn-danger">Open
                        Virtual Tour</a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

