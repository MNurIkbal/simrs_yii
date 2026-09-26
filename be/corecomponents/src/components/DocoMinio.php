<?php

namespace Doco\components;

use Yii;
use yii\base\Component;
use Aws\S3\S3Client;

class DocoMinio extends Component
{

    /**
     * @var string $host untuk menampung host server minio
     */
    public $host;

    /**
     * @var string $region untuk menampung region yang digunakan
     */
    public $region;

    /**
     * @var array $credentials untuk menampung credential berupa access key, dan secret key yang digunakan
     */
    public $credentials;

    /**
     * @var string $version untuk menampung versi yang digunakan
     */
    public $version = 'latest';

    /**
     * @var string $bucket_name untuk menampung bucket name s3
     */
    public $bucket_name = "tilakalite";

    /**
     * @var array $use_path_style_endpoint untuk menampung versi yang digunakan
     */
    public $use_path_style_endpoint = true;

    /**
     * @var array $us untuk menampung versi yang digunakan
     */
    public $use_signature_version4 = true;

    protected $s3;

    public function init() {
        parent::init();

        $this->s3 = new S3Client([
            'version' => $this->version,
            'region'  => $this->region,
            'endpoint' => $this->host,
            'use_path_style_endpoint' => $this->use_path_style_endpoint,
            'UseSignatureVersion4' => $this->use_signature_version4,
            'credentials' => $this->credentials,
        ]);
    }

    public function getPresignedUrl($objectName, $expiredOn)
    {
        $command = $this->s3->getCommand('GetObject', [
            'Bucket' => $this->bucket_name,
            'Key'    => $objectName,
        ]);
        $presignedRequest = $this->s3->createPresignedRequest($command, $expiredOn);
        $url = (string) $presignedRequest->getUri();

        return $url;
    }

    public function saveFile($objectName, $filename) {
        $command = $this->s3->getCommand('GetObject', [
            'Bucket' => $this->bucket_name,
            'Key'    => $objectName,
            'SaveAs' => $filename,
        ]);

        $this->s3->execute($command);
    }

    public function deleteFile($objectName)
    {
        $command = $this->s3->getCommand('DeleteObject', [
            'Bucket' => $this->bucket_name,
            'Key'    => $objectName,
        ]);
        $this->s3->execute($command);
    }

    public function getListFiles() {
        $command = $this->s3->getCommand('ListObjects', [
            'Bucket' => $this->bucket_name,
        ]);
        $list = $this->s3->execute($command);
        return $list;
    }
}
