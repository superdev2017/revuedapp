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
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Routing\Router;

use Alt3\Tokens\RandomBytesToken;
use \CloudConvert\Api;
use \CloudConvert\Process;


/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link http://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class VideoController extends AppController
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

    public function record()
    {
        $token = $this->request->query('token');
        $revuedPass = $this->validateToken($token);

        $this->set(compact('token'));

        if ($this->request->is('mobile') or 1) {
            $this->render('record_mobile');
        }
    }

    public function store()
    {
        $token = $this->request->data('token');
        $revuedPass = $this->validateToken($token);

        $this->autoRender = false;

        if ($this->request->data('recording') == null) {
            $resultJ = json_encode(['message' => 'Empty file name.']);
            $this->response->type('json');
            $this->response->body($resultJ);
            return;
        }

        $recording = $this->request->data('recording');

        $fileName = $recording['name'];
        $md5fileName = md5($recording['name']);
        $tempName = $recording['tmp_name'];
        $file_idx = 'recording';
/*
        if ( ! empty($_FILES[$file_idx])) {
            $fileName = $this->request->data('recording');
            $tempName = $_FILES[$file_idx]['tmp_name'];
        }
*/
        if ( ! $fileName or ! $tempName) {
            if( ! $tempName) {
                $resultJ = json_encode(['message' => 'Invalid temp_name: ' . $tempName]);
                $this->response->type('json');
                $this->response->body($resultJ);
                return;
            }

            $resultJ = json_encode(['message' => 'Invalid file name: ' . $fileName]);
            $this->response->type('json');
            $this->response->body($resultJ);
            return;
        }
/*
        $upload_max_filesize = return_bytes(ini_get('upload_max_filesize'));
        if ($_FILES[$file_idx]['size'] > $upload_max_filesize)
            echo 'upload_max_filesize exceeded.';
            return;
        }

        $post_max_size = return_bytes(ini_get('post_max_size'));
        if ($_FILES[$file_idx]['size'] > $post_max_size)
            echo 'post_max_size exceeded.';
            return;
        }
*/
        $filePath = 'uploads/' . $fileName;

        // Valid audio/video files
        $allowed = [
            'webm',
            'wav',
            'mp4',
            "mkv",
            'mp3',
            'ogg',
            'mov',
            'MOV',
            '3gpp',
            '3GPP'
        ];

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        if ( ! $extension || empty($extension) || ! in_array($extension, $allowed)) {
            $resultJ = json_encode(['message' => 'Invalid file extension: ' . $extension]);
            $this->response->type('json');
            $this->response->body($resultJ);
            return;
        }

        $md5filePath = 'uploads/' . $md5fileName . '.' . $extension;

        if ( ! move_uploaded_file($tempName, $md5filePath)) {
            $resultJ = json_encode(['message' => 'Problem saving file: ' . $tempName]);
            $this->response->type('json');
            $this->response->body($resultJ);
            return;
        }

        $this->loadModel('Users');
        $user = $this->Users->get($revuedPass->foreign_key);

        $rating = $this->request->data('rating');
        $author = $this->request->data('author');

        $data['user_id'] = $user->id;
        $data['source'] = 'rv';
        $data['rating'] = $rating ? $rating : '0';
        $data['title'] = 'Revued Video';
        $data['author'] = $author ? $author : __('anonymous');
        $data['body'] = $token;
        $data['author_img'] = '';
        $data['date'] = date('Y-m-d H:i:s');
        $data['status'] = 'pending';

        $this->loadModel('Reviews');
        $review = $this->Reviews->newEntity($data);
        $review->user = $user;

        $reviewAsset = $this->Reviews->ReviewAssets->newEntity();
        $reviewAsset->type = $extension;
        $reviewAsset->src = Router::url("/$md5filePath");

        $review->review_assets = [$reviewAsset];

        try {
            $this->Reviews->save($review);
        } catch (\PDOException $e) {
        } catch (\Exception $e) {
            Log::write(
                'error',
                $e->getMessage()
            );

            $resultJ = json_encode(['message' => 'error']);
            $this->response->type('json');
            $this->response->body($resultJ);
            return $this->response;
        }

        $resultJ = json_encode(['message' => 'success']);
        $this->response->type('json');
        $this->response->body($resultJ);
        return $this->response;
    }

    /*
    * Stores Video submitted by mobile device
    *
    */
    public function appStore()
    {
        $this->autoRender = false;
        if ($this->request->data('collaboratorID') == null) {
            $resultJ = json_encode(['success' => 0, 'message' => 'collaborator ID is required.']);
            $this->response->type('json');
            $this->response->body($resultJ);

            return;
        }

        if ($this->request->data('placeID') == null) {
            $resultJ = json_encode(['success' => 0, 'message' => 'Place ID is required.']);
            $this->response->type('json');
            $this->response->body($resultJ);

            return;
        }

        if ($this->request->data('recording') == null) {
            $resultJ = json_encode(['success' => 0, 'message' => 'Empty file name.']);
            $this->response->type('json');
            $this->response->body($resultJ);

            return;
        }

        $recording = $this->request->data('recording');

        $fileName = $recording['name'];
        $md5fileName = md5($recording['name']);
        $tempName = $recording['tmp_name'];

        if ( ! $fileName or ! $tempName) {
            if( ! $tempName) {
                $resultJ = json_encode(['success' => 0, 'message' => 'Invalid temp_name: ' . $tempName]);
                $this->response->type('json');
                $this->response->body($resultJ);

                return;
            }

            $resultJ = json_encode(['success' => 0, 'message' => 'Invalid file name: ' . $fileName]);
            $this->response->type('json');
            $this->response->body($resultJ);

            return;
        }

        $filePath = 'uploads/' . $fileName;

        // Valid audio/video files
        $allowed = [
            'mp4',
            '3gp'
        ];

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        if ( ! $extension || empty($extension) || ! in_array($extension, $allowed)) {
            $resultJ = json_encode(['success' => 0, 'message' => 'Invalid file extension: ' . $extension]);
            $this->response->type('json');
            $this->response->body($resultJ);

            return;
        }

        $md5filePath = 'uploads/' . $md5fileName . '.' . $extension;

        if ( ! move_uploaded_file($tempName, $md5filePath)) {
            $resultJ = json_encode(['success' => 0, 'message' => 'Problem saving file: ' . $tempName]);
            $this->response->type('json');
            $this->response->body($resultJ);

            return;
        }

        $collaboratorID = $this->request->data('collaboratorID');

        $this->loadModel('Collaborators');
        $collaborator = $this->Collaborators->get($collaboratorID);

        $token = new RandomBytesToken();
        $token = $token->toArray();
        $placeID = $this->request->data('placeID');
        $placeName = $this->request->data('placeName');
        $placeCity = $this->request->data('placeCity');
        $rating = $this->request->data('rating');

        $this->loadModel('UserSettings');
        $userSettings = $this->UserSettings->find()->where(['gp_place_id' => $placeID])->first();

        if ($userSettings) {
            $this->loadModel('Users');
            $user = $this->Users->get($userSettings->user_id);
            $data['user_id'] = $user->id;
        }

        $data['collaborator_id'] = $collaborator->id;
        $data['place_id'] = $placeID;
        $data['place_name'] = $placeName;
        $data['place_city'] = $placeCity;
        $data['source'] = 'rv';
        $data['rating'] = $rating ? $rating : '0';
        $data['title'] = 'Revued Video';
        $data['author'] = $collaborator->author_name;;
        $data['body'] = $token['token'];
        $data['author_img'] = '';
        $data['date'] = date('Y-m-d H:i:s');
        $data['status'] = 'active';

        $this->loadModel('Reviews');
        $review = $this->Reviews->newEntity($data);
        $review->collaborator = $collaborator;

        if (isset($user)) {
            $review->user = $user;
        }

        $reviewAsset = $this->Reviews->ReviewAssets->newEntity();
        $reviewAsset->type = $extension;
        $reviewAsset->src = $md5filePath;

        $review->review_assets = [$reviewAsset];

        try {
            $this->Reviews->save($review);
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

        $resultJ = json_encode(['success' => 1, 'message' => 'success']);
        $this->response->type('json');
        $this->response->body($resultJ);

        return $this->response;
    }

    public function capture()
    {
        $token = $this->request->data('token');
        $revuedPass = $this->validateToken($token);

        $this->autoRender = false;

        if ($this->request->data('audio-filename') !== null &&  $this->request->data('video-filename') !== null) {
            echo 'Empty file name.';
            return;
        }

        // do NOT allow empty file names
        if (empty($this->request->data('audio-filename')) && empty($this->request->data('video-filename'))) {
            echo 'Empty file name.';
            return;
        }

        // do NOT allow third party audio uploads
        /*if (isset($_POST['audio-filename']) && strrpos($_POST['audio-filename'], "RecordRTC-") !== 0) {
            echo 'File name must start with "RecordRTC-"';
            return;
        }*/
        // do NOT allow third party video uploads
        /*if (isset($_POST['video-filename']) && strrpos($_POST['video-filename'], "RecordRTC-") !== 0) {
            echo 'File name must start with "RecordRTC-"';
            return;
        }*/

        $fileName = '';
        $tempName = '';
        $file_idx = '';

        if ( ! empty($_FILES['audio-blob'])) {
            $file_idx = 'audio-blob';
            $fileName = $this->request->data('audio-filename');
            $tempName = $_FILES[$file_idx]['tmp_name'];
        } else {
            $file_idx = 'video-blob';
            $fileName = $this->request->data('video-filename');
            $tempName = $_FILES[$file_idx]['tmp_name'];
        }

        if ( ! $fileName or ! $tempName) {
            if( ! $tempName) {
                echo 'Invalid temp_name: '.$tempName;
                return;
            }
            echo 'Invalid file name: '.$fileName;
            return;
        }

        /*
        $upload_max_filesize = return_bytes(ini_get('upload_max_filesize'));
        if ($_FILES[$file_idx]['size'] > $upload_max_filesize)
        echo 'upload_max_filesize exceeded.';
        return;
        }
        $post_max_size = return_bytes(ini_get('post_max_size'));
        if ($_FILES[$file_idx]['size'] > $post_max_size)
        echo 'post_max_size exceeded.';
        return;
        }
        */

        $filePath = 'uploads/' . $fileName;

        // Valid audio/video files
        $allowed = [
            'webm',
            'wav',
            'mp4',
            "mkv",
            'mp3',
            'ogg',
            'mov',
            'MOV'
        ];

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        if ( ! $extension || empty($extension) || ! in_array($extension, $allowed)) {
            echo 'Invalid file extension: '.$extension;
            return;
        }

        if ( ! move_uploaded_file($tempName, $filePath)) {
            echo 'Problem saving file: '.$tempName;
            return;
        }

        $this->loadModel('Users');
        $user = $this->Users->get($revuedPass->foreign_key);

        $rating = $this->request->data('rating');
        $author = $this->request->data('author');

        $data['user_id'] = $user->id;
        $data['source'] = 'rv';
        $data['rating'] = $rating ? $rating : '0';
        $data['title'] = 'Revued Video';
        $data['author'] = $author ? $author : __('anonymous');
        $data['body'] = $token;
        $data['author_img'] = '';
        $data['date'] = date('Y-m-d H:i:s');
        $data['status'] = 'active';

        $this->loadModel('Reviews');
        $review = $this->Reviews->newEntity($data);
        $review->user = $user;

        $reviewAsset = $this->Reviews->ReviewAssets->newEntity();
        $reviewAsset->type = $extension;
        $reviewAsset->src = Router::url("/$filePath");

        $review->review_assets = [$reviewAsset];

        try {
            $this->Reviews->save($review);
        } catch (\PDOException $e) {
        } catch (\Exception $e) {
            Log::write('error',$e->getMessage());
        }

        echo 'success';
        exit;
    }

    private function validateToken($token)
    {
        $config = TableRegistry::exists('Tokens') ? [] : ['className' => 'Alt3\CakeTokens\Model\Table\TokensTable'];
        $this->Tokens = TableRegistry::get('Tokens', $config);

        if ( ! $token) {
            die('Token required');
        }

        $query = $this->Tokens->find('validToken', ['token' => $token]);
        $result = $query->first();

        if ( ! $result) {
            die('Not a valid token');
        }

        return $result;
    }

    public function cloudConvertAlternate()
    {
        $this->autoRender = false;

        $this->loadModel('ReviewAssets');

        $api = new Api(Configure::read('cloudConvertAPIKey'));

        $originalAsset = $this->ReviewAssets->find()->where(['cc_process_id' => $_REQUEST['id']])->first();

        $info = pathinfo($originalAsset->src);
        $filename = $info['filename'] . '.mp4';
        $filePath = 'uploads/' . $filename;

        $reviewAsset = $this->ReviewAssets->newEntity();
        $reviewAsset->review_id = $originalAsset->review_id;
        $reviewAsset->type = 'mp4';
        $reviewAsset->src = $filePath;

        $process = new Process($api, $_REQUEST['url']);
        $process->refresh()->download($filePath);

        $this->ReviewAssets->save($reviewAsset);
    }
}
