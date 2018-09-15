<?php

namespace App\Controller\Admin;

use Cake\Event\Event;
use Cake\Mailer\Email;
use App\Controller\AppController;;
use App\Utility\CsvIterator;

class DashboardController extends AppController
{
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->loadModel('Users');
        $this->loadModel('Reviews');

        $this->loggedUser = $this->Users->get($this->Auth->user('id'));
        $this->set('loggedUser', $this->loggedUser);

        $this->viewBuilder()->setLayout('admin');
    }

    public function index()
    {
        $users = $this->Users->find()->where(['role' => 'user'])->orWhere(['role' => 'reseller']);
        $this->paginate = [
            'limit' => 10,
            'order' => ['Users.id' => 'desc']
        ];
        $users = $this->paginate($users);

        $this->set(compact('users'));
        $this->set('_serialize', ['users']);
    }

    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        $subscription = $this->Users->UserSubscriptions->find()->where(['user_id' => $user->id, 'status' => 'active'])->first();

        $this->set(compact('user', 'subscription'));
        $this->set('_serialize', ['user', 'subscription']);
    }

    public function edit($id = null)
    {
        $user = $this->Users->find()->where(['id' => $id])->first();

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
    }

    public function bulkUpload()
    {
        if ($this->request->is(['post']) and isset($_FILES['upload_file'])) {
            $userId = $this->request->data('user_id');
            $source = $this->request->data('source');
            $user = $this->Users->get($userId);

            $csv = new CsvIterator($_FILES['upload_file']['tmp_name']);

            foreach ($csv->parse() as $row) {
                $review = $this->Reviews->newEntity();
                $data['user_id'] = $user->id;
                $data['source'] = $source;
                $data['rating'] = $row['rating'];
                $data['title']  = $row['title'];
                $data['author'] = $row['author'];
                $data['date'] = date( 'Y-m-d H:i:s', strtotime( $row['date'] ));
                $data['body'] = $row['body'];
                $data['status'] = 'pending';

                $review = $this->Reviews->newEntity($data);
                $review->user = $user;

                try {
                    $this->Reviews->save($review);
                } catch (\PDOException $e) {
                } catch (\Exception $e) {
                    Log::write(
                        'error',
                        $e->getMessage()
                    );
                }
            }

            $this->Flash->success(__('File processed.'));
            return $this->redirect(['action' => 'view', $userId]);
        }
    }

    public function resellerCodes()
    {
        $this->loadModel('ResellerCodes');
        $resellerCodes = $this->ResellerCodes->find();
        $resellerCode = $this->ResellerCodes->newEntity();

        if ($this->request->is('post')) {
            $resellerCode = $this->ResellerCodes->patchEntity($resellerCode, $this->request->data);

            if ($this->ResellerCodes->save($resellerCode)) {
                $this->Flash->success(__('The code has been saved.'));
                $resellerCode = $this->ResellerCodes->newEntity();
            } else {
                $this->Flash->error(__('The code could not be saved. Please, try again.'));
            }
        }

        $this->set(compact('resellerCodes', 'resellerCode'));
    }

    public function deleteResellerCode($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $this->loadModel('ResellerCodes');
        $resellerCode = $this->ResellerCodes->get($id);
        if ($this->ResellerCodes->delete($resellerCode)) {
            $this->Flash->success(__('The code has been deleted.'));
        } else {
            $this->Flash->error(__('The code could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'resellerCodes']);
    }
}

