<div class="row spaced">
    <div class="col-md-12 text-center">
        <?= $this->Html->link(__('Add User'), '/register', ['class' => 'btn btn-primary btn-lg']) ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-12"><h1 class="page-header"><?= __('Revued Accounts') ?></h1></div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="table-responsive">
            <?php if ($users): ?>
                <table class="table table-condensed table-bordered">
                    <thead>
                        <th><?= __('Username') ?></th>
                        <th><?= __('Email') ?></th>
                        <th><?= __('Status') ?></th>
                        <th></th>
                    </thead>
                    <tbody>
                        <?php foreach($users as $row): ?>
                            <tr>
                                <td><?= $row->username ?></td>
                                <td><?= $row->email ?></td>
                                <td><?= $row->status ?></td>
                                <td>
                                    <?= $this->Html->link(__('View'), ['action' => 'view', $row->id]) ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>

                <div class="paginator">
                    <ul class="pagination">
                        <?= $this->Paginator->first('<< ' . __('first')) ?>
                        <?= $this->Paginator->prev('< ' . __('previous')) ?>
                        <?= $this->Paginator->numbers() ?>
                        <?= $this->Paginator->next(__('next') . ' >') ?>
                        <?= $this->Paginator->last(__('last') . ' >>') ?>
                    </ul>
                    <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
