<?php  use App\Utility\Common; ?>
<fieldset>
    <legend><?= __('Card Information') ?></legend>
    <?php if ( ! $this->request->session()->read('Auth')): ?>
        <p><?= __("Your card won't be charged until your trial period expires.") ?></p>
    <?php endif ?>
    <div class="row">
        <div class="col-md-6">
            <?= $this->Form->input('billing.card_number', ['class' => 'form-control', 'label' => 'Card Number']) ?>
        </div>
        <div class="col-md-4">
            <?= $this->Form->input('billing.expiration_date', ['class' => 'form-control cc-date', 'label' => 'Expiration Date']) ?>
        </div>
        <div class="col-md-2">
            <?= $this->Form->input('billing.cvc', ['class' => 'form-control', 'label' => 'CVC']) ?>
        </div>
    </div>
</fieldset>
<br />

<fieldset>
    <legend><?= __('Billing Address') ?></legend>
    <div class="row">
        <div class="col-md-6">
            <?= $this->Form->input('billing.first_name', ['class' => 'form-control', 'label' => 'First Name']) ?>
        </div>
        <div class="col-md-6">
            <?= $this->Form->input('billing.last_name', ['class' => 'form-control', 'label' => 'Last Name']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $this->Form->input('billing.address', ['class' => 'form-control', 'label' => 'Street Address']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $this->Form->input('billing.city', ['class' => 'form-control', 'label' => 'City']) ?>
        </div>
        <div class="col-md-3">
            <?= $this->Form->input('billing.state', ['class' => 'form-control', 'label' => 'State / Region', 'required' => true, 'options' => Common::us_states(), 'default' => 'TX']) ?>
        </div>
        <div class="col-md-3">
            <?= $this->Form->input('billing.zip', ['class' => 'form-control', 'label' => 'Zip', 'maxlength' => 5]) ?>
        </div>
    </div>
</fieldset>
<br />
