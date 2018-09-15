<div class="panel panel-default">
    <div class="panel-heading"><?= __('Account') ?></div>
    <div class="panel-body">
        <div class="col-xs-12 col-md-6"><strong><?= __('Status') ?></strong>: <?= $user->status ?></div>
        <div class="col-xs-12 col-md-6"><strong><?= __('Created') ?></strong>: <?= $user->created ?></div>
        <div class="col-xs-12 col-md-6"><strong><?= __('Username') ?></strong>: <?= $user->username ?></div>
        <div class="col-xs-12 col-md-6"><strong><?= __('Email') ?></strong>: <?= $user->email ?></div>
        <div class="col-xs-12 col-md-6"><strong><?= __('First Name') ?></strong>: <?= $user->firt_name ?></div>
        <div class="col-xs-12 col-md-6"><strong><?= __('Last Name') ?></strong>: <?= $user->last_name ?></div>
        <div class="col-xs-12 col-md-6"><strong><?= __('Phone') ?></strong>: <?= $user->phone ?></div>
        <div class="col-xs-12 col-md-6"><strong><?= __('Address') ?></strong>: <?= $user->address ?></div>
        <div class="col-xs-12 col-md-6"><strong><?= __('State') ?></strong>: <?= $user->state ?></div>
        <div class="col-xs-12 col-md-6"><strong><?= __('Zip') ?></strong>: <?= $user->zip ?></div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><?= __('Subscription') ?></div>
    <div class="panel-body">
        <?php if ($subscription): ?>
            <div class="col-xs-12 col-md-6"><strong><?= __('Status') ?></strong>: <?= $subscription->status ?></div>
            <div class="col-xs-12 col-md-6"><strong><?= __('Payment Gateway') ?></strong>: <?= $subscription->gateway ?></div>
            <div class="col-xs-12 col-md-6"><strong><?= __('Subscription ID') ?></strong>: <?= $subscription->subscription_id ?></div>
            <div class="col-xs-12 col-md-6"><strong><?= __('Created') ?></strong>: <?= $subscription->created ?></div>
        <?php else: ?>
            <?= __('No subscription available') ?>
        <?php endif ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __('Bulk Upload') ?></div>
            <div class="panel-body">
                <?= $this->Form->create(null, ['url' => ['action' => 'bulkUpload'], 'class' => 'form', 'type' => 'file']) ?>
                <?= $this->Form->hidden('user_id', ['value' => $user->id]) ?>
                <?= $this->Form->input('source', ['options' => ['gp' => 'Google', 'fb' => 'Facebook', 'av' => 'Avvo', 'yp' => 'Yelp'], 'class' => 'form-control']) ?>
                <br />
                <?= $this->Form->control('upload_file', ['type' => 'file']) ?>
                <br />
                <?= $this->Form->submit(__("Process"), ['class' => 'btn btn-primary']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

