<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ReviewFeedback Entity
 *
 * @property int $id
 * @property int $review_id
 * @property int $collaborator_id
 * @property string $type
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\Time $modified
 *
 * @property \App\Model\Entity\Review $review
 */
class ReviewFeedback extends Entity
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
}
