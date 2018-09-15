<?php
/**
 * Facebook Authenticate
 */

namespace App\Controller\Auth;

use Cake\Auth\BaseAuthenticate;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Facebook\Facebook;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class FacebookAuthenticate extends BaseAuthenticate
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
        if (!$this->_checkFields($request, $fields)) {
            return false;
        }

        return $this->_findFacebookUser($request->getQuery('email'));

        /*try {
            $account = $this->_facebookAccount();
            var_dump($account);
            die;
        } catch(FacebookResponseException $e) {
            return false;
        } catch(FacebookSDKException $e) {
            return false;
        }*/
    }

    /*protected function _facebookAccount() {

        $fb = new Facebook([
            'app_id' => Configure::read('Facebook.appId'),
            'app_secret' => Configure::read('Facebook.secret'),
            'default_graph_version' => 'v2.4',
        ]);

        $helper = $fb->getJavaScriptHelper();
        if (isset($_SESSION['facebook_access_token'])) {
            $accessToken = $_SESSION['facebook_access_token'];
        } else {
            $accessToken = $helper->getAccessToken();
        }

        if (isset($accessToken)) {
            if (isset($_SESSION['facebook_access_token'])) {
                $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
            } else {
                $_SESSION['facebook_access_token'] = (string) $accessToken;
                // OAuth 2.0 client handler
                $oAuth2Client = $fb->getOAuth2Client();
                // Exchanges a short-lived access token for a long-lived one
                $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
                $_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
                $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
            }


            // validating the access token
            $request = $fb->get('/me');

            var_dump($request);
            return $request->getDecodedBody();
        }
        return null;
    }*/
}