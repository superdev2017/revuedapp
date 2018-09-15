<?php $this->Html->script(['//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js', '//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/additional-methods.min.js', 'dashboard.js'], ['block' => 'script']); ?>

<?php if ($loggedUser->user_setting->rv_on): ?>
    <?php $this->Html->script(['//vjs.zencdn.net/6.2.7/video.js'], ['block' => 'script']); ?>
    <?php $this->Html->css(['//vjs.zencdn.net/6.2.7/video-js.css'], ['block' => 'css']); ?>
<?php endif ?>

<?php $this->assign('title', __('Revued Dashboard')); ?>

<?= $this->element('menu') ?>

<div class="row spaced">
    <div class="review-status text-center">
        <div class="col-md-3 col-xs-12"><a class="filter-status" data-filter-value="all"><?= __('All Reviews') ?>: <?= array_sum($statusCount) ?></a></div>
        <div class="col-md-3 col-xs-12"><a class="filter-status" data-filter-value="active"><?= __('Posted ') ?>: <?= isset($statusCount['active']) ? $statusCount['active'] : 0 ?></a></div>
        <div class="col-md-3 col-xs-12"><a class="filter-status" data-filter-value="archived"><?= __('Archived ') ?>: <?= isset($statusCount['archived']) ? $statusCount['archived'] : 0 ?></a></div>
        <div class="col-md-3 col-xs-12"><a class="filter-status" data-filter-value="pending"><?= __('Pending ') ?>: <?= isset($statusCount['pending']) ? $statusCount['pending'] : 0 ?></a></div>
    </div>
</div>


<div class="row spaced">
    <h4 class="text-center">Reviews by Source</h4>
    <div class="review-sources">
        <?php if ($loggedUser->user_setting->fb_on): ?>
            <div class="text-center"><a class="filter-icon" data-filter-value="fb"><?= $this->Html->image('embed/tab4.png', ['class' => 'facebook-logo']) ?> <?= isset($sourceCount['fb']) ? $sourceCount['fb'] : 0 ?></a></div>
        <?php endif ?>
        <?php if ($loggedUser->user_setting->gp_on): ?>
            <div class="text-center"><a class="filter-icon" data-filter-value="gp"><?= $this->Html->image('embed/tab2.png', ['class' => 'google-logo']) ?> <?= isset($sourceCount['gp']) ? $sourceCount['gp'] : 0 ?></a></div>
        <?php endif ?>
        <?php if ($loggedUser->user_setting->av_on): ?>
            <div class="text-center"><a class="filter-icon" data-filter-value="av"><?= $this->Html->image('embed/tab1.png', ['class' => 'avvo-logo']) ?> <?= isset($sourceCount['av']) ? $sourceCount['av'] : 0 ?></a></div>
        <?php endif ?>
        <?php if ($loggedUser->user_setting->yp_on): ?>
            <div class="text-center"><a class="filter-icon" data-filter-value="yp"><?= $this->Html->image('embed/tab3.png', ['class' => 'yelp-logo']) ?> <?= isset($sourceCount['yp']) ? $sourceCount['yp'] : 0 ?></a></div>
        <?php endif ?>
        <?php if ($loggedUser->user_setting->rv_on): ?>
            <div class="text-center"><a class="filter-icon" data-filter-value="rv"><?= $this->Html->image('embed/tab5.png', ['class' => 'revued-logo']) ?> <?= isset($sourceCount['rv']) ? $sourceCount['rv'] : 0 ?></a></div>
        <?php endif ?>
    </div>
</div>

<div class="row spaced">
    <div class="col-xs-6 col-xs-offset-3 text-center">
        <button class="btn btn-default" id="select-btn">Select All</button>
        <button class="btn btn-default" id="approve-btn">Approve</button>
        <button class="btn btn-default" id="archive-btn">Archive</button>
    </div>
</div>

<div class="row spaced">
    <div id="review-list"></div>
</div>

<div id="approve-dialog" title="Confirm Approval" style="display:none;">
    <br />
    <p class="text-center"><?= __("Selected reviews will now be displayed in the widget.") ?></p>
</div>

<div id="archive-dialog" title="Confirm Approval" style="display:none;">
    <br />
    <p class="text-center"><?= __("Selected reviews will now be archived.") ?></p>
</div>
