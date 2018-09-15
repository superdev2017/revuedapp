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
 * ReviewAssets Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Reviews
 *
 * @method \App\Model\Entity\ReviewAsset get($primaryKey, $options = [])
 * @method \App\Model\Entity\ReviewAsset newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ReviewAsset[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ReviewAsset|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ReviewAsset patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ReviewAsset[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ReviewAsset findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ReviewAssetsTable extends Table
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

        $this->setTable('review_assets');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Reviews', [
            'foreignKey' => 'review_id',
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

        return $rules;
    }

    public function hasThumbnail($review_id)
    {
        $query = $this->find()->where(['review_id' => $review_id, 'type' => 'jpg']);
        $found = $query->first();

        return $found ? true : false;
    }

    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {}

    public function afterSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        if ($entity->isNew()) {
            if (in_array($entity->type, ['MOV', 'mov'])) {
                $event = new Event('Model.ReviewAsset.created', $this, ['reviewAsset' => $entity]);
                $this->eventManager()->dispatch($event);
            }

            if ( ! $this->hasThumbnail($entity->review_id)) {
                $event = new Event('Model.ReviewAsset.createThumbnail', $this, ['reviewAsset' => $entity]);
                $this->eventManager()->dispatch($event);
            }

            if (in_array($entity->type, ['3gp'])) {
                $event = new Event('Model.ReviewAsset.cloudConvert', $this, ['reviewAsset' => $entity]);
                $this->eventManager()->dispatch($event);
            }
        }
    }
}
