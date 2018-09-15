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

/**
 * ReviewFeedbacks Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Reviews
 * @property \Cake\ORM\Association\BelongsTo $Collaborators
 *
 * @method \App\Model\Entity\ReviewFeedback get($primaryKey, $options = [])
 * @method \App\Model\Entity\ReviewFeedback newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ReviewFeedback[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ReviewFeedback|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ReviewFeedback patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ReviewFeedback[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ReviewFeedback findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ReviewFeedbacksTable extends Table
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

        $this->setTable('review_feedbacks');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Reviews', [
            'foreignKey' => 'review_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('Collaborators', [
            'foreignKey' => 'collaborator_id',
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
        $rules->add($rules->existsIn(['review_id'], 'Reviews'));
        $rules->add($rules->existsIn(['collaborator_id'], 'Collaborators'));

        return $rules;
    }

    public function afterSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        $event = new Event('Model.ReviewFeedback.given', $this, ['reviewFeedback' => $entity]);
        $this->eventManager()->dispatch($event);
    }
}
