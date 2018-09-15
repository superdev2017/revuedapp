<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\Event;
use Cake\Datasource\EntityInterface;
use ArrayObject;
use Cake\Log\Log;
use Cake\Core\Configure;
use App\Model\Entity\User;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\HasOne $UserSetting
 * @property \Cake\ORM\Association\HasMany $UserSubscriptions
 * @property \Cake\ORM\Association\HasMany $Reviews
 */
class UsersTable extends Table
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

        $this->table('users');
        $this->displayField('username');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasOne('UserSettings')->setDependent(true);
        $this->hasMany('UserSubscriptions')->setDependent(true);
        $this->hasMany('Reviews')->setDependent(true);
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
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmpty('email');

        $validator
            ->requirePresence('username', 'create')
            ->notEmpty('username');

        $validator
            ->requirePresence('password', 'create')
            ->notEmpty('password');

        $validator
            ->integer('trial_days')
            ->allowEmpty('trial_days');

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
        //$rules->add($rules->isUnique(['email']));
        $rules->add($rules->isUnique(['username']));
        return $rules;
    }

    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        if ($entity->isNew()) {
            if ($entity->reseller_id == null) {
                $entity->status = 'trial';
                $entity->trial_days = Configure::read('REVUED_TRIAL_DURATION');
            } else {
                $entity->status = 'active';
                $entity->trial_days = 0;
            }
        }
    }

    public function afterSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        if ($entity->isNew()) {
            $event = new Event('Model.User.created', $this, ['user' => $entity]);
            $this->eventManager()->dispatch($event);
        }
    }

}
