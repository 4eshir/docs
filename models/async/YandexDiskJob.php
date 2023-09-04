<?php

namespace app\models\async;


use app\models\common\Queue;
use Arhitector\Yandex\Disk;
use app\models\components\YandexDiskContext;



class YandexDiskJob extends Queue implements \yii\queue\JobInterface
{
    public $url;
    public $file;
    
    public function execute($queue)
    {
        $disk = new Disk(YandexDiskContext::OAUTH_TOKEN);
        
        $resource = $disk->getResource('/upload/newFile.rar');

        set_time_limit(0);
        $resource->upload('C:\\Users\\work\\Downloads\\Downloads.rar');
    }
}
