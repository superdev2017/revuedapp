<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UserSetting Entity
 *
 * @property int $id
 * @property int $user_id
 * @property string $site_name
 * @property string $display_mode
 * @property int $autopost_threshold
 * @property string $custom_settings
 * @property bool $av_on
 * @property string $av_api_url
 * @property string $av_api_id
 * @property string $av_api_user
 * @property string $av_api_pass
 * @property bool $av_parser
 * @property bool $gp_on
 * @property string $gp_url
 * @property string $gp_api_key
 * @property string $gp_query_url
 * @property string $gp_place_id
 * @property string $gp_reference
 * @property string $gp_plus_page
 * @property string $gp_api_key_2
 * @property string $gp_details_url
 * @property string $gp_plus_personal
 * @property string $gp_review_url
 * @property bool $fb_on
 * @property string $fb_api_url
 * @property string $fb_page_id
 * @property string $fb_page_title
 * @property string $fb_api_access_token
 * @property bool $yp_on
 * @property string $yp_app_id
 * @property string $yp_app_secret
 * @property string $yp_business_id
 * @property bool $yp_parser_active
 * @property int $yp_parser_limit
 * @property bool $yp_url_active
 * @property string $yp_url
 * @property bool $yp_url2_active
 * @property string $yp_url2
 * @property bool $yp_url3_active
 * @property string $yp_url3
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $fetched
 *
 * @property \App\Model\Entity\User $user
 */
class UserSetting extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];

    protected function _setCustomSettings($settings)
    {
        return $settings;
    }
}
