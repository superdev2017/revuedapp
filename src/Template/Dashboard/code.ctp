<?php $this->Html->script(['clipboard.min.js'], ['block' => 'script']); ?>
<?php $this->assign('title', __('Revued Code')); ?>

<?php
    $embedCode = '
<div id="revued-placeholder" data-id="' . $this->request->session()->read('Auth.User.id') . '" data-base="' . $this->Url->build("/", true) . '" data-formurl="' . $this->Url->build("/embed/" . $this->request->session()->read('Auth.User.id'), true) . '" data-formheight="510"></div>
<script type="text/javascript" src="' . $this->Url->build("/js/jquery.min.js", true) . '"></script>
<script type="text/javascript">var rJQuery = $.noConflict(true);</script>
<script type="text/javascript" src="' . $this->Url->build("/js/revued_loader.js", true) .'"></script>
';

    $embedCode = str_replace('http:', '', $embedCode);
    $embedCode = str_replace('https:', '', $embedCode);
?>

<section>
    <div class="panel panel-default">
        <div class="panel-heading"><?= __('Embed Code') ?><div class="pull-right"><a href="#" class="copy-to-clipboard" title="Copy embed code to clipboard" data-clipboard-target="#code-text"><span class="glyphicon glyphicon-copy" aria-hidden="true"></span></a> <span>click here to copy</span></div></div>
        <div class="panel-body">
            <?= $this->Form->textarea('code', ['value' => $embedCode, 'class' => 'embed-code-textarea', 'id' => 'code-text']) ?>
        </div>
    </div>
</section>

<script>
    new Clipboard('.copy-to-clipboard');
</script>
