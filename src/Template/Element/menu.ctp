<div class="row spaced">
    <div class="col-md-12 text-center">
        <div class="btn-group" role="group">
            <?= $this->Html->link(__('Your Reviews'), ['controller' => 'Dashboard', 'action' => ''], ['class' => 'btn btn-primary ']) ?>

            <?php if ( ! $loggedUser->reseller_id): ?>
                <?= $this->Html->link(__('Settings'), ['controller' => 'Dashboard', 'action' => 'settings'], ['class' => 'btn btn-primary ']) ?>
                <?= $this->Html->link(__('Billing'), ['controller' => 'Dashboard', 'action' => 'billing'], ['class' => 'btn btn-primary ']) ?>
            <?php endif ?>
            <?= $this->Html->link(__('Account'), ['controller' => 'Dashboard', 'action' => 'account'], ['class' => 'btn btn-primary ']) ?>

            <?= $this->Html->link(__('Code'), ['controller' => 'Dashboard', 'action' => 'code'], ['class' => 'btn btn-primary ']) ?>
            <?php if ($loggedUser->role == 'reseller'): ?>
                <?= $this->Html->link(__('Users'), ['controller' => 'Dashboard', 'action' => 'listing'], ['class' => 'btn btn-primary ']) ?>
            <?php endif ?>

            <?= $this->Html->link(__('Ask For a Review'), ['controller' => 'Dashboard', 'action' => 'requestReview'], ['class' => 'btn btn-primary ']) ?>
            <?= $this->Form->postLink(__('Refresh Reviews'), ['controller' => 'work', 'action' => 'manualFetch', 'prefix' => false], ['confirm' => __('Are you sure you want to manually fetch reviews?'), 'class' => 'btn btn-primary']) ?>
        </div>
    </div>
</div>
