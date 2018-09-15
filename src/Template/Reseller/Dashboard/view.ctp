<?php $this->Html->script(['//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js', '//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/additional-methods.min.js', 'loadingoverlay.min.js', 'clipboard.min.js', 'dashboard.js', 'bootstrap-colorpicker.min.js', 'preview.js'], ['block' => 'script']); ?>
<?php $this->Html->css(['bootstrap-colorpicker.min.css', 'preview.css'], ['block' => 'css']); ?>

<?php  use App\Utility\Common; ?>

<section>
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
</section>

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
                <?= $this->Form->create(null, ['id' => 'subscription-form', 'url' => ['action' => 'userBilling'], 'class' => 'form', 'autocomplete' => 'off']) ?>
                    <?= $this->Form->hidden('billing.user_id', ['value' => $user->id]) ?>
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


<section>
    <div class="panel panel-default">
        <div class="panel-heading"><?= __('Settings') ?><div class="pull-right"><a href="#" id="help-settings" title="Need help?"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a></div></div>
        <div class="panel-body">
            <?= $this->Form->create($user, ['url' => ['action' => 'userSettings']]) ?>
                <?= $this->Form->hidden('user_id', ['value' => $user->id]) ?>
                <?= $this->element('settings') ?>
                <br />
                <div class="pull-right"><?= $this->Form->submit(__('Save Settings'), ['class' => 'btn btn-default']) ?></div>
            <?= $this->Form->end() ?>
        </div>
    </div>

    <?= $this->element('help-settings') ?>
</section>

<?php
    $embedCode = '
<div id="revued-placeholder" data-id="' . $user->id . '" data-base="' . $this->Url->build("/", true) . '" data-formurl="' . $this->Url->build("/embed/" . $user->id, true) . '" data-formheight="510"></div>
<script type="text/javascript" src="' . $this->Url->build("/js/jquery.min.js", true) . '"></script>
<script type="text/javascript">var rJQuery = $.noConflict(true);</script>
<script type="text/javascript" src="' . $this->Url->build("/js/revued_loader.js", true) .'"></script>
';

    $embedCode = str_replace('http:', '', $embedCode);
    $embedCode = str_replace('https:', '', $embedCode);
?>

<section>
    <div class="panel panel-default">
        <div class="panel-heading"><?= __('Embed Code') ?><div class="pull-right"><a href="#" class="copy-to-clipboard" title="Copy embed code to clipboard" data-clipboard-target="#code-text"><span class="glyphicon glyphicon-copy" aria-hidden="true"></span></a></div></div>
        <div class="panel-body">
            <?= $this->Form->textarea('code', ['value' => $embedCode, 'class' => 'embed-code-textarea', 'id' => 'code-text']) ?>
        </div>
    </div>
</section>

<script>
    new Clipboard('.copy-to-clipboard');
</script>


<div id="revued-placeholder" data-id="<?= $user->id ?>" data-base="<?= $this->Url->build('/') ?>" data-formurl="<?= $this->Url->build('/embed/' . $user->id) ?>" data-formheight="510"></div>
<script type="text/javascript" src="<?= $this->Url->build('/js/jquery.min.js') ?>"></script>
<script type="text/javascript">var rJQuery = $.noConflict(true);</script>
<script type="text/javascript" src="<?= $this->Url->build('/js/revued_loader.js') ?>"></script>
