<?= $message ?>

<br />
<?= $this->Html->link(__('Request Review'), $this->Url->build('/record?token=' . $token, ['fullBase' => true])); ?>
