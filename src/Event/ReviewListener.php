<?php
namespace App\Event;

use Cake\Log\Log;
use Cake\Event\EventListenerInterface;
use Cake\Mailer\MailerAwareTrait;

class ReviewListener implements EventListenerInterface
{
    use MailerAwareTrait;

    public function implementedEvents()
    {
        return [
            'Model.Review.created' => 'sendReviewNotification',
            'Model.Review.rating' => 'ratingAverage',
        ];
    }

    public function sendReviewNotification($event, $entity)
    {
        if ($entity->user_id) {
            try {
                $this->getMailer('User')->send('sendReviewNotification', [$entity]);
            } catch (\Exception $e) {
                Log::write('alert', $e);
            }
        }
    }

    public function ratingAverage($event, $entity)
    {
        
    }
}
