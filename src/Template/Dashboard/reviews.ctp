<?php
    function print_star_rating( $star ) {
        for ( $i = 1; $i <= 5; $i ++ ) {
            if ( $star >= $i ) echo '<span class="active-star">☆</span>';
            else echo '<span>☆</span>'; 
        }
    }
?>

<div class="row">
    <div class="col-xs-2 col-xs-offset-5">
        <?= __('Per Page') ?>
        <select class="form-control limit ">
            <option value="10"  <?php if ($limit==10): ?>  selected<?php endif ?>>10</option>
            <option value="20"  <?php if ($limit==20): ?>  selected<?php endif ?>>20</option>
            <option value="50"  <?php if ($limit==50): ?>  selected<?php endif ?>>50</option>
            <option value="100" <?php if ($limit==100): ?> selected<?php endif ?>>100</option>
        </select>
    </div>
</div>

<div class="row paginator text-center">
    <ul class="pagination">
        <?= $this->Paginator->first('<< ' . __('first')) ?>
        <?= $this->Paginator->prev('< ' . __('previous')) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__('next') . ' >') ?>
        <?= $this->Paginator->last(__('last') . ' >>') ?>
        <li>
    </ul>
    <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
</div>

<?php foreach($reviews as $review): ?>
    <?php
        switch ($review->source) {
            case 'av': $logo = 'tab1.png'; break;
            case 'fb': $logo = 'tab4.png'; break;
            case 'gp': $logo = 'tab2.png'; break;
            case 'yp': $logo = 'tab3.png'; break;
            case 'rv': $logo = 'tab5.png'; break;
        }
    ?>

    <div class="row">
        <div class="col-xs-1 col-xs-offset-1"><input type="checkbox" data-id="<?= $review->id ?>" /></div>
        <div class="col-xs-9">
            <div class="list-block">
                <h5 class="author"><?= $review->author ?></h5>
                <h5 class="date"><?= $review->date->format('M d Y') ?></h5>
                <?= $this->Html->image("embed/$logo", ['class' => 'source']) ?>
                <div class="stars">
                    <?php print_star_rating( $review->rating ) ?>
                </div>
                <p><?= $review->body ?></p>
                <br />
                <p><?= __('Status') ?>: <?= $review->status ?></p>

                <?php if ( ! empty($review->review_assets)): ?>
                    <?php
                        $review_assets = [];
                        $poster = null;
                        if (count($review->review_assets) > 1) {
                            foreach($review->review_assets as $source) {
                                if ($source['type'] == 'jpg') {
                                    $poster = $source['src'];
                                    continue;
                                }

                                $review_assets[] = $source;
                            }
                        }
                    ?>
                    <!-- Revued Video-->
                    <video id="vid-<?= $review->id ?>" class="video-js" controls preload="auto" width="120" height="100" poster="<?= $this->Url->build($poster ?: '/img/embed/thumbnail.jpg') ?>" data-setup='{"autoplay": false}'>
                        <?php foreach($review_assets as $source): ?>
                            <source src="<?= $source['src'] ?>" type="video/<?= $source['type'] ?>">
                        <?php endforeach ?>
                    </video>
                <?php endif ?>
            </div>
        </div>
    </div>
<?php endforeach ?>

<div class="paginator text-center">
    <ul class="pagination">
        <?= $this->Paginator->first('<< ' . __('first')) ?>
        <?= $this->Paginator->prev('< ' . __('previous')) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__('next') . ' >') ?>
        <?= $this->Paginator->last(__('last') . ' >>') ?>
    </ul>
    <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
</div>

<?php if (count($reviews) == 0): ?>
    <div class="row">
        <h5 class="text-center">No reviews available.</h5>
    </div>
<?php endif ?>
