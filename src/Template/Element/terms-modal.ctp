<!-- Terms Modal -->
<div class="modal fade terms-modal" id="<?= $id ?>" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="termsModalLabel"><?= __($title) ?></h4>
            </div>
            <div class="modal-body">
                <?= $this->element($content) ?>
            </div>
            <div class="modal-footer">
                <button type="button" disabled class="btn btn-primary" data-dismiss="modal" data-enable-target="<?= $target ?>"><?= __('Agree') ?></button>
            </div>
        </div>
    </div>
</div>
