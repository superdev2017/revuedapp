<?php use Cake\Core\Configure; ?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= $this->Html->meta('icon') ?>

    <title><?= $this->fetch('title') ?></title>

    <?= $this->Html->css(['bootstrap.min.css', '../js/jquery-ui/jquery-ui.min.css', 'revued-site.css']) ?>
    <?= $this->Html->script(['jquery.min.js', 'bootstrap.min.js', 'jquery-ui/jquery-ui.min.js', 'common.js']) ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>

    <script>
        var custom_config = { "baseUrl": "<?= $this->Url->build('/') ?>"
            , 'yelpAccessToken' : "<?= Configure::read('YelpApiKey') ?>"
            , "fbAppId" :  "<?= Configure::read('Facebook.appId') ?>"
            , "fbLoginUrl": "<?= $this->Url->build(["controller" => "Auth", "action" => "loginWithFacebook"])?>"
            , "fbFetchPageUrl": "<?= $this->Url->build(["controller" => "Dashboard", "action" => "requestFacebookPages"]) ?>"
            , "fbFetchRatingUrl": "<?= $this->Url->build(["controller" => "Dashboard", "action" => "requestPage"]) ?>"
            , "yelpSearchUrl": "<?= $this->Url->build(["controller" => "Dashboard", "action" => "requestYelpSearch"]) ?>"
        };
    </script>
    <?= $this->Html->script('facebook.js') ?>
    <?= $this->fetch('script') ?>

    <!-- Default Layout -->
</head>
<body>
    <div class="container">
        <?= $this->element('header') ?>
        <?= $this->fetch('content') ?>
        <?= $this->element('footer') ?>
    </div>

    <?= $this->fetch('bottomScripts') ?>
</body>
</html>
