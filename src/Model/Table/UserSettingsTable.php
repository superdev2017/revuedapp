<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Database\Schema\TableSchema;

/**
 * UserSettings Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\UserSetting get($primaryKey, $options = [])
 * @method \App\Model\Entity\UserSetting newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\UserSetting[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UserSetting|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UserSetting patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\UserSetting[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\UserSetting findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UserSettingsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('user_settings');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('site_name');

        $validator
            ->allowEmpty('display_mode');

        $validator
            ->integer('autopost_threshold');

        $validator
            ->allowEmpty('custom_settings');

        $validator
            ->boolean('av_on')
            ->allowEmpty('av_on');

        $validator
            ->allowEmpty('av_api_url');

        $validator
            ->allowEmpty('av_api_user');

        $validator
            ->allowEmpty('av_api_pass');

        $validator
            ->boolean('av_parser')
            ->allowEmpty('av_parser');

        $validator
            ->boolean('gp_on')
            ->allowEmpty('gp_on');

        $validator
            ->allowEmpty('gp_url');

        $validator
            ->allowEmpty('gp_api_key');

        $validator
            ->allowEmpty('gp_query_url');

        $validator
            ->allowEmpty('gp_plus_page');

        $validator
            ->allowEmpty('gp_api_key_2');

        $validator
            ->allowEmpty('gp_details_url');

        $validator
            ->allowEmpty('gp_plus_personal');

        $validator
            ->boolean('fb_on')
            ->allowEmpty('fb_on');

        $validator
            ->allowEmpty('fb_api_url');

        $validator
            ->allowEmpty('fb_api_access_token');

        $validator
            ->boolean('yp_on')
            ->allowEmpty('yp_on');

        $validator
            ->allowEmpty('yp_app_secret');

        $validator
            ->boolean('yp_parser_active')
            ->allowEmpty('yp_parser_active');

        $validator
            ->integer('yp_parser_limit')
            ->allowEmpty('yp_parser_limit');

        $validator
            ->boolean('yp_url_active')
            ->allowEmpty('yp_url_active');

        $validator
            ->allowEmpty('yp_url');

        $validator
            ->boolean('yp_url2_active')
            ->allowEmpty('yp_url2_active');

        $validator
            ->allowEmpty('yp_url2');

        $validator
            ->boolean('yp_url3_active')
            ->allowEmpty('yp_url3_active');

        $validator
            ->allowEmpty('yp_url3');

        $validator
            ->dateTime('fetched')
            ->allowEmpty('fetched');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->columnType('custom_settings', 'json');
        return $schema;
    }
}
