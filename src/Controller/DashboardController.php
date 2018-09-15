<?php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;
use Cake\Mailer\Email;
use Cake\Log\Log;

use App\Form\RequestForm;
use App\Form\RequestVideoForm;
use App\Utility\AuthNetHandler;
use App\Utility\AuthNetException;
use Facebook\Facebook;
use http\Url;

class DashboardController extends AppController
{
    public $loggedUser;

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->loadModel('Users');

        $this->loggedUser = $this->Users->get($this->Auth->user('id'), ['contain' => 'UserSettings']);
        $this->set('loggedUser', $this->loggedUser);
    }

    public function isAuthorized($user)
    {
        // Users with resellers can't access billing
        if ($this->request->getParam('action') === 'billing' and $user['reseller_id']) {
            return false;
        }

        // Any registered user can access public functions
        if ( ! $this->request->getParam('prefix')) {
            return true;
        }

        // Default deny
        return false;
    }

    public function index()
    {
        $this->loadModel('Reviews');

        $mapper = function ($review, $key, $mapReduce) {
            $mapReduce->emitIntermediate($review['count'], $review['status']);
        };

        $reducer = function ($count, $status, $mapReduce) {
            $mapReduce->emit($count[0], $status);
        };

        $onSources = [];
        if ($this->loggedUser->user_setting->av_on) {
            $onSources[] = 'av';
        }
        if ($this->loggedUser->user_setting->fb_on) {
            $onSources[] = 'fb';
        }
        if ($this->loggedUser->user_setting->gp_on) {
            $onSources[] = 'gp';
        }
        if ($this->loggedUser->user_setting->yp_on) {
            $onSources[] = 'yp';
        }
        if ($this->loggedUser->user_setting->rv_on) {
            $onSources[] = 'rv';
        }

        $reviews = $this->Reviews->find()->where(['user_id' => $this->loggedUser->id]);
        if ($onSources) {
            $reviews->where(['source IN' => $onSources]);
        }

        $reviews->select(['status', 'count' => $reviews->func()->count('status')])->group('status');
        $reviews->hydrate(false);
        $reviews->mapReduce($mapper, $reducer);
        $statusCount = $reviews->toArray();

        $mapper = function ($review, $key, $mapReduce) {
            $mapReduce->emitIntermediate($review['count'], $review['source']);
        };

        $reducer = function ($count, $source, $mapReduce) {
            $mapReduce->emit($count[0], $source);
        };

        $reviews = $this->Reviews->find()->where(['user_id' => $this->loggedUser->id]);
        if ($onSources) {
            $reviews->where(['source IN' => $onSources]);
        }

        $reviews->select(['source', 'count' => $reviews->func()->count('source')])->group('source');
        $reviews->hydrate(false);
        $reviews->mapReduce($mapper, $reducer);
        $sourceCount = $reviews->toArray();

        $this->set(compact('statusCount', 'sourceCount'));
        $this->render('/Dashboard/index');
    }

    public function billing()
    {
        $subscription = $this->Users->UserSubscriptions->find()->where(['user_id' => $this->loggedUser->id, 'status' => 'active'])->first();
/*
        if ($subscription) {
            # Check subscription
            if ($subscription->gateway == 'AuthNet') {
                try {
                    $authHandler = new AuthNetHandler();
                    $subscription->gateway_data = $authHandler->getSubscription($subscription->subscription_id);
                } catch (AuthNetException $e) {
                    $this->Flash->error($e->getMessage());
                } catch (\Exception $e) {
                    $this->Flash->error('Unable to get subscription information.');
                    Log::write('alert', $e);
                }
            }
        }
*/
        if ($this->request->is('post')) {
            $data = $this->request->data('billing');
            $data['trial_days'] = $this->loggedUser->trial_days;

            try {
                $authHandler = new AuthNetHandler();
                if ($subscription_id = $authHandler->createSubscription($data)) {
                    $subscription = $this->Users->UserSubscriptions->newEntity();
                    $subscription->user_id = $this->loggedUser->id;
                    $subscription->subscription_id = $subscription_id;
                    $subscription->gateway = 'AuthNet';

                    $this->Users->UserSubscriptions->save($subscription);
                }
            } catch (AuthNetException $e) {
                $this->Flash->error($e->getMessage());
            } catch (\Exception $e) {
                $this->Flash->error('Unable to contact payment gateway, please try later.');
                Log::write('alert', $e);
            }
        }

        $this->set(compact('subscription'));
        $this->render('/Dashboard/billing');
    }

    public function settings()
    {
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($this->loggedUser, $this->request->data, ['associated' => 'UserSettings']);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('Settings saved.'));
                return $this->redirect(['controller' => 'Dashboard']);
            } else {
                $this->Flash->error(__('Unable to save Settings. Please, try again.'));
            }
        }

        $this->render('/Dashboard/settings');
    }

    public function code()
    {
        $display_mode = $this->loggedUser->user_setting->display_mode;

        $this->set(compact('display_mode'));
        $this->set('_serialize', ['display_mode']);
        $this->render('/Dashboard/code');
    }

    public function reviews()
    {
        if ($this->request->is(['post'])) {
            $this->loadModel('Reviews');
            $this->loadComponent('Paginator');

            $sources = $this->request->data('sources');
            $status = $this->request->data('status');
            $limit = $this->request->data('limit');

            if ( ! $limit) {
                $limit = 10;
            }

            $onSources = [];
            if ($this->loggedUser->user_setting->av_on) {
                $onSources[] = 'av';
            }
            if ($this->loggedUser->user_setting->fb_on) {
                $onSources[] = 'fb';
            }
            if ($this->loggedUser->user_setting->gp_on) {
                $onSources[] = 'gp';
            }
            if ($this->loggedUser->user_setting->yp_on) {
                $onSources[] = 'yp';
            }
            if ($this->loggedUser->user_setting->rv_on) {
                $onSources[] = 'rv';
            }

            if ( ! isset($sources)) {
                $sources = $onSources;
            } else {
                $sources = array_intersect($sources, $onSources);
            }

            $reviews = $this->Reviews->find()->contain('ReviewAssets')->where(['user_id' => $this->loggedUser->id]);
            if ($sources) {
                $reviews->where(['source IN' => $sources]);
            }

            if ($status and $status != 'all') {
                $reviews->where(['status' => $status]);
            }

            $reviews = $this->Paginator->paginate($reviews, ['limit' => $limit]);

            $this->set(compact('reviews', 'limit'));
            $this->set('_serialize', ['reviews']);
            $this->render('/Dashboard/reviews');
        }
    }

    public function account()
    {
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($this->loggedUser, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('Account updated.'));
                return $this->redirect(['controller' => 'Dashboard']);
            } else {
                $this->Flash->error(__('Unable to update. Please, try again.'));
            }
        }

        $this->render('/Dashboard/account');
    }

    public function requestReview()
    {
        $form = new RequestForm();
        if ($this->request->is('post')) {
            $data = $this->request->data;
            $data['settings'] = $this->loggedUser->user_setting;
            if ($form->execute($data)) {
                $this->Flash->success('Review Request sent.');
                return $this->redirect(['controller' => 'dashboard']);
            } else {
                $this->Flash->error('Unable to send request. Please, try again.');
            }
        }

        $this->set(compact('form'));
    }

    public function requestVideoReview()
    {
        $form = new RequestVideoForm();
        if ($this->request->is('post')) {
            $data = $this->request->data;
            $data['settings'] = $this->loggedUser->user_setting;
            if ($form->execute($data)) {
                $this->Flash->success('Review Request sent.');
                return $this->redirect(['controller' => 'dashboard']);
            } else {
                $this->Flash->error('Unable to send request. Please, try again.');
            }
        }

        $this->set(compact('form'));
        $this->render('/Dashboard/request_review');
    }


    public function requestYelpSearch() {


        // API constants, you shouldn't have to change these.
        $API_HOST = "https://api.yelp.com";
        $SEARCH_PATH = "/v3/businesses/search";
        $BUSINESS_PATH = "/v3/businesses/";  // Business ID will come after slash.
        // Defaults for our simple example.
        $DEFAULT_TERM = "dinner";
        $DEFAULT_LOCATION = "San Francisco, CA";
        $limit = $this->request->getData('limit');
        $SEARCH_LIMIT = 10;

        $GLOBALS['API_HOST'] = $API_HOST;
        $GLOBALS['SEARCH_PATH'] = $SEARCH_PATH;
        $GLOBALS['BUSINESS_PATH'] = $BUSINESS_PATH;
        $GLOBALS['DEFAULT_TERM'] = $DEFAULT_TERM;
        $GLOBALS['DEFAULT_LOCATION'] = $DEFAULT_LOCATION;
        $GLOBALS['SEARCH_LIMIT'] = $SEARCH_LIMIT;

        /**
         * Makes a request to the Yelp API and returns the response
         */
        function request($host, $path, $url_params = array()) {
            // Send Yelp API Call
            try {

                // https://www.yelp.com/developers/v3/manage_app
                $API_KEY = Configure::read('YelpApiKey');
                // Complain if credentials haven't been filled out.
                //assert($API_KEY, "Please supply your API key.");

                $curl = curl_init();
                if (FALSE === $curl)
                    throw new Exception('Failed to initialize');
                $url = $host . $path . "?" . http_build_query($url_params);
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,  // Capture response.
                    CURLOPT_ENCODING => "",  // Accept gzip/deflate/whatever.
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        "authorization: Bearer " . $API_KEY,
                        "cache-control: no-cache",
                    ),
                ));
                $response = curl_exec($curl);
                if (FALSE === $response)
                    throw new Exception(curl_error($curl), curl_errno($curl));
                $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                if (200 != $http_status)
                    throw new Exception($response, $http_status);
                curl_close($curl);
            } catch(Exception $e) {
                trigger_error(sprintf(
                    'Curl failed with error #%d: %s',
                    $e->getCode(), $e->getMessage()),
                    E_USER_ERROR);
            }
            return $response;
        }
        /**
         * Query the Search API by a search term and location
         */
        function search($term, $location) {
            $url_params = array();

            $url_params['term'] = $term;
            $url_params['location'] = $location;
            $url_params['limit'] = $GLOBALS['SEARCH_LIMIT'];

            return request($GLOBALS['API_HOST'], $GLOBALS['SEARCH_PATH'], $url_params);
        }
        /**
         * Query the Business API by business_id
         */
        function get_business($business_id) {
            $business_path = $GLOBALS['BUSINESS_PATH'] . urlencode($business_id);

            return request($GLOBALS['API_HOST'], $business_path);
        }
        /**
         * Queries the API by the input values from the user
         */
        function query_api($term, $location) {
            $response = json_decode(search($term, $location));
            $business_id = $response->businesses[0]->id;

            print sprintf(
                "%d businesses found, querying business info for the top result \"%s\"\n\n",
                count($response->businesses),
                $business_id
            );

            $response = get_business($business_id);

            print sprintf("Result for business \"%s\" found:\n", $business_id);
            $pretty_response = json_encode(json_decode($response), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            print "$pretty_response\n";
        }

        $term = $this->request->getData('term');
        $location = $this->request->getData('location');

        $term = $term ?: $GLOBALS['DEFAULT_TERM'];
        $location = $location ?: $GLOBALS['DEFAULT_LOCATION'];
        //query_api($term, $location);
        $result = search($term, $location);

        $this->response->type('json');
        $this->response->body($result);

        return $this->response;


    }


    public function requestPage() {
        $fb = new Facebook([
            'app_id' => Configure::read('Facebook.appId'),
            'app_secret' => Configure::read('Facebook.secret'),
            'default_graph_version' => 'v2.4',
        ]);


        $page_id = $this->request->getData('page_id');
        $accessToken = $this->request->getData('access_tocken');

        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get(
                "/$page_id/ratings",
                $accessToken
            );

            $accountsBody = $response->getDecodedBody();
            $result["error"] = false;
            $result["message"] = 'Success';
            $result["data"] = $accountsBody["data"];
            $json_output = json_encode($result, true);
            header('Content-Type: application/json');
            echo $json_output;
            die;

        } catch(FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }




    }
    /**
     * Facebook pages
     *
     */
    public function requestFacebookPages()
    {
        $session = $this->request->session();
        $fbSessionAccessToken = $session->read('facebook_access_token');
        $fb = new Facebook([
            'app_id' => Configure::read('Facebook.appId'),
            'app_secret' => Configure::read('Facebook.secret'),
            'default_graph_version' => 'v2.4',
        ]);

        $helper = $fb->getJavaScriptHelper();
        header('Content-Type: application/json');
        $result = array("error" => false, "message" => "", "data" => "");
        try {
            if (isset($fbSessionAccessToken)) {
                $accessToken = $session->read('facebook_access_token');
            } else {
                $accessToken = $helper->getAccessToken();
            }
        } catch(FacebookResponseException $e) {
            // When Graph returns an error
            $result["error"] = true;
            $result["message"] = 'Facebook Graph an error: ' . $e->getMessage();
            $json_output = json_encode($result, true);
            echo $json_output;
            exit;
        } catch(FacebookSDKException $e) {
            // When validation fails or other local issues
            $result["error"] = true;
            $result["message"] = 'Facebook SDK returned an error: ' . $e->getMessage();
            $json_output = json_encode($result, true);
            echo $json_output;
            exit;
        }


        if (isset($accessToken)) {
            if (isset($fbSessionAccessToken)) {
                $fb->setDefaultAccessToken($session->read('facebook_access_token'));
            } else {
                $session->write('facebook_access_token', (string)$accessToken);
                // OAuth 2.0 client handler
                $oAuth2Client = $fb->getOAuth2Client();
                // Exchanges a short-lived access token for a long-lived one
                $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($session->read('facebook_access_token'));
                $session->write('facebook_access_token', (string)$longLivedAccessToken);
                $fb->setDefaultAccessToken($session->read('facebook_access_token'));
            }
            // validating the access token
            try {
                $request = $fb->get('/me');
            } catch(FacebookResponseException $e) {
                // When Graph returns an error
                if ($e->getCode() == 190) {
                    $result["error"] = true;
                    $result["message"] = 'Session is close';
                    $json_output = json_encode($result, true);
                    echo $json_output;
                    exit;
                }
            } catch(FacebookSDKException $e) {
                // When validation fails or other local issues
                $result["error"] = true;
                $result["message"] = 'Facebook SDK returned an error: ' . $e->getMessage();
                $json_output = json_encode($result, true);
                echo $json_output;
                exit;
            }
            // get list of pages by user
            try {
                $requestAccounts = $fb->get('/me/accounts');
                $accountsBody = $requestAccounts->getDecodedBody();
                $result["error"] = false;
                $result["message"] = 'Success';
                $result["data"] = array("longLiveAccessToken" => $session->read('facebook_access_token'), "pages" => $accountsBody["data"]);
                $json_output = json_encode($result, true);
                header('Content-Type: application/json');
                echo $json_output;
                die;
            } catch(FacebookResponseException $e) {
                // When Graph returns an error
                $result["error"] = true;
                $result["message"] = 'Graph returned an error: ' . $e->getMessage();
                $json_output = json_encode($result, true);
                echo $json_output;
                exit;
            } catch(FacebookSDKException $e) {
                // When validation fails or other local issues
                $result["error"] = true;
                $result["message"] = 'Facebook SDK returned an error: ' . $e->getMessage();
                $json_output = json_encode($result, true);
                echo $json_output;
                exit;
            }
        } else {
            // When validation fails or other local issues
            $result["error"] = true;
            $result["message"] = 'Session Closed';
            $json_output = json_encode($result, true);
            echo $json_output;
            exit;
        }
    }
}
