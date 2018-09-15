<?php  use App\Utility\Common; ?>
<?php $this->Html->script([
    'jquery-fileupload/js/load-image.all.min.js',
    'jquery-fileupload/js/canvas-to-blob.min.js',
    'jquery-fileupload/js/jquery.iframe-transport.js',
    'jquery-fileupload/js/jquery.fileupload.js',
    'jquery-fileupload/js/jquery.fileupload-process.js',
    'jquery-fileupload/js/jquery.fileupload-image.js',
    'jquery-fileupload/js/jquery.fileupload-validate.js',
    '//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js', 
    '//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/additional-methods.min.js', 
    'loadingoverlay.min.js', 
    'common.js',
    'site.js'
], ['block' => 'script']); ?>
<?php $this->assign('title', __('Register')); ?>

<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <?= $this->Form->create($user, ['id' => "$userType-register-form", 'class' => 'form']) ?>
            <fieldset>
                <legend>Personal Info</legend>
                <div class="row">
                    <div class="col-md-12">
                        <?= $this->Form->input('email', ['class' => 'form-control']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $this->Form->input('first_name', ['class' => 'form-control']) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $this->Form->input('last_name', ['class' => 'form-control']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $this->Form->input('phone', ['class' => 'form-control']) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $this->Form->input('address', ['class' => 'form-control']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <?= $this->Form->input('city', ['class' => 'form-control']) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $this->Form->input('state', ['class' => 'form-control', 'options' => Common::us_states(), 'default' => 'TX']) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $this->Form->input('zip', ['class' => 'form-control']) ?>
                    </div>
                </div>
            </fieldset>
            <br />

            <fieldset>
                <legend>Account Info</legend>
                <div class="row">
                    <div class="col-md-6">
                        <?= $this->Form->input('username', ['class' => 'form-control']) ?>
                    </div>
                    <div class="col-md-6">
                        <?php if ($userType == 'user'): ?>
                            <?= $this->Form->input('user_setting.domain', ['class' => 'form-control']) ?>
                        <?php else: ?>
                            <?= $this->Form->input('user.company', ['class' => 'form-control']) ?>
                        <?php endif ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $this->Form->input('password', ['class' => 'form-control']) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $this->Form->input('confpassword', ['type' => 'password', 'class' => 'form-control', 'label' => 'Confirm Password']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <br />
                        <?= $this->Form->checkbox('terms', ['id' => 'accept-terms', 'hiddenField' => false]) ?> <a href="#" data-toggle="modal" data-target="#termsModal"><?= __('Agree to Terms and Conditions') ?></a>
                        <?php if ($userType == 'reseller'): ?>
                            <br />
                            <?= $this->Form->checkbox('agreements', ['id' => 'accept-agreements', 'hiddenField' => false]) ?> <a href="#" data-toggle="modal" data-target="#agreementsModal"><?= __('Reseller Agreement') ?></a>
                        <?php endif ?>
                    </div>
                </div>
            </fieldset>
            <br />

            <?php if ($userType == 'user'): ?>
                <?= $this->element('billing') ?>
            <?php else: ?>
                <p class="text-center">We are currently only accepting resellers who have been personally invited to Revued. Please enter the code below given to you by the person who invited you</p>
                <div class="row">
                    <div class="col-md-12">
                        <?= $this->Form->input('reseller_code', ['class' => 'form-control']) ?>
                    </div>
                </div>
                <br />
            <?php endif ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="pull-right"><?= $this->Form->submit(__('Register'), ['class' => 'btn btn-default']) ?></div>
                </div>
            </div>
        <?= $this->Form->end() ?>
    </div>
</div>

<?= $this->element('terms-modal', ['title' => 'User Terms and Conditions', 'id' => 'termsModal', 'content' => 'user-terms', 'target' => '#accept-terms']) ?>
<?php if ($userType == 'reseller'): ?>
    <?= $this->element('terms-modal', ['title' => 'Reseller Agreement', 'id' => 'agreementsModal', 'content' => 'reseller-terms', 'target' => '#accept-agreements']) ?>
<?php endif ?>
