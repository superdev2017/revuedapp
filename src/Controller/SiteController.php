<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use App\Form\ContactForm;


/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link http://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class SiteController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow();
    }

    public function index()
    {}

    public function about()
    {}

    public function contact()
    {
        $form = new ContactForm();
        if ($this->request->is('post')) {
            if ($form->execute($this->request->data)) {
                $this->Flash->success('We will get back to you soon.');
            } else {
                $this->Flash->error('There was a problem submitting your form.');
            }
        }

        $this->set(compact('form'));
    }

    public function embed($id)
    {
        $this->loadModel('Users');
        $this->loadModel('Reviews');

        $user = $this->Users->get($id, ['contain' => 'UserSettings']);

        $mapper = function ($review, $key, $mapReduce) {
            $mapReduce->emitIntermediate($review, $review['source']);
        };

        $reducer = function ($review, $source, $mapReduce) {
            $mapReduce->emit($review, $source);
        };

        $reviews = $this->Reviews->find()->contain('ReviewAssets')->where(['user_id' => $id, 'status' => 'active'])->order(['date' => 'desc']);
        $reviews->hydrate(false);
        $reviews->mapReduce($mapper, $reducer);

        $data = $reviews->toArray();
        if ( ! isset($data['av'])) $data['av'] = [];
        if ( ! isset($data['gp'])) $data['gp'] = [];
        if ( ! isset($data['fb'])) $data['fb'] = [];
        if ( ! isset($data['yp'])) $data['yp'] = [];
        if ( ! isset($data['rv'])) $data['rv'] = [];

        $this->viewBuilder()->setLayout('revued');

        $this->set(compact('data', 'user'));
    }
}
