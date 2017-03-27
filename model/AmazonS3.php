<?php
/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 27/03/17
 * Time: 12:51
 */

namespace Wastetopia\Model;
use Aws\S3\S3Client;

class AmazonS3
{
    const LENGTH = 20;

    public function __construct()
    {
        $this->bucket = $_ENV['AWS_BUCKET'];
        $this->awsRegion = $_ENV['AWS_REGION'];
        $this->s3 = new S3Client(array(
            'version' => 'latest',
            'region' => $this->awsRegion,
            'credentials' => array(
                'key' => $_ENV['AWS_ACCESS_KEY_ID'],
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY']
            )
        ));
    }

    private function randomString($length) {
        $output = "";
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for($i = 0; $i < $length; $i++) {
            $output = $output . substr($chars, rand(0, strlen($chars)),1);
        }
        return $output;
    }

    public function upload($file) {
        // this uploads a specified image
        // returns the url of the S3 upload.
        $randomKey = $this->randomString(self::LENGTH);
        $upload = $this->s3->upload($this->bucket, $randomKey, fopen($_FILES['userfile']['tmp_name'], 'rb'), 'public-read');
        return $upload->get('ObjectURL');
    }

    public function download($fileKey) {
        $result = $this->s3->getObject(array(
            'Bucket' => $this->bucket,
            'key' => $fileKey
        ));

        return $result;
    }
}