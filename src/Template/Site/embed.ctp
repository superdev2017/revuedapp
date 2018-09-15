<?php  use App\Utility\Common; ?>
<?php if ( ! in_array($user->status, ['active', 'trial'])) exit; ?>
<?php
    function print_star_rating( $star ) {
        for ( $i = 1; $i <= 5; $i ++ ) {
            if ( $star >= $i ) echo '<span class="active">☆</span>';
            else echo '<span>☆</span>'; 
        }
    }

    if ($user->user_setting->rv_on) {
        $this->Html->script(['//vjs.zencdn.net/6.2.7/video.js'], ['block' => 'script']);
        $this->Html->css(['//vjs.zencdn.net/6.2.7/video-js.css'], ['block' => 'script']);
    }

    $avvo_lawyer_url = sprintf( 'https://www.avvo.com/attorneys/%s/reviews.html', $user->user_setting->av_api_id );
    $gplus_url = $user->user_setting->gp_plus_personal;
    $facebook_url = sprintf( 'https://www.facebook.com/%s/reviews', $user->user_setting->fb_page_id );
    $yelp_url = sprintf( 'https://www.yelp.com/biz/%s', $user->user_setting->yp_business_id );

    //$tab_names = array("6" => "Revued", "3" => "Google +", "4" => "Facebook", "5" => "Yelp", "2" => "Avvo");
?>

<?php if ($user->user_setting->display_mode == 'floats'): ?>
    <a href="#" id="revued-close" aria-label="Close Revued Box">&nbsp; &times; &nbsp;</a>
<?php endif ?>
<div id="revued-widget" class="reviews-container">
    <h3 class="reviews-heading"><?= $user->user_setting->site_name ?></h3>

    <main class='reviews-main'>
        <div class="gallery js-flickity" data-flickity-options='{"contain": true, "imagesLoaded": true, "autoPlay": true, "pageDots": false, "prevNextButtons": false, "wrapAround": false}'>

            <?php if (!isset($user->user_setting->custom_settings['tab_orders']) || $user->user_setting->custom_settings['tab_orders'] == ''):
                $tab_orders = ["6", "3", "4", "5", "2"]; //
            else:
                $tab_orders = explode(',', $user->user_setting->custom_settings['tab_orders']);
            endif?>
            <?php $first = true; foreach ($tab_orders as $order) :?>
                <?php if ($user->user_setting->gp_on && $order == "3"): ?>
                    <div class="source-tab" data-content="#content2">
                        <input id="tab2" type="radio" name="tabs" <?php echo $first ? ' checked' : ''?>/>
                        <label for="tab2">
                            <div class="source-img"></div>
                            <span class="circleBase type2"><?= count($data['gp']) ?></span>
                        </label>
                    </div>
                <?php endif ?>

                <?php if ($user->user_setting->av_on && $order == "2"): ?>
                    <div class="source-tab" data-content="#content1">
                        <input id="tab1" type="radio" name="tabs" <?php echo $first ? ' checked' : ''?>/>
                        <label for="tab1">
                            <div class="source-img"></div>
                            <span class="circleBase type2"><?= count($data['av']) ?></span>
                        </label>
                    </div>
                <?php endif ?>

                <?php if ($user->user_setting->fb_on  && $order == "4"): ?>
                    <div class="source-tab" data-content="#content4">
                        <input id="tab4" type="radio" name="tabs" <?php echo $first ? ' checked' : ''?>/>
                        <label for="tab4">
                            <div class="source-img"></div>
                            <span class="circleBase type2"><?= count($data['fb']) ?></span>
                        </label>
                    </div>
                <?php endif ?>

                <?php if ($user->user_setting->yp_on && $order == "5"): ?>
                    <div class="source-tab" data-content="#content3">
                        <input id="tab3" type="radio" name="tabs" <?php echo $first ? ' checked' : ''?>/>
                        <label for="tab3">
                            <div class="source-img"></div>
                            <span class="circleBase type2"><?= count($data['yp']) ?></span>
                        </label>
                    </div>
                <?php endif ?>

                <?php if ($user->user_setting->rv_on && $order == "6"): ?>
                    <div class="source-tab" data-content="#content5">
                        <input id="tab5" type="radio" name="tabs" <?php echo $first ? ' checked' : ''?>/>
                        <label for="tab5">
                            <div class="source-img"></div>
                            <span class="circleBase type2"><?= count($data['rv']) ?></span>
                        </label>
                    </div>
                <?php endif ?>

            <?php $first = false; endforeach;?>

        </div>

        <!-- Avvo content -->
        <section id="content1">
            <?php if ( count($data['av']) > 0 ): ?>
            <div class="testimonial-wrap">
                <?php foreach ( $data['av'] as $entry ): ?>
                    <div class="testimonial">
                        <div class="quote">
                            <p class="avvo_uid">
                                <span class="avvo_sm">Posted by </span>
                                <?= $entry['author'] ?>
                            </p>
                            <p class="avvo_date"><?= date('F j, Y', strtotime($entry['date'])) ?></p>
                            <strong><a href="<?= $avvo_lawyer_url ?>" target="_blank"><?= htmlspecialchars_decode( $entry['title'] ) ?></a></strong>
                            <div class="rating">
                                <a href="<?= $avvo_lawyer_url ?>" target="_blank">
                                    <?= $this->Html->image('embed/tab1.png', ['class' => 'avvo-logo']) ?>
                                </a>
                                <?php print_star_rating( $entry['rating'] ) ?>
                            </div>
                            <div class='star-text'><?= sprintf('%d out of 5 stars', $entry['rating'] ); ?></div>

                            <!-- Avvo Review Text-->
                            <div class="avvo_rev"><?= $entry['body'] ?></div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
            <?php else: ?>
                <h3 class="no-reviews">Sorry! No Avvo review found! Thank you for visiting us.</h3>
            <?php endif ?>
        </section>


        <!-- Google+ content -->
        <section id="content2" style="display: block;">
            <?php if ( count($data['gp']) > 0 ): ?>
            <div class="testimonial-wrap">
                <?php foreach ( $data['gp'] as $entry ): ?>
                    <div class="testimonial">
                        <div class="quote">
                            <p class="plus_name">
                                <a href="<?= $gplus_url ?>" target="_blank">
                                    <div class="source-img"></div>
                                </a>
                                <?= $entry['author'] ?>
                            </p>
                            <p class="plus_date"><?= date('F j, Y', strtotime($entry['date'])) ?></p>

                            <div class="rating">
                                <?php print_star_rating( $entry['rating'] ) ?>
                                <span class="g_star">
                                    <?= sprintf( '%d/5', $entry['rating'] ) ?>
                                </span>
                            </div>

                            <div class='star-text'></div>

                            <!--Google Reviews Text-->
                            <div class="plus_rev">
                                <?= $entry['body'] ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
            <?php else: ?>
                <h3 class="no-reviews">Sorry! <br> No Google+ review found! <br> Thank you for visiting us.</h3>
            <?php endif ?>
        </section>


        <!-- Yelp content -->
        <section id="content3">
            <?php if ( count($data['yp']) > 0 ): ?>
            <div class="testimonial-wrap">
                <?php foreach ( $data['yp'] as $entry ): ?>
                    <div class="testimonial">
                        <div class="quote">
                            <p class="yelp_uid">
                                <?= $entry['author'] ?>
                                <span class="yelp_date"><?= date('F j, Y', strtotime($entry['date'])) ?></span>
                            </p>

                            <div class="rating">
                                <a href="<?= $yelp_url ?>" target="_blank">
                                    <div class="source-img"></div>
                                </a>
                                <?php print_star_rating( $entry['rating'] ) ?>
                            </div>

                            <div class='star-text'></div>

                            <div class="yelp_rev"><?= $entry['body'] ?></div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
            <?php else: ?>
                <h3 class="no-reviews">Sorry! <br> No Yelp review found! <br> Thank you for visiting us.</h3>
            <?php endif ?>
        </section>


        <!-- Facebook content -->
        <section id="content4">
            <?php if ( count($data['fb']) > 0 ): ?>
            <div class="testimonial-wrap">
                <?php foreach ( $data['fb'] as $entry ): ?>
                    <div class="testimonial">
                        <div class="quote">
                            <p class="fb_uid">
                                <?= $entry['author'] ?>
                                <span class="fb_grey"> reviewed </span>
                                <a class="fb_pagelink" href="https://www.facebook.com/187798914600145/"><?= $user->user_setting->fb_page_title ?></a>
                                <span class="fb_grey">:</span>
                            </p>

                            <p class="fb_date"><?= date('F j, Y', strtotime($entry['date'])) ?></p>

                            <div class="rating">
                                <a href="<?= $facebook_url ?>" target="_blank">
                                    <div class="source-img"></div>
                                </a>

                                <?php print_star_rating( $entry['rating'] ) ?>
                            </div>

                            <div class="fb_rev"><?= $entry['body'] ?></div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
            <?php else: ?>
                <h3 class="no-reviews">Sorry! <br> No Facebook review found! <br> Thank you for visiting us.</h3>
            <?php endif ?>
        </section>


        <!-- Revued content -->
        <section id="content5">
            <?php if ( count($data['rv']) > 0 ): ?>
            <div class="testimonial-wrap">
                <?php foreach ( $data['rv'] as $entry ): ?>
                    <div class="testimonial">
                        <div class="rating">
                            <div class="source-img"></div> <strong><?= $entry['author'] ?></strong> <small><?= Common::time_elapsed_string($entry['date']) ?></small>
                            <br /><?php print_star_rating( $entry['rating'] ) ?>
                        </div>
                        <div class="quote">

                            <!-- Revued Video-->
                            <?php
                                $review_assets = [];
                                $poster = null;
                                if (count($entry['review_assets']) > 1) {
                                    foreach($entry['review_assets'] as $source) {
                                        if ($source['type'] == 'jpg') {
                                            $poster = $source['src'];
                                            continue;
                                        }

                                        $review_assets[] = $source;
                                    }
                                }
                            ?>
                            <video id="vid-<?= $entry['id'] ?>" class="video-js" controls preload="auto" height="180" poster="<?= $this->Url->build("/" . $poster ?: '/img/embed/thumbnail.jpg') ?>" data-setup='{"autoplay": false}'>
                                <?php if (isset($entry['review_assets'])): ?>
                                    <?php foreach($review_assets as $source): ?>
                                        <source src="<?= $this->Url->build("/" . $source['src']) ?>" type="video/<?= $source['type'] ?>">
                                    <?php endforeach ?>
                                <?php else: ?>
                                    <source src="//vjs.zencdn.net/v/oceans.mp4" type="video/mp4">
                                    <source src="//vjs.zencdn.net/v/oceans.webm" type="video/webm">
                                <?php endif ?>
                                <p class="vjs-no-js">
                                    To view this video please enable JavaScript, and consider upgrading to a web browser that
                                    <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                                </p>
                            </video>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
            <?php else: ?>
                <h3 class="no-reviews">Sorry! No Revued videos found! Thank you for visiting us.</h3>
            <?php endif ?>
        </section>
    </main>

    <!--Powered by Revued-->
    <div class="brand">
        <a href="https://www.revued.com/" target="_blank" class="revbrandtxt">
            <?= $this->Html->image('embed/tab5.png') ?> Powered by <strong>Revued</strong>
        </a>
    </div>

</div>

<style>
    <?php if ($user->user_setting->custom_settings): ?>
        <?php if ($user->user_setting->custom_settings['header_color']): ?>
            .reviews-heading {
                background-color: <?= $user->user_setting->custom_settings['header_color'] ?>
            }
        <?php endif ?>
        <?php if ($user->user_setting->custom_settings['footer_color']): ?>
            .brand {
                background-color: <?= $user->user_setting->custom_settings['footer_color'] ?>
            }
        <?php endif ?>
        <?php if ($user->user_setting->custom_settings['background_color']): ?>
            .reviews-main {
                background-color: <?= $user->user_setting->custom_settings['background_color'] ?>
            }
        <?php endif ?>
    <?php endif ?>
</style>

<script>
    $(document).ready(function(){
        $(".source-tab").click(function(){
            $("section").hide();
            $($(this).data('content')).show();
        });
    });
    /*
    window.onload = function() {
        var tabs = document.getElementsByClassName('source-tab');

        for (var i = 0; i < tabs.length; i++){
            tabs[i].addEventListener('click', function() {
                selectThisTab
            });
        }
    }

    function selectThisTab() {
        console.log('h');
        
            document.getElementsByTagName('section').style.display = 'none';
            document.getElementById(id).style.display = 'block';
            //tabs[i].style.display = 'none';
            //console.log(tabs[i]);

    }*/
</script>

<!-- Global Site Tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-90405372-3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments)};
  gtag('js', new Date());

  gtag('config', 'UA-90405372-3');
</script>
