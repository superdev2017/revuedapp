<?php $this->Html->script(['//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js', '//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/additional-methods.min.js'], ['block' => 'script']); ?>
<?php $this->assign('title', __('Billing')); ?>

<section>
    <div class="panel panel-default">
        <?php if ($subscription): ?>
            <div class="panel-heading"><?= __('My Subscription') ?></div>
            <div class="panel-body">
                <div class="col-xs-12 col-md-6"><strong><?= __('Status') ?></strong>: <?= $subscription->status ?></div>
                <div class="col-xs-12 col-md-6"><strong><?= __('Payment Gateway') ?></strong>: <?= $subscription->gateway ?></div>
                <div class="col-xs-12 col-md-6"><strong><?= __('Subscription ID') ?></strong>: <?= $subscription->subscription_id ?></div>
                <div class="col-xs-12 col-md-6"><strong><?= __('Created') ?></strong>: <?= $subscription->created ?></div>
            </div>
        <?php else: ?>
            <div class="panel-heading"><?= __('Create Subscription') ?></div>
            <div class="panel-body">
                <?= $this->Form->create(null, ['id' => 'subscription-form', 'class' => 'form', 'autocomplete' => 'off']) ?>
                    <div class="tabs">
                        <ul>
                            <li><a href="#tabs-1"><?= __('Credit Card / Debit') ?></a></li>
                        </ul>
                        <div id="tabs-1">
                            <?= $this->element('billing') ?>
                        </div>
                    </div>
                    <br />
                    <div class="pull-right"><?= $this->Form->submit(__('Create Subscription'), ['class' => 'btn btn-default']) ?></div>
                <?= $this->Form->end() ?>
            </div>
        <?php endif ?>
    </div>
</section>
