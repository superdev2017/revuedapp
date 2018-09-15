<?php

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Log\Log;

class MobileAppController extends AppController
{
    public function initialize()
    {
        parent::initialize();

        $this->Auth->allow();
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->loadModel('Collaborators');
    }

    /*
    * All authorized
    *
    */
    public function isAuthorized($collaborator)
    {
        return true;
    }

    /*
    * Handles login from the mobile app
    *
    */
    public function login()
    {
        $response = ['success' => 1, 'message' => __('Unable to log you in, please try again')];

        if ($provider = $this->request->data('provider')) {
            $response = $this->_providerLogin($provider);
        } else {
            $this->Auth->setConfig('authenticate', [
                'Form' => [
                    'userModel' => 'Collaborators',
                    'fields' => ['username' => 'email']
                ]
            ]);

            if ($this->request->is('post')) {
                if ($collaborator = $this->Auth->identify()) {
                    $response = ['success' => 1, 'message' => __('Login successful'), 'data' => $collaborator];
                } else {
                    $response = ['success' => 0, 'message' => __('Invalid username or password, try again')];
                }
            }
        }

        $result = json_encode($response);

        $this->response->type('json');
        $this->response->body($result);

        return $this->response;
    }

    public function listVideos()
    {
        $this->loadModel('Reviews');

        $type = $this->request->data('type');
        $placeID = $this->request->data('placeID');
        $collaboratorID = $this->request->data('collaboratorID');

        $videos = []; $avg = 0;
        if ($type == 'place' and $placeID) {
            $videos = $this->Reviews->find()->where(['place_id' => $placeID, 'status' => 'active', 'source' =>'rv'])->contain('ReviewAssets')->contain('ReviewFeedbacks', function ($q) use ($collaboratorID) {
                return $q->where(['ReviewFeedbacks.collaborator_id' => $collaboratorID]);
            })->all();

            if (($count = count($videos)) > 0) {
                $sum = 0;
                foreach($videos as $video) {
                    $sum += $video->rating;
                }

                $avg = $sum / $count;
            }
        }

        if ($type == 'featured') {
            $videos = $this->Reviews->find()->where(['status' => 'active', 'source' => 'rv'])->contain('ReviewAssets')->contain('ReviewFeedbacks', function ($q) use ($collaboratorID) {
                return $q->where(['ReviewFeedbacks.collaborator_id' => $collaboratorID]);
            })->all();
        }

        if ( ! empty($videos)) {
            $resultJ = json_encode(['success' => 1, 'message' => '', 'data' => ['count' => count($videos), 'videos' => $videos, 'ratingAVG' => $avg]]);
        } else {
            $resultJ = json_encode(['success' => 0, 'message' => 'No video reviews available.']);
        }

        $this->response->type('json');
        $this->response->body($resultJ);

        return $this->response;
    }

    /*
    * Handles reviewing the uhm review
    *
    */
    public function thumb()
    {
        $this->loadModel('Reviews');
        //$this->loadModel('ReviewFeedbacks');
        $action = $this->request->data('action');
        $reviewID = $this->request->data('reviewID');
        $collaboratorID = $this->request->data('collaboratorID');

        $this->response->type('json');

        if ($collaboratorID == null) {
            $resultJ = json_encode(['success' => 0, 'message' => 'Collaborator ID is required.']);
            $this->response->body($resultJ);

            return $this->response;
        }

        if ($reviewID == null) {
            $resultJ = json_encode(['success' => 0, 'message' => 'Review ID is required.']);
            $this->response->body($resultJ);

            return $this->response;
        }

        if ($action == null) {
            $resultJ = json_encode(['success' => 0, 'message' => 'No action specified.']);
            $this->response->body($resultJ);

            return $this->response;
        }

        $review = $this->Reviews->find()->where(['id' => $reviewID])->first();

        if ( ! $review) {
            $resultJ = json_encode(['success' => 0, 'message' => 'Review does not exist anymore, so...']);
            $this->response->body($resultJ);

            return $this->response;
        }

        $reviewFeedback = $this->Reviews->ReviewFeedbacks->find()->where(['review_id' => $review->id, 'collaborator_id' => $collaboratorID])->first();
        if ( ! $reviewFeedback) {
            $reviewFeedback = $this->Reviews->ReviewFeedbacks->newEntity(['review_id' => $review->id, 'collaborator_id' => $collaboratorID, 'type' => $action]);
        } else {
            $reviewFeedback = $this->Reviews->ReviewFeedbacks->patchEntity($reviewFeedback, ['type' => $action]);
        }

        try {
            $this->Reviews->ReviewFeedbacks->save($reviewFeedback);
        } catch (\PDOException $e) {
        } catch (\Exception $e) {
            Log::write(
                'error',
                $e->getMessage()
            );

            $resultJ = json_encode(['success' => 0, 'message' => 'error']);
            $this->response->type('json');
            $this->response->body($resultJ);
            return $this->response;
        }

        $review = $this->Reviews->get($review->id);
        $resultJ = json_encode(['success' => 0, 'message' => 'Thank you for your feedback.', 'data' => ['thumbup' => $review->voteup, 'thumbdown' => $review->votedown]]);
        $this->response->body($resultJ);

        return $this->response;
    }

    /*
    * Handles 3rd party login (FB, Gg, etc) tries to authenticate, if not, it creates a collaborator from the post data
    *
    */
    private function _providerLogin($provider)
    {
        switch ($provider) {
            default: //FB
                $authParams = [
                    'Form' => [
                        'userModel' => 'Collaborators',
                        'fields' => ['username' => 'fb_id']
                    ]
                ];

                $this->request->data['fb_id'] = $this->request->data('id');
                $this->request->data['password'] = $this->request->data('id');
        }

        $this->Auth->setConfig('authenticate', $authParams);

        if ($this->request->is('post')) {
            if ($collaborator = $this->Auth->identify()) {
                return ['success' => 1, 'message' => __('Login successful'), 'data' => $collaborator];
            } else {
                $collaborator = $this->Collaborators->newEntity();
                $collaborator = $this->Collaborators->patchEntity($collaborator, $this->request->data);
                if ($result = $this->Collaborators->save($collaborator)) {
                    return ['success' => 1, 'message' => __('Welcome to Revued.'), 'data' => ['collaborator_id' => $result->id]];
                }
            }
        }

        return ['success' => 0, 'message' => __('Your account could not be saved. Please, try again.')];
    }
}
