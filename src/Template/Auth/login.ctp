<?php $this->assign('title', __('Revued Login')); ?>
<!-- Facebook login initialize -->
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <?= $this->Form->create() ?>
            <div class="row">
                <?= $this->Form->input('username', ['class' => 'form-control']) ?>
                <br />
            </div>
            <div class="row">
                <?= $this->Form->input('password', ['class' => 'form-control']) ?>
                <br />
                <?= $this->Html->link(__('Forgot Password?'), '/forgot-password', ['class' => 'pull-left']) ?>
            </div>
            <div class="row">
                <div class="pull-right">
                    <?= $this->Form->button(__('Login'), ['class' => 'btn btn-success']) ?>
                </div>
            </div>

            <!--
            <div class="row text-center">
                <br />
                <hr class="line-or" />
                <br />
                <div class="fb-login-button" data-max-rows="1" id="fb_login_button"
                     data-size="large" data-button-type="login_with"
                     data-show-faces="false" data-auto-logout-link="false"
                     data-scope="public_profile, email, manage_pages"
                     data-use-continue-as="false"></div>
            </div>
            -->
        <?= $this->Form->end() ?>
    </div>
</div>
