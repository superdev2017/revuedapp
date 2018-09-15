<div class="row header text-center">
    <?php if ($this->request->session()->read('Auth')): ?>
        <?= $this->Html->link($this->Html->image('logo.png', ['width' => '250']), ['controller' => 'Dashboard', 'action' => 'index'], ['escape' => false, 'title' => 'Dashboard']) ?>

        <?php if (isset($loggedUser)): ?>
            <p>Welcome <?= $loggedUser->username ?> &nbsp; <?= $this->Html->link('<span class="glyphicon glyphicon-log-out"></span>', '/logout', ['escape' => false, 'title' => 'logout']) ?></p>
            <?php if ($loggedUser->status == 'trial'): ?>
                <p>
                    Thank you for using Revued, you have <strong><?= $loggedUser->trial_days ?></strong> days remaining in your trial period.
                </p>
            <?php endif ?>
        <?php endif ?>
    <?php else: ?>
        <?= $this->Html->link($this->Html->image('logo.png', ['width' => '250']), '/', ['escape' => false]) ?>
    <?php endif ?>

    <p><?= $this->Flash->render() ?></p>
    <br />
</div>
