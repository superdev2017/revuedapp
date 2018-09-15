<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __('Add Code') ?></div>
            <div class="panel-body">
                <?= $this->Form->create($resellerCode, ['class' => 'form']) ?>
                <?= $this->Form->input('code', ['class' => 'form-control']) ?>
                <?= $this->Form->input('name', ['class' => 'form-control']) ?>
                <?= $this->Form->input('price', ['class' => 'form-control']) ?>
                <br />
                <?= $this->Form->submit(__("Add"), ['class' => 'btn btn-primary']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="table-responsive">
            <?php if ($resellerCodes): ?>
                <table class="table table-condensed table-bordered">
                    <thead>
                        <th><?= __('Id') ?></th>
                        <th><?= __('Code') ?></th>
                        <th><?= __('Name') ?></th>
                        <th><?= __('Price') ?></th>
                        <th><?= __('Created') ?></th>
                        <th></th>
                    </thead>
                    <tbody>
                        <?php foreach($resellerCodes as $row): ?>
                            <tr>
                                <td><?= $row->id ?></td>
                                <td><?= $row->code ?></td>
                                <td><?= $row->name ?></td>
                                <td><?= $row->price ?></td>
                                <td><?= $row->created ?></td>
                                <td>
                                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete_reseller_code', $row->id], ['confirm' => __('Are you sure you want to delete # {0}?', $row->id)]) ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            <?php endif ?>
        </div>
    </div>
</div>

