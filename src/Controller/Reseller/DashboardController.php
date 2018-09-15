<?php

namespace App\Controller\Reseller;

use Cake\Event\Event;
use Cake\Mailer\Email;
use Cake\Log\Log;
use App\Controller\AppController;;
use App\Utility\CsvIterator;
use App\Utility\AuthNetHandler;
use App\Utility\AuthNetException;

use App\Controller\DashboardController as BaseDashboard;

class DashboardController extends BaseDashboard
{
    /*
    * Allows only resellers
    *
    */
    public function isAuthorized($user)
    {
        if ($user['role'] == 'reseller') {
            return true;
        }

        // Default deny
        return false;
    }

    /*
    * Lists users under the reseller account
    *
    */
    public function listing()
    {
        $users = $this->Users->find()->where(['role' => 'user'])->andWhere(['reseller_id' => $this->loggedUser->id]);
        $this->paginate = [
            'limit' => 10,
            'order' => ['Users.id' => 'desc']
        ];
        $users = $this->paginate($users);

        $this->set(compact('users'));
        $this->set('_serialize', ['users']);
    }

    /*
    * Views a specific user under the reseller account
    *
    */
    public function view($id = null)
    {
        $user = $this->Users->find()->contain('UserSettings')->where(['Users.id' => $id, 'reseller_id' => $this->loggedUser->id])->first();

        if ( ! $user) {
            $this->Flash->error(__('The specified user could not be found.'));
            return $this->redirect(['action' => 'index']);
        }

        $subscription = $this->Users->UserSubscriptions->find()->where(['user_id' => $id, 'status' => 'active'])->first();

        $this->set(compact('user', 'subscription'));
        $this->set('_serialize', ['user', 'subscription']);
    }

    /*
    * Handles billing for a user under a reseller account
    *
    */
    public function userBilling()
    {
        if ($this->request->is(['patch', 'post', 'put'])) {
            $userId = $this->request->data('billing.user_id');
            $data = $this->request->data('billing');
            if (isset($this->loggedUser->user_setting->price)) {
                $data['amount'] = $this->loggedUser->user_setting->price;
            } else {
                $data['amount'] = '75.00';
            }

            try {
                $authHandler = new AuthNetHandler();
                if ($subscription_id = $authHandler->createSubscription($data)) {
                    $this->loadModel('UserSubscriptions');
                    $subscription = $this->UserSubscriptions->newEntity();
                    $subscription->user_id = $userId;
                    $subscription->subscription_id = $subscription_id;
                    $subscription->gateway = 'AuthNet';

                    $this->UserSubscriptions->save($subscription);
                }

                $this->Flash->success(__('The subscription has been created.'));
            } catch (AuthNetException $e) {
                $this->Flash->error($e->getMessage());
            } catch (\Exception $e) {
                $this->Flash->error('Unable to contact payment gateway, please try later.');
                Log::write('alert', $e);
            }

            if (isset($subscription_id) and $subscription_id) {
                $user = $this->Users->get($userId);
                $user->status = 'active';
                $this->Users->save($user);
            }

            return $this->redirect("/reseller/dashboard/view/$userId");
        }
    }

    /*
    * Handles settings for a user under a reseller account
    *
    */
    public function userSettings()
    {
        if ($this->request->is(['patch', 'post', 'put'])) {
            $userId = $this->request->data('user_id');
            $user = $this->Users->find()->contain('UserSettings')->where(['Users.id' => $userId, 'reseller_id' => $this->loggedUser->id])->first();
            $user = $this->Users->patchEntity($user, $this->request->data, ['associated' => 'UserSettings']);

            if ($this->Users->save($user)) {
                $this->Flash->success(__('Settings saved.'));
            } else {
                $this->Flash->error(__('Unable to save Settings. Please, try again.'));
            }

            return $this->redirect("/reseller/dashboard/view/$userId");
        }
    }
}

