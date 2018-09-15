<?php use Cake\Core\Configure; ?>
<?= $this->Html->script(['google.js'], ['block' => 'script']) ?>
<?= $this->Html->script(['yelp.js'], ['block' => 'script']) ?>
<?= $this->Html->script(['drag-arrange.min'], ['block' => 'script']) ?>
<?php $tab_names = array("6" => "Revued", "3" => "Google +", "4" => "Facebook", "5" => "Yelp", "2" => "Avvo")?>
<div class="tabs">
    <ul>
        <li><a href="#tabs-1">General</a></li>
        <?php if (!isset($user->user_setting->custom_settings['tab_orders']) || $user->user_setting->custom_settings['tab_orders'] == ''):
            $tab_orders = ["6", "3", "4", "5", "2"];
        else:
            $tab_orders = explode(',', $user->user_setting->custom_settings['tab_orders']);
        endif?>
        <?php foreach ($tab_orders as $order) :?>
            <li><a href="#tabs-<?php echo $order?>"><?php echo $tab_names[$order]?></a></li>
        <?php endforeach;?>
    </ul>
    <div id="tabs-1">

        <?= $this->Form->input('user_setting.site_name', ['class' => 'form-control', 'label' => 'Title']) ?>
        <br />
        <label>Type</label>
        <?= $this->Form->select('user_setting.display_mode', ['embed' => 'Embed', 'float' => 'Float'], ['class' => 'form-control']) ?>
        <br />
        <label>Auto Approve Threshold</label>
        <?= $this->Form->select('user_setting.autopost_threshold', ['5' => '5', '4' => '4', '3' => '3', '2' => '2', '1' => '1', '0' => 'None'], ['class' => 'form-control']) ?>
        <br />

        <label>Tab's Order</label>
        <div id="tab-order-container">
            <?php foreach ($tab_orders as $order) :?>
                <div class="draggable-element" data-key="<?php echo $order?>"><?php echo $tab_names[$order]?></div>
            <?php endforeach;?>
        </div>
        <?= $this->Form->input('user_setting.custom_settings.tab_orders', ['id' => 'tab_orders', 'type' => 'hidden', 'label' => false]) ?>
        <br />

        <label>Button Size</label><br />
        <div class="row">
            <div class="col-md-2">
                <input type="radio" name="user_setting[custom_settings][btn_size]" value="sm" <?php if (isset($user->user_setting->custom_settings['btn_size']) and $user->user_setting->custom_settings['btn_size'] == 'sm'): ?> checked<?php endif ?> /> Small
                <div class="revued-btn-container revued-btn-sm"></div>
            </div>
            <div class="col-md-4">
                <input type="radio" name="user_setting[custom_settings][btn_size]" value="md" <?php if (isset($user->user_setting->custom_settings['btn_size']) and $user->user_setting->custom_settings['btn_size'] == 'md'): ?> checked<?php endif ?> /> Medium
                <div class="revued-btn-container revued-btn-md"></div>
            </div>
            <div class="col-md-6">
                <input type="radio" name="user_setting[custom_settings][btn_size]" value="lg" <?php if ( ! isset($user->user_setting->custom_settings['btn_size']) or (isset($user->user_setting->custom_settings['btn_size']) and $user->user_setting->custom_settings['btn_size'] == 'lg')): ?> checked<?php endif ?> /> Large
                <div class="revued-btn-container revued-btn-lg"></div>
            </div>
        </div>
        <br />
        <br />

        <div class="row">
            <div class="col-sm-6">
                <label>Header Color</label>
                <?= $this->Form->input('user_setting.custom_settings.header_color', ['id' => 'header-color', 'type' => 'color', 'label' => false]) ?>
            </div>

            <div class="col-sm-6">
                <label>Footer Color</label>
                <?= $this->Form->input('user_setting.custom_settings.footer_color', ['id' => 'footer-color', 'type' => 'color', 'label' => false]) ?>
            </div>
        </div>
        <br />
        <div class="row">
            <div class="col-sm-6">
                <label>Close Color</label>
                <?= $this->Form->input('user_setting.custom_settings.close_color', ['id' => 'close-color', 'type' => 'color', 'label' => false]) ?>
            </div>

            <div class="col-sm-6">
                <label>Background Color</label>
                <?= $this->Form->input('user_setting.custom_settings.background_color', ['id' => 'background-color', 'type' => 'color', 'label' => false]) ?>
            </div>
        </div>
    </div>
    <div id="tabs-2">
        <?= $this->Form->input('user_setting.av_on', ['class' => 'form-control', 'label' => 'Show?']) ?>
        <?= $this->Form->input('user_setting.av_api_url', ['class' => 'form-control', 'label' => 'API URL']) ?>
        <?= $this->Form->input('user_setting.av_api_id', ['class' => 'form-control', 'label' => 'API ID', 'type' => 'text']) ?>
        <?= $this->Form->input('user_setting.av_api_user', ['class' => 'form-control', 'label' => 'API User']) ?>
        <?= $this->Form->input('user_setting.av_api_pass', ['class' => 'form-control', 'label' => 'API Password']) ?>
        <?= $this->Form->input('user_setting.av_parser', ['class' => 'form-control', 'label' => 'Parser']) ?>
    </div>
    <div id="tabs-3">
        <?= $this->Form->input('user_setting.gp_on', ['class' => 'form-control', 'label' => 'Show?']) ?>
        <?= $this->Form->input('user_setting.gp_url', ['class' => 'form-control', 'placeholder' => 'Enter a location', 'label' => 'Place Name']) ?>
        <?= $this->Form->input('user_setting.gp_api_key', ['class' => 'form-control', 'label' => 'API Key1', 'type' => 'hidden']) ?>
        <?= $this->Form->input('user_setting.gp_query_url', ['value'=>'https://maps.googleapis.com/maps/api/place/textsearch/xml?query=%s&sensor=true&key=%s', 'class' => 'form-control', 'label' => 'Query URL', 'type' => 'hidden']) ?>
        <?= $this->Form->input('user_setting.gp_place_id', ['class' => 'form-control', 'label' => 'Place ID', 'type'=>'hidden']) ?>
        <?= $this->Form->input('user_setting.gp_details_url', ['value' => 'https://maps.googleapis.com/maps/api/place/details/xml?reference=%s&sensor=true&key=%s', 'class' => 'form-control', 'label' => 'Details URL', 'type' => 'hidden']) ?>
        <?= $this->Form->input('user_setting.gp_reference', ['class' => 'form-control', 'label' => 'Place Reference', 'type' => 'hidden']) ?>
        <div id="map-wrapper"></div>
    </div>
    <div id="tabs-4">
        <?= $this->Form->input('user_setting.fb_on', ['class' => 'form-control', 'label' => 'Show?']) ?>
        <?= $this->Form->input('user_setting.fb_api_url', ['class' => 'form-control', 'label' => 'API URL', 'type' => 'hidden']) ?>
        <?= $this->Form->input('user_setting.fb_page_title', ['class' => 'form-control', 'label' => 'Page Title', 'type' => 'hidden']) ?>
        <?= $this->Form->input('fb_email', ['class' => 'form-control', 'label' => 'Facebook Email', 'type' => 'text', 'type' => 'hidden']) ?>
        <div>
            <label>Page Title</label>
            <select id="pages_select" class="form-control">
                <?php if($user->user_setting->fb_page_title != null) :?>
                    <option value="<?php echo $user->user_setting->fb_page_id ?>"><?php echo $user->user_setting->fb_page_title ?></option>
                <?php endif; ?>
            </select>
        </div>
        <?= $this->Form->input('user_setting.fb_page_id', ['class' => 'form-control', 'label' => 'Page ID', 'type' => 'text', 'type' => 'hidden']) ?>
        <?= $this->Form->input('user_setting.fb_api_access_token', ['class' => 'form-control', 'label' => 'API Access Token', 'type'=> 'hidden']) ?>
        <div class="row text-center">
            <br />
            <hr />

            <div id="fb_link_facebook" class="fb-login-button" data-max-rows="1"
                 data-size="large" data-button-type="login_with"
                 data-show-faces="false" data-auto-logout-link="false"
                 data-scope="public_profile, email, manage_pages"
                 data-use-continue-as="false">Link Facebook Account</div>
        </div>
    </div>
    <div id="tabs-5">
        <?= $this->Form->input('user_setting.yp_on', ['class' => 'form-control', 'label' => 'Show?']) ?>
        <?= $this->Form->input('user_setting.yp_app_id', ['class' => 'form-control', 'label' => 'App ID', 'type' => 'hidden']) ?>
        <?= $this->Form->input('user_setting.yp_app_secret', ['class' => 'form-control', 'label' => 'App Secret', 'type' => 'hidden']) ?>
        <div>
            <label for="user-setting-yp-business-id">Business ID</label>
            <div class="col-sm-12 input-group">
            <?= $this->Form->input('user_setting.yp_business_id', ['class' => 'form-control'
                , 'label' => false, 'type' => 'text'
                , 'templates' => ['inputContainer' => '{{content}}']
            ]) ?>
                <div class="input-group-btn">
                    <button class="btn btn-default" data-toggle="modal" data-target="#yelpModal" type="button" style="font-size: 14px;">Search</button>
                </div>
            </div>
        </div>
        <?= $this->Form->input('user_setting.yp_parser_active', ['class' => 'form-control', 'label' => 'Parser', 'type' => 'hidden']) ?>
        <?= $this->Form->input('user_setting.yp_parser_limit', ['class' => 'form-control', 'label' => 'Parser Limit']) ?>
        <?= $this->Form->input('user_setting.yp_url_active', ['class' => 'form-control', 'label' => 'URL Active', 'type' => 'hidden']) ?>
        <?= $this->Form->input('user_setting.yp_url', ['class' => 'form-control', 'label' => 'URL', 'type' => 'hidden']) ?>
        <?= $this->Form->input('user_setting.yp_url2_active', ['class' => 'form-control', 'label' => 'URL2 Active', 'type' => 'hidden']) ?>
        <?= $this->Form->input('user_setting.yp_url2', ['class' => 'form-control', 'label' => 'URL2', 'type' => 'hidden']) ?>
        <?= $this->Form->input('user_setting.yp_url3_active', ['class' => 'form-control', 'label' => 'URL3 Active', 'type' => 'hidden']) ?>
        <?= $this->Form->input('user_setting.yp_url3', ['class' => 'form-control', 'label' => 'URL3', 'type' => 'hidden']) ?>
    </div>
    <div id="tabs-6">
        <?= $this->Form->input('user_setting.rv_on', ['class' => 'form-control', 'label' => 'Show?']) ?>
    </div>
</div>

<div class="map-area" style="width: 400px; height: 400px; position: absolute; left: -1111px;">
    <div id="map" style="width: 400px; height: 400px;"></div>
    <div id="infowindow-content">
        <img src="" width="16" height="16" id="place-icon">
        <span id="place-name"  class="title"></span><br>
        <span id="place-address"></span>
    </div>
</div>
<!-- Replace the value of the key parameter with your own API key. -->
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo Configure::read('GoogleApiKey')?>&libraries=places&callback=initMap"
        async defer></script>

<?= $this->element('yelp-modal', ['title' => 'Select Your Business', 'id' => 'yelpModal', 'target' => '#user-setting-yp-business-id']) ?>
