<?php $this->assign('title', __('Record your Review')); ?>

<?= $this->Html->script([
    'jquery-fileupload/js/load-image.all.min.js',
    'jquery-fileupload/js/canvas-to-blob.min.js',
    'jquery-fileupload/js/jquery.iframe-transport.js',
    'jquery-fileupload/js/jquery.fileupload.js',
    'jquery-fileupload/js/jquery.fileupload-process.js',
    'jquery-fileupload/js/jquery.fileupload-image.js',
    'jquery-fileupload/js/jquery.fileupload-validate.js',
    '//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js', 
    '//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/additional-methods.min.js',
    'common.js',
    'site.js'
], ['block' => 'script']); ?>

<?= $this->Html->css(['/js/jquery-fileupload/css/jquery.fileupload.css'], ['block' => 'css']); ?>

<section>
    <?= $this->Form->create(null, ['url' => '/store', 'class' => 'form', 'enctype' =>'multipart/form-data']) ?>
        <div class="row">
            <div class="col-md-12">
                <?= $this->Form->hidden('token', ['id' => 'token', 'value' => $this->request->query('token')]) ?>
                <?= $this->Form->input('rating', ['id' => 'rating', 'class' => 'form-control', 'options' => [1=>1,2=>2,3=>3,4=>4,5=>5]]) ?>

                <br /><br />

                <div id="progress" class="progress">
                    <div class="progress-bar progress-bar-success"></div>
                </div>

                <span class="btn btn-default fileinput-button">
                    <i class="fa fa-paperclip"></i>
                    <?= $this->Form->input('recording', ['id' => 'fileupload', 'accept' => 'video/*', 'value' => 'Start Recording', 'type' => 'file']) ?>
                </span>
            </div>
        </div>
    <?= $this->Form->end() ?>
</section>
