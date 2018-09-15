<?php $this->Html->script(['//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js', '//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/additional-methods.min.js', 'site.js'], ['block' => 'script']); ?>
<?php $this->assign('title', __('My Account')); ?>

<section>
    <div class="panel panel-default">
        <div class="panel-heading"><?= __('My Account') ?></div>
        <div class="panel-body">
            <?= $this->Form->create($loggedUser, ['id' => 'my-account-form', 'class' => 'form', 'autocomplete' => 'off']) ?>
                <div class="row">
                    <div class="col-md-12">
                        <?= $this->Form->input('email', ['class' => 'form-control']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?= $this->Form->input('password', ['class' => 'form-control']) ?>
                    </div>
                </div>
                <br />
                <div class="pull-right"><?= $this->Form->submit(__('Update'), ['class' => 'btn btn-default']) ?></div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</section>
