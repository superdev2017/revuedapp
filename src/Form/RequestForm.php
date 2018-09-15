<?php
namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;
use Cake\Mailer\Email;
use Cake\Mailer\MailerAwareTrait;

class RequestForm extends Form
{
    use MailerAwareTrait;

    protected function _buildSchema(Schema $schema)
    {
        return $schema
            ->addField('email', ['type' => 'string'])
            ->addField('message', ['type' => 'string'])
        ;
    }

    protected function _buildValidator(Validator $validator)
    {
        return $validator
            ->add('email', 'format', [
                'rule' => 'email',
                'message' => 'A valid email address is required',
            ])
            ->add('message', 'length', [
                'rule' => ['minLength', 5],
                'message' => 'Please provide a message'
            ])
        ;
    }

    protected function _execute(array $data)
    {
        return $this->getMailer('User')->send('sendReviewRequest', [$data]);
    }
}
