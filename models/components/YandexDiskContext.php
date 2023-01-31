<?php


namespace app\models\components;

use Arhitector\Yandex\Disk;


class YandexDiskContext
{
    const OAUTH_TOKEN = "y0_AgAEA7qjlWFzAAhnoAAAAADOk9pSLFsGZe59SkioZ4hPt40FKeSqN50";

    static public function CheckSameFile($filepath)
    {
        $disk = new Disk(YandexDiskContext::$oauth_token);

        $resource = $disk->getResource($filepath);

        return $resource->has();
    }

    static public function GetFileFromDisk($filepath, $filename)
    {
        $disk = new Disk(YandexDiskContext::OAUTH_TOKEN);

        $resource = $disk->getResource($filepath.$filename);

        $fp = fopen('php://output', 'r');

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $resource->size);

        $resource->download($fp);

        fseek($fp, 0);
    }

    static public function UploadFileOnDisk($disk_filepath, $local_filepath)
    {
        $disk = new Disk(YandexDiskContext::OAUTH_TOKEN);
        
        $resource = $disk->getResource($disk_filepath);

        $resource->upload($local_filepath);
    }

    static public function DeleteFileFromDisk($filepath)
    {
        $disk = new Disk(YandexDiskContext::OAUTH_TOKEN);

        $resource = $disk->getResource($filepath);

        return $resource->delete();
    }
}