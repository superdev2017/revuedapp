<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <?= $this->Form->create($user) ?>
            <?php //$this->Form->hidden('role'); ?>
            <div class="row">
                <?= $this->Form->input('role', ['class' => 'form-control', 'required' => true, 'options' => ['admin' => 'admin', 'user' => 'user', 'reseller' => 'reseller']]) ?>
            </div>
            <div class="row">
                <?= $this->Form->input('username', ['class' => 'form-control', 'required' => true]) ?>
            </div>
            <div class="row">
                <?= $this->Form->input('password', ['class' => 'form-control', 'required' => true]) ?>
            </div>
            <div class="row">
                <?= $this->Form->input('email', ['class' => 'form-control', 'required' => true]) ?>
            </div>
            <div class="row">
                <?= $this->Form->input('status', ['class' => 'form-control', 'required' => true]) ?>
            </div>
            <div class="row">
                <?= $this->Form->input('trial_days', ['class' => 'form-control', 'required' => true]) ?>
            </div>
            <br />
            <div class="row">
                <div class="pull-right">
                    <?= $this->Form->submit(__('Update'), ['class' => 'btn btn-default']) ?>
                </div>
            </div>
        <?= $this->Form->end() ?>
    </div>
</div>
