<?= $message ?>

<br />
<?php if ($settings->gp_on): ?>
    <br />
    <a href="<?= $settings->gp_review_url ?>">Leave a Google Review</a>
<?php endif ?>

<?php if ($settings->fb_on): ?>
    <br />
    <a href="https://www.facebook.com/<?= $settings->fb_page_id ?>/reviews">Leave a Facebook Review</a>
<?php endif ?>

<?php if ($settings->yp_on): ?>
    <br />
    <a href="https://www.yelp.com/biz/<?= $settings->yp_business_id ?>">Leave a Yelp Review</a>
<?php endif ?>
