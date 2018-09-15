<div class="row header text-center">
    <?= $this->Html->link($this->Html->image('logo.png', ['width' => '250']), ['controller' => 'dashboard'], ['escape' => false, 'title' => 'Dashboard']) ?>

    <p>Welcome <?= $loggedUser->username ?> &nbsp; <?= $this->Html->link('<span class="glyphicon glyphicon-log-out"></span>', '/logout', ['escape' => false, 'title' => 'logout']) ?></p>

    <p><?= $this->Flash->render() ?></p>
    <br />
</div>
