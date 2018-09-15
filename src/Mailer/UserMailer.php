<?php

namespace App\Mailer;

use Cake\Mailer\Mailer;
use Cake\Core\Configure;

class UserMailer extends Mailer
{
    public function resetPassword($user)
    {
        $this
            ->transport('gmail')
            ->to($user->email)
            ->subject('Reset password')
            ->set(['token' => $user->token])
        ;
    }

    public function sendReviewNotification($review)
    {
        $this
            ->transport('gmail')
            ->to($review->user->email)
            ->subject(__('You have a new review!'))
            ->emailFormat('html')
        ;
    }

    public function sendWelcome($user)
    {
        $this
            ->transport('gmail')
            ->viewVars(['user' => $user])
            ->to($user->email)
            ->subject(__('Welcome to Revued'))
            ->emailFormat('html')
        ;
    }

    public function sendNewPassword($user)
    {
        $this
            ->transport('gmail')
            ->viewVars(['user' => $user])
            ->to($user->email)
            ->subject(__('Temporary Password Request'))
            ->emailFormat('html')
        ;
    }

    public function sendReviewRequest($data)
    {
        $this
            ->transport('gmail')
            ->viewVars(['message' => $data['message'], 'settings' => $data['settings']])
            ->to($data['email'])
            ->subject(__('Review Request'))
            ->emailFormat('html')
        ;
    }

    public function sendVideoReviewRequest($data)
    {
        $this
            ->transport('gmail')
            ->viewVars(['message' => $data['message'], 'settings' => $data['settings'], 'token' => $data['token']])
            ->to($data['email'])
            ->subject(__('Review Request'))
            ->emailFormat('html')
        ;
    }
}
