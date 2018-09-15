<?php
namespace App\Event;

use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Event\EventListenerInterface;
use Cake\Routing\Router;
use Cake\Datasource\ModelAwareTrait;

use FFMpeg;
use \CloudConvert\Api;

class ReviewFeedbackListener implements EventListenerInterface
{
    use ModelAwareTrait;

    public function implementedEvents()
    {
        return [
            'Model.ReviewFeedback.given' => 'processFeedback',
            'Model.ReviewFeedback.changed' => 'processFeedbackChange'
        ];
    }

    public function processFeedback($event, $entity)
    {
        $this->loadModel('Reviews');

        $review = $this->Reviews->get($entity->review_id);

        switch($entity->type) {
            case 'flag':
                $review->status = 'flagged';
                break;
            case 'thumbup':
                $review->voteup += 1;
                if ($entity->dirty('type') and $entity->getOriginal('type') == 'thumbdown') {
                    $review->votedown -= 1;
                }

                break;
            case 'thumbdown':
                $review->votedown += 1;
                if ($entity->dirty('type') and $entity->getOriginal('type') == 'thumbup') {
                    $review->voteup -= 1;
                }

                break;
        }

        $this->Reviews->save($review);
    }
}
