<?php $this->Html->script(['//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js', '//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/additional-methods.min.js', 'loadingoverlay.min.js', 'dashboard.js', 'bootstrap-colorpicker.min.js', 'preview.js'], ['block' => 'script']); ?>
<?php $this->Html->css(['bootstrap-colorpicker.min.css', 'preview.css'], ['block' => 'css']); ?>

<?php $this->assign('title', __('Revued Settings')); ?>

<section>
    <div class="panel panel-default">
        <div class="panel-heading"><?= __('Settings') ?><div class="pull-right"><a href="#" id="help-settings" title="Need help?"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a></div></div>
        <div class="panel-body">
            <?= $this->Form->create($loggedUser) ?>
                <?= $this->element('settings', ['user' => $loggedUser]) ?>
                <br />
                <div class="pull-right"><?= $this->Form->submit(__('Save Settings'), ['class' => 'btn btn-default']) ?></div>
            <?= $this->Form->end() ?>
        </div>
    </div>

    <?= $this->element('help-settings') ?>
</section>

<div id="revued-placeholder" data-id="<?= $loggedUser->id ?>" data-base="<?= $this->Url->build('/') ?>" data-formurl="<?= $this->Url->build('/embed/' . $loggedUser->id) ?>" data-formheight="510"></div>
<script type="text/javascript" src="<?= $this->Url->build('/js/jquery.min.js') ?>"></script>
<script type="text/javascript">var rJQuery = $.noConflict(true);</script>
<script type="text/javascript" src="<?= $this->Url->build('/js/revued_loader.js') ?>"></script>
