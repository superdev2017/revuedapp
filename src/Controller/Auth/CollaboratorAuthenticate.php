<?php
/**
 * Collaborator Authenticate
 */

namespace App\Controller\Auth;

use Cake\Auth\BaseAuthenticate;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;

class CollaboratorAuthenticate extends BaseAuthenticate
{

    protected function _query1($email)
    {
        $config = $this->_config;
        $table = TableRegistry::get($config['userModel']);

        $options = [
            'conditions' => [$table->aliasField($config['fields']['username']) => $email]
        ];

        if (!empty($config['scope'])) {
            $options['conditions'] = array_merge($options['conditions'], $config['scope']);
        }

        if (!empty($config['contain'])) {
            $options['contain'] = $config['contain'];
        }

        $finder = $config['finder'];


        if (is_array($finder)) {
            $options += current($finder);
            $finder = key($finder);
        }



        if (!isset($options[$config['fields']['username']])) {
            $options[$config['fields']['username']] = $email;
        }

        var_dump($options);
        $query = $table->find($finder, $options);
        echo $query->sql();
        return $query;

    }
    protected function _findFacebookUser($email)
    {
        $result = $this->_query1($email)->first();

        if (empty($result)) {
            return false;
        }

        return $result->toArray();
    }

    /**
     * Checks the fields to ensure they are supplied.
     *
     * @param \Cake\Http\ServerRequest $request The request that contains login information.
     * @param array $fields The fields to be checked.
     * @return bool False if the fields have not been supplied. True if they exist.
     */
    protected function _checkFields(ServerRequest $request, array $fields)
    {
        foreach (['email'] as $field) {
            $value = $request->getQuery($field);
            if (empty($value) || !is_string($value)) {
                return false;
            }
        }
        return true;
    }

    public function authenticate(ServerRequest $request, Response $response)
    {

        $fields = $this->_config['fields'];
        if ( ! $this->_checkFields($request, $fields)) {
            return false;
        }

        return $this->_findFacebookUser($request->getQuery('email'));
    }
}
