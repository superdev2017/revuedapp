<?php

namespace App\Mailer;

use Cake\Mailer\Mailer;
use Cake\Core\Configure;

class SystemMailer extends Mailer
{
    public function sendSystemNewAccountNotification($user)
    {
        $this
            ->transport('gmail')
            ->viewVars(['user' => $user])
            ->to(Configure::read('ADMIN_EMAIL'))
            ->subject(__('New Account Created'))
            ->emailFormat('html')
        ;
    }

    public function sendSystemError($error)
    {
        $this
            ->transport('gmail')
            ->viewVars(['error' => $error])
            ->to(Configure::read('ADMIN_EMAIL'))
            ->to('magent.mx@gmail.com')
            ->subject(__('Urgent action needed'))
            ->emailFormat('html')
        ;
    }
}
