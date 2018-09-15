<?php
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Event\Event;
use App\Controller\AppController;
use Httpful\Request;
use SimpleHtmlDom\simple_html_dom;

/**
 * Work Controller
 *
 */
class WorkController extends AppController
{
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->loadModel('Users');
        $this->loadModel('Reviews');
    }

    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow('dailyFetch');
    }

    public function dailyFetch($key = null)
    {
        if ($key == "45457RRGerer11-key") {
            $users = $this->Users->find()->where(['status' => 'active'])->orWhere(['status' => 'trial'])->contain(['UserSettings']);

            foreach ($users as $user) {
                set_time_limit(60);
                if ($user->canFetch()) {
                    $count = $this->fetch($user);
                }

                var_dump($count);

                if ($user->status == 'trial') {
                    $subscription = $this->Users->UserSubscriptions->find()->where(['user_id' => $user->id, 'status' => 'active'])->first();
                    if ($user->trial_days) {
                        $user->trial_days--;
                    } else {
                        if ( ! $subscription) {
                            $user->status = 'suspended';
                        } else {
                            $user->status = 'active';
                        }
                    }

                    $this->Users->save($user);
                }
            }
        }

        exit();
    }

    public function manualFetch()
    {
        $user = $this->Users->get($this->Auth->user('id'), ['contain' => 'UserSettings']);

        if ($this->request->is('post')) {
            if ($user->canFetch()) {
                $count = $this->fetch($user);
                $this->Flash->success(__("$count new reviews!"));
            } else {
                $this->Flash->error(__("Can't fetch right now."));
            }
        } else {
            $this->Flash->error(__("Can't fetch like this"));
        }

        if ($user->role == 'reseller') {
            return $this->redirect(['controller' => 'Dashboard', 'prefix' => 'reseller']);
        } else {
            return $this->redirect(['controller' => 'Dashboard']);
        }
    }

    private function fetch($user)
    {
        $count = 0;
        $errors = [];

        try {
            $avCount = $this->getAv($user);
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        $count += isset($avCount) ? $avCount : 0;

        try {
            $gpCount = $this->getGp($user);
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        $count += isset($gpCount) ? $gpCount : 0;

        try {
            $fbCount = $this->getFb($user);
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        $count += isset($fbCount) ? $fbCount : 0;

        try {
            $ypCount = $this->getYp($user);
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        $count += isset($ypCount) ? $ypCount : 0;


        return $count;
    }

    private function getAv($user)
    {
        $i = 0;

        if ( ! $user->user_setting->av_api_id) {
            return $i;
        }

        $url = sprintf( $user->user_setting->av_api_url, $user->user_setting->av_api_id );
        if ( empty( $url ) ) {
            throw new \Exception('Incorrect Avvo URL.');
        }

        $response = Request::get( $url )->authenticateWith( $user->user_setting->av_api_user, $user->user_setting->av_api_pass )->send();

        if ( ! empty( $response->body ) && $response->code === 200 ) {
            if ( ! empty( $response->body[0]->url ) && $user->user_setting->av_parser ) {
                $full_review_body = array();
                $html_url = preg_replace('/#.*/', '', $response->body[0]->url);
                $html = Request::get( $html_url )
                        ->followRedirects()
                        ->send();
                if ( ! empty( $html->raw_body ) && $html->code === 200 ) {
                    $dom = new simple_html_dom( $html->raw_body );
                    $pager = $dom->find( 'nav.pagination ul > li' );
                    $total_pages = ( count( $pager ) / 2 );
                    if ( $total_pages ) {
                        do {
                            $total_pages--;
                            $data_array = $dom->find( '[itemprop=reviewBody]' );
                            foreach ( $data_array as $item ) {
                                preg_match( '/^js-truncated-review-([0-9]+)$/', $item->attr['id'], $matches );
                                $full_review_body[ $matches[1] ] = trim( $item->plaintext );
                            }
                            $dom->clear();
                            unset( $dom );
                            if ( $total_pages ) {
                                $html = Request::get( $html_url . "?page={$total_pages}" )
                                        ->followRedirects()
                                        ->send();
                                if ( ! empty( $html->raw_body ) && $html->code === 200 ) {
                                    $dom = new simple_html_dom( $html->raw_body );
                                }
                            }
                        } while ( $total_pages && isset( $dom ) );
                    }
                }
            }

            foreach ( $response->body as $item ) {
                $data['user_id'] = $user->id;
                $data['source'] = 'av';
                $data['rating'] = ! empty( $item->overall_rating ) ? $item->overall_rating : 0;
                $data['title']  = ! empty( $item->title ) ? $item->title : '';
                $data['author'] = ! empty( $item->posted_by ) ? $item->posted_by : __( 'anonymous', 'revued' );
                $data['date'] = date( 'Y-m-d H:i:s', strtotime( ! empty( $item->posted_at ) ? $item->posted_at : time() ) );
                if ( isset( $full_review_body[ $item->id ] ) ) {
                    $data['body'] = $full_review_body[ $item->id ];
                } else {
                    $data['body'] = ! empty( $item->body ) ? $item->body : '';
                }

                if ($data['rating'] >= $user->user_setting->autopost_threshold) {
                    $data['status'] = 'active';
                } else {
                    $data['status'] = 'pending';
                }

                $review = $this->Reviews->newEntity($data);
                $review->user = $user;

                try {
                    $this->Reviews->save($review);
                    $i++;
                } catch (\PDOException $e) {
                } catch (\Exception $e) {
                    Log::write(
                        'error',
                        $e->getMessage()
                    );
                }
            }
        } else {
            $this->log('av: ' . trim($response->body) . " ( UserId: " . $user->id . ")");
            throw new \Exception('Unable to fetch reviews from Avvo.');
        }

        return $i;
    }

    private function getGp($user)
    {
        $i = 0;

        if ($user->user_setting->gp_place_id) {
            $response = $this->getGpByPlaceID($user);
        } else {
            $response = $this->getGpByText($user);
        }

        if ( ! empty( $response->body->status ) && $response->body->status == 'OK' && ! empty( $response->body->result->review ) ) {
            foreach ( $response->body->result->review as $item ) {
                $data['user_id'] = $user->id;
                $data['source'] = 'gp';
                $data['rating'] = ! empty( $item->rating ) ? (string) $item->rating : 0;
                $data['title'] = 'google review';
                $data['author'] = ! empty( $item->author_name ) ? (string) $item->author_name : __( 'anonymous' );
                $data['body'] = ! empty( $item->text ) ? (string) $item->text : '';
                $data['author_img'] = ! empty( $item->profile_photo_url ) ? (string) $item->profile_photo_url : '';
                $data['date'] = date( 'Y-m-d H:i:s', ! empty( $item->time ) ? (string) $item->time : time() );

                if ($user->user_setting->autopost_threshold and $data['rating'] >= $user->user_setting->autopost_threshold) {
                    $data['status'] = 'active';
                } else {
                    $data['status'] = 'pending';
                }

                $review = $this->Reviews->newEntity($data);
                $review->user = $user;

                try {
                    $this->Reviews->save($review);
                    $i++;
                } catch (\PDOException $e) {
                } catch (\Exception $e) {
                    Log::write(
                        'error',
                        $e->getMessage()
                    );
                }
            }
        } else {
            $this->log('gp: ' . $response->body->error_message . " ( UserId: " . $user->id . ")");
            throw new \Exception('Unable to fetch reviews from Google.');
        }

        return $i;
    }

    private function getGpByPlaceID($user)
    {
        if ($user->user_setting->gp_api_key) {
            $active_api_key = $user->user_setting->gp_api_key;
        } else {
            $active_api_key = Configure::read('GoogleApiKey');
        }

        $url = "https://maps.googleapis.com/maps/api/place/details/xml?place_id=%s&sensor=true&key=%s";
        $url      = sprintf( $url, $user->user_setting->gp_place_id, $active_api_key );
        $result   = Request::get( $url )->send();

        return $result;
    }

    private function getGpByText($user)
    {
        if ($user->user_setting->gp_api_key) {
            $active_api_key = $user->user_setting->gp_api_key;
        } else {
            $active_api_key = Configure::read('GoogleApiKey');
        }

        $url = sprintf( $user->user_setting->gp_query_url, htmlentities( urlencode( $user->user_setting->gp_plus_page ) ), $active_api_key );

        if ( empty( $url ) ) {
            throw new \Exception('Incorrect Google URL.');
        }

        $response = Request::get( $url )->send();

        if ( ! empty( $response->body->status ) && $response->body->status == 'OVER_QUERY_LIMIT' ) {
            # Try API key number 2
            $active_api_key = $user->user_setting->gp_api_key_2;

            $url = sprintf( $user->user_setting->gp_query_url, htmlentities( urlencode( $user->user_setting->gp_plus_page ) ), $active_api_key );
            if ( empty( $url ) ) {
                throw new \Exception('Incorrect Google URL.');
            }

            $response = Request::get( $url )->send();
        }

        if ( ! empty( $response->body->status ) && $response->body->status == 'ZERO_RESULTS' ) {
            throw new \Exception('No results on Google.');
        }

        $result = null;
        if ( ! empty( $response->body->status ) && $response->body->status == 'OK' && ! empty( $response->body->result ) ) {
            foreach ( $response->body->result as $item ) {
                if ( $item->name == $user->user_setting->gp_plus_page ) {
                    $reference = (string) $item->reference;
                    break;
                }
            }

            if ( ! empty( $reference ) ) {
                $url      = sprintf( $user->user_setting->gp_details_url, $reference, $active_api_key );
                $result   = Request::get( $url )->send();
            }
        } else {
            $this->log('gp: ' . $response->body->error_message . " ( UserId: " . $user->id . ")");
            throw new \Exception('Unable to fetch reviews from Google.');
        }

        return $result;
    }

    private function getFb($user)
    {
        $i = 0;

        if ( ! $user->user_setting->fb_api_access_token) {
            return $i;
        }

        $url = sprintf( $user->user_setting->fb_api_url, $user->user_setting->fb_page_id, $user->user_setting->fb_api_access_token );
        if ( empty( $url ) ) {
            throw new \Exception('Incorrect Facebook URL.');
        }

        $response = Request::get( $url )->send();

        if ( ! empty( $response->body->data ) && $response->code === 200 ) {

            foreach ( $response->body->data as $item ) {
                $data['user_id'] = $user->id;
                $data['source'] = 'fb';
                $data['rating'] = ! empty( $item->rating ) ? $item->rating : 0;
                $data['title'] = 'Facebook Review';
                $data['author'] = ! empty( $item->reviewer->name ) ? $item->reviewer->name : 'anonymous';
                $data['body'] = ! empty( $item->review_text ) ? $item->review_text : '';
                $data['date'] = date( 'Y-m-d H:i:s', strtotime( ! empty( $item->created_time ) ? $item->created_time : time() ) );

                if ($user->user_setting->autopost_threshold and $data['rating'] >= $user->user_setting->autopost_threshold) {
                    $data['status'] = 'active';
                } else {
                    $data['status'] = 'pending';
                }

                $review = $this->Reviews->newEntity($data);
                $review->user = $user;

                try {
                    $this->Reviews->save($review);
                    $i++;
                } catch (\PDOException $e) {
                } catch (\Exception $e) {
                    Log::write(
                        'error',
                        $e->getMessage()
                    );
                }
            }
        } elseif ( ! empty( $response->body->error ) && $response->code === 400 ) {
            $access_token_error = array(
                'message' => $response->body->error->message,
                'error_code' => $response->body->error->code,
            );

            throw new \Exception($response->body->error->message);
        } else {
            throw new \Exception('Unable to fetch reviews from Facebook.');
        }

        return $i;
    }

    private function getYp($user)
    {
        $i = 0;

        if ( ! $user->user_setting->yp_business_id) {
            return $i;
        }

        if ( $user->user_setting->yp_parser_active ) {
            return $this->ypParse($user);
        }

        $data = [
            'grant_type' => 'client_credentials',
            'client_id' => $user->user_setting->yp_app_id,
            'client_secret' => $user->user_setting->yp_app_secret
        ];

        $url = $user->user_setting->yp_url;

        $response = Request::post( $url )
                    ->method( \Httpful\Http::POST ) /* Alternative to Request::post */
                    ->withoutStrictSsl() /* Ease up on some of the SSL checks */
                    ->sendsType( \Httpful\Mime::FORM )
                    ->body( http_build_query( $data ) )
                    ->send();

        if ( ! empty( $response->body->access_token ) && $response->code === 200 ) {
            $url = sprintf( $user->user_setting->yp_url2, $user->user_setting->yp_business_id );

            $response = Request::get( $url )
                        ->addHeader(
                            'Authorization', sprintf( '%s %s', $response->body->token_type, $response->body->access_token )
                        )
                        ->send();

            if ( ! empty( $response->body->reviews ) && $response->code === 200 ) {
                foreach ( $response->body->reviews as $item ) {
                    $data['user_id'] = $user->id;
                    $data['source'] = 'yp';
                    $data['rating'] = ! empty( $item->rating ) ? $item->rating : 0;
                    $data['title'] = 'Yelp Review';
                    $data['date'] = ! empty( $item->time_created ) ? $item->time_created : time();
                    $data['author'] = ! empty( $item->user->name ) ? $item->user->name : 'anonymous';
                    $data['body'] = ! empty( $item->text ) ? $item->text : '';
                    $data['rating_url'] = ! empty( $item->url ) ? $item->url : '';
                    $data['date']  = date( 'Y-m-d H:i:s', strtotime( $data['date'] ) );

                    if ($user->user_setting->autopost_threshold and $data['rating'] >= $user->user_setting->autopost_threshold) {
                        $data['status'] = 'active';
                    } else {
                        $data['status'] = 'pending';
                    }

                    $review = $this->Reviews->newEntity($data);
                    $review->user = $user;

                    try {
                        $this->Reviews->save($review);
                        $i++;
                    } catch (\PDOException $e) {
                    } catch (\Exception $e) {
                        Log::write(
                            'error',
                            $e->getMessage()
                        );
                    }
                }
            } else {
                throw new \Exception('Unable to fetch reviews from Yelp.');
            }
        } else {
            throw new \Exception('Unable to fetch reviews from Yelp.');
        }

        return $i;
    }

    private function ypParse($user)
    {
        $i = 0;

        if ( ! $user->user_setting->yp_business_id) {
            return $i;
        }

        $html_url = sprintf( $user->user_setting->yp_url3, htmlentities( urlencode( $user->user_setting->yp_business_id ) ) );
        if ( empty( $html_url ) ) {
            throw new \Exception('Incorrect Yelp URL.');
        }

        $html = Request::get( $html_url )
                ->followRedirects()
                ->send();

        if ( ! empty( $html->raw_body ) && $html->code === 200 ) {
            $dom = new simple_html_dom( $html->raw_body );
            $total_pages = (int) $user->user_setting->yp_parser_limit;

            do {
                $total_pages --;
                $data_array = $dom->find( '[itemprop=review]' );
                foreach ( $data_array as $item ) {
                    $data['user_id'] = $user->id;
                    $data['source'] = 'yp';
                    $data['rating'] = floor( $item->find( '[itemprop=ratingValue]', 0 )->attr['content'] );
                    $data['title'] = 'Yelp Review';
                    $data['date'] = trim( $item->find( '[itemprop=datePublished]', 0 )->attr['content'] );
                    $data['author'] = trim( $item->find( '[itemprop=author]', 0 )->attr['content'] );
                    $data['body'] = trim( $item->find( '[itemprop=description]', 0 )->plaintext );
                    $data['rating_url'] = $html_url;
                    $data['date'] = date( 'Y-m-d H:i:s', strtotime( $data['date'] ) );

                    if ($user->user_setting->autopost_threshold and $data['rating'] >= $user->user_setting->autopost_threshold) {
                        $data['status'] = 'active';
                    } else {
                        $data['status'] = 'pending';
                    }

                    $review = $this->Reviews->newEntity($data);
                    $review->user = $user;

                    try {
                        $this->Reviews->save($review);
                        $i++;
                    } catch (\PDOException $e) {
                    } catch (\Exception $e) {
                        Log::write('error', $e->getMessage());
                    }
                }

                $new_url = null;
                if ($next = $dom->find( 'div.pagination-links > a.next', 0 )){
                    $new_url = $next->attr['href'];
                }
                $dom->clear();
                unset( $dom );

                if ( $total_pages && $new_url ) {
                    $html = Request::get( $new_url )
                            ->followRedirects()
                            ->send();

                    if ( ! empty( $html->raw_body ) && $html->code === 200 ) {
                        $dom = new simple_html_dom( $html->raw_body );
                    }
                }
            } while ( $total_pages && isset( $dom ) );
        } else {
            throw new \Exception('Unable to fetch reviews from Yelp.');
        }

        return $i;
    }

    public function bulkOperations()
    {
        if ($this->request->is('post')) {
            $operation = $this->request->data('operation');

            switch ($operation) {
                case 'update-status':
                    $status = $this->request->data('status');
                    $reviewArray = $this->request->data('reviews');

                    foreach ($reviewArray as $id) {
                        $review = $this->Reviews->get($id);
                        $review->status = $status;

                        $this->Reviews->save($review);
                    }

                    $this->Flash->success(__('Status Updated.'));
                    break;
            }
        }
    }
}
