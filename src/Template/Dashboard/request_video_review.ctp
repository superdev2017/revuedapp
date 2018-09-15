<?php $this->assign('title', __('Request Review')); ?>

<section>
    <div class="panel panel-default">
        <div class="panel-heading"><?= __('Request Video Review') ?></div>
        <div class="panel-body">
            <?= $this->Form->create($form) ?>
                <?= $this->Form->input('email', ['class' => 'form-control']) ?><br />

                <label>Message</label>
                <?= $this->Form->textarea('message', ['class' => 'form-control embed-code-textarea']) ?>

                <br />
                <div class="pull-right"><?= $this->Form->submit(__('Send'), ['class' => 'btn btn-default']) ?></div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</section>
