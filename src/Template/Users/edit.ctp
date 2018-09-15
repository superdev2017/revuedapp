
<?= $this->Form->create($user, ['class' => 'form']) ?>
<fieldset>
    <legend><?= __('Edit {0}', ['User']) ?></legend>
    <?php
        echo $this->Form->input('username');
        echo $this->Form->input('email');
        echo $this->Form->input('password');
    ?>
</fieldset>
<?= $this->Form->button(__("Save")) ?>
<?= $this->Form->end() ?>
