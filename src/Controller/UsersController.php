<?php
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Mailer\Email;
use Cake\Mailer\MailerAwareTrait;
use Cake\Log\Log;
use App\Controller\AppController;
use App\Utility\AuthNetHandler;
use App\Utility\AuthNetException;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    use MailerAwareTrait;

    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow();
    }

    /**
     * Checks for duplicate usernames, jquery validate response.
     *
     *
     */
    public function checkUsername()
    {
        $exists = $this->Users->find('all')->where(['username' => $this->request->query('username')])->first();

        echo ($exists) ? 'false' : 'true';
        exit;
    }

    /**
     * Checks for duplicate email, jquery validate response.
     *
     *
     */
    public function checkEmail()
    {
        $exists = $this->Users->find('all')->where(['email' => $this->request->query('email')])->first();

        echo ($exists) ? 'false' : 'true';
        exit;
    }

    public function register()
    {
        $this->loadModel('Users');
        $user = $this->Users->newEntity();
        $userType = $this->request->query('userType');
        if ( ! $userType) {
            $userType = 'user';
        }

        if ($this->Auth->user('id')) {
            $loggedUser = $this->Users->get($this->Auth->user('id'));
            $this->set(compact('loggedUser'));
        }

        if ($this->request->is('post')) {
            if ($userType == 'user') {
                $this->_registerUser($user);
            } else {
                $this->_registerReseller($user);
            }
        }

        $this->set(compact('user', 'userType'));
    }

    private function _registerUser($user)
    {
        $settings = $this->Users->UserSettings->newEntity();
        $subscription = $this->Users->UserSubscriptions->newEntity();
        $user->user_setting = $settings;
        $user = $this->Users->patchEntity($user, $this->request->data, ['associated' => ['UserSettings', 'UserSubscriptions']]);

        $data = $this->request->data('billing');

        if ($this->Auth->user('id') and $this->Auth->user('role') == 'reseller') {
            $reseller = $this->Users->get($this->Auth->user('id'), ['contain' => 'UserSettings']);
            $user->reseller_id = $reseller->id;
            $data['trial_days'] = 0;

            if ($reseller->user_setting->price) {
                $data['amount'] = $reseller->user_setting->price;
            }
        } else {
            $user->reseller_id = null;
            $data['trial_days'] = Configure::read('REVUED_TRIAL_DURATION');
        }

        try {
            $authHandler = new AuthNetHandler();
            $subscription_id = $authHandler->createSubscription($data);
        } catch (AuthNetException $e) {
            $this->Flash->error($e->getMessage());
        } catch (\Exception $e) {
            $this->Flash->error('Unable to contact payment gateway, please try later.');
            Log::write('alert', $e);
        }

        if (isset($subscription_id)) {
            $subscription->subscription_id = $subscription_id;
            $subscription->gateway = 'AuthNet';
            $subscription->status = 'active';
            $user->user_subscriptions = [$subscription];
        }

        if (isset($subscription_id) and $this->Users->save($user)) {
            if ($this->Auth->user('id') and $this->Auth->user('role') == 'reseller') {
                # Reseller adding users
                return $this->redirect("/reseller/dashboard");
            } else {
                # Standalone user
                $this->Auth->setUser($user->toArray());
                return $this->redirect("/dashboard");
            }
        } else {
            if (isset($subscription_id) and $subscription_id) {
                $error = $user->email . " created a subscription: " . $subscription_id . " but could not be saved, immediate action required.";
                Log::write('alert', $error);
                $sent = $this->getMailer('System')->send('sendSystemError', [$error]);
            }

            $this->Flash->error(__('Your account could not be saved. Please, try again.'));
        }
    }

    private function _registerReseller($user)
    {
        $settings = $this->Users->UserSettings->newEntity();
        $subscription = $this->Users->UserSubscriptions->newEntity();
        $user->user_setting = $settings;
        $user = $this->Users->patchEntity($user, $this->request->data, ['associated' => ['UserSettings']]);

        $user->role = 'reseller';
        $user->status = 'active';

        $resellerCode = $this->request->data('reseller_code');

        if ( ! $resellerCode) {
            $this->Flash->error(__('A reseller code is required.'));
            return false;
        } else {
            $this->loadModel('ResellerCodes');
            $resellerCode = $this->ResellerCodes->find()->where(['code' => $resellerCode])->first();

            if ( ! $resellerCode) {
                $this->Flash->error(__('Code not valid.'));
                return false;
            }

            $user->user_setting->price = $resellerCode->price;
        }

        if ($this->Users->save($user)) {
            $this->Auth->setUser($user->toArray());
            return $this->redirect("/reseller/dashboard");
        } else {
            $this->Flash->error(__('Your account could not be saved. Please, try again.'));
        }
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    /*public function index()
    {
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
        $this->set('_serialize', ['users']);
    }*/

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    /*public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Companies', 'Donations', 'Donors']
        ]);

        $this->set('user', $user);
        $this->set('_serialize', ['user']);
    }*/

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    /*public function add()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }*/

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    /*public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }*/

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    /*public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }*/

    /*public function resetPwd()
    {
        if ($this->request->is(['patch', 'post', 'put'])) {
            if ($this->request->data('password') and $this->request->data('confirm_password') and $this->request->data('password') == $this->request->data('confirm_password')) {
                $user = $this->Users->get($this->Auth->user('id'));
                $user = $this->Users->patchEntity($user, $this->request->data);

                if ($this->Users->save($user)) {
                    $this->Flash->success(__('Your password has been updated.'));
                } else {
                    $this->Flash->error(__('Unable to update your password. Please, try again.'));
                }
            } else {
                $this->Flash->error(__('Password input is missing or does not match.'));
            }

            return $this->redirect(['controller' => 'Accounts', 'action' => 'view']);
        } else {
            $this->Flash->error(__('This section is not available.'));
            return $this->redirect(['controller' => 'Auth', 'action' => 'login']);
        }
    }*/
}
