<!-- Terms Modal -->
<div class="modal fade" id="<?= $id ?>" tabindex="-1" role="dialog" aria-labelledby="yelpModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="termsModalLabel"><?= __($title) ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <label for="ex1">State</label>
                        <input class="form-control" id="user-setting-yp-state" type="text">
                    </div>
                    <div class="col-sm-6">
                        <label for="ex2">City</label>
                        <input class="form-control" id="user-setting-yp-city" type="text">
                    </div>
                    <div class="col-sm-12">
                        <label for="ex3">Business Terms</label>
                        <input class="form-control" id="user-setting-yp-business-name" type="text">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary yelp-modal-confirm" data-dismiss="modal"><?= __('Confirm') ?></button>
            </div>
        </div>
    </div>
</div>
