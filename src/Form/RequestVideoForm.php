<?php
namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;
use Cake\Mailer\Email;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\TableRegistry;

use Alt3\Tokens\RandomBytesToken;

class RequestVideoForm extends Form
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
        // create a token object
        $token = new RandomBytesToken();
        $token->setCategory('revued-pass');
        $token->setLifetime('+3 day');
        $token->foreign_table = 'users';
        $token->foreign_key = $data['settings']->user_id;

        // save the token object
        $table = TableRegistry::get('Alt3/CakeTokens.Tokens');
        $entity = $table->newEntity($token->toArray());

        if ($table->save($entity)) {
            $data['token'] = $entity->token;
            return $this->getMailer('User')->send('sendVideoReviewRequest', [$data]);
        }

        return false;
    }
}
