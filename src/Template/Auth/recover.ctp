<?php $this->assign('title', __('Recover Account')); ?>

<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="row">
            <p><?= __('A temporary password will be sent to you by email.') ?></p>
        </div>
        <?= $this->Form->create() ?>
            <div class="row">
                <?= $this->Form->input('username', ['class' => 'form-control', 'label' => false, 'placeholder' => __('username')]) ?>
            </div>

            <div class="row">
                <div class="pull-right">
                    <br />
                    <?= $this->Form->button(__('Reset Password'), ['class' => 'btn btn-danger']) ?>
                </div>
            </div>
        <?= $this->Form->end() ?>
    </div>
</div>
