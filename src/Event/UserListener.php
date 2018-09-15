<?php
namespace App\Event;

use Cake\Log\Log;
use Cake\Event\EventListenerInterface;
use Cake\Mailer\MailerAwareTrait;

class UserListener implements EventListenerInterface
{
    use MailerAwareTrait;

    public function implementedEvents()
    {
        return [
            'Model.User.created' => 'sendNewAccountNotification',
        ];
    }

    public function sendNewAccountNotification($event, $entity)
    {
        try {
            $this->getMailer('User')->send('sendWelcome', [$entity]);
            $this->getMailer('System')->send('sendSystemNewAccountNotification', [$entity]);
        } catch (\Exception $e) {
            Log::write('alert', $e);
        }
    }
}
