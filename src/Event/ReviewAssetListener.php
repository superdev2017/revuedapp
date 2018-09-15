<?php
namespace App\Event;

use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Event\EventListenerInterface;
use Cake\Routing\Router;
use Cake\Datasource\ModelAwareTrait;

use FFMpeg;
use \CloudConvert\Api;

class ReviewAssetListener implements EventListenerInterface
{
    use ModelAwareTrait;

    public function implementedEvents()
    {
        return [
            'Model.ReviewAsset.created' => 'processsAlternates',
            'Model.ReviewAsset.createThumbnail' => 'createThumbnail',
            'Model.ReviewAsset.cloudConvert' => 'cloudConvert',
        ];
    }

    public function processsAlternates($event, $entity)
    {
        $localPath = str_replace(Router::url("/"), '', $entity->src);
        $localName = pathinfo($localPath)['filename'];
        $alternate = "uploads/$localName.webm";

        try {
            $ffmpeg = FFMpeg\FFMpeg::create();
            $video = $ffmpeg->open($localPath);
            $video->save(new FFMpeg\Format\Video\WebM(), $alternate);
        } catch (\Exception $e) {
            Log::write('alert', $e);
        }

        $this->loadModel('ReviewAssets');
        $reviewAsset = $this->ReviewAssets->newEntity([
            'review_id' => $entity->review_id,
            'type' => 'webm',
            'src' => Router::url("/$alternate")
        ]);

        $this->ReviewAssets->save($reviewAsset);
    }

    public function createThumbnail($event, $entity)
    {
        $localName = pathinfo($entity->src)['filename'];
        $thumbnail = "uploads/$localName.jpg";

        try {
            $ffmpeg = FFMpeg\FFMpeg::create();
            $video = $ffmpeg->open($entity->src);
            $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(1))->save($thumbnail);
        } catch (\Exception $e) {
            Log::write('alert', $e);
        }

        $this->loadModel('ReviewAssets');
        $reviewAsset = $this->ReviewAssets->newEntity([
            'review_id' => $entity->review_id,
            'type' => 'jpg',
            'src' => $thumbnail
        ]);

        $this->ReviewAssets->save($reviewAsset);
    }

    public function cloudConvert($event, $entity)
    {

        $api = new Api(Configure::read('cloudConvertAPIKey'));

        try {
            $process = $api->createProcess([
                'inputformat' => '3gp',
                'outputformat' => 'mp4',
            ]);

            $urlParts = explode('/', $process->url);
            $entity->cc_process_id = end($urlParts);

            $this->loadModel('ReviewAssets');
            $this->ReviewAssets->save($entity);

            $process->start([
                'outputformat' => 'mp4',
                'converteroptions' => [
                    'quality' => 75,
                ],
                'input' => 'download',
                'file' => Router::url("/".$entity->src, true),
                'callback' => Router::url("/video/cloudConvertAlternate", true)
            ]);
        } catch (\CloudConvert\Exceptions\ApiBadRequestException $e) {
            Log::write('alert', "Something with your request is wrong: " . $e->getMessage());
        } catch (\CloudConvert\Exceptions\ApiConversionFailedException $e) {
            Log::write('alert', "Conversion failed, maybe because of a broken input file: " . $e->getMessage());
        }  catch (\CloudConvert\Exceptions\ApiTemporaryUnavailableException $e) {
            Log::write('alert', "API temporary unavailable: " . $e->getMessage());
            Log::write('alert', "We should retry the conversion in " . $e->retryAfter . " seconds");
        } catch (Exception $e) {
            // network problems, etc..
            Log::write('alert', "Something else went wrong: " . $e->getMessage());
        }
    }
}
