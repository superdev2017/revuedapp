<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Collaborators Controller
 *
 * @property \App\Model\Table\CollaboratorsTable $Collaborators
 */
class CollaboratorsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow();
    }

    public function register()
    {
        $this->autoRender = false;

        $this->loadModel('Collaborators');
        $collaborator = $this->Collaborators->newEntity();

        if ($this->request->is('post')) {
            $collaborator = $this->Collaborators->patchEntity($collaborator, $this->request->data);

            if ($result = $this->Collaborators->save($collaborator)) {
                $resultJ = json_encode(['success' => 1, 'message' => __('Welcome to Revued.'), 'data' => ['collaborator_id' => $result->id]]);
            } else {
                $resultJ = json_encode(['success' => 0, 'message' => __('Your account could not be saved. Please, try again.')]);
            }

        }

        $this->response->type('json');
        $this->response->body($resultJ);
        return;
    }

}
