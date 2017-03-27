<?php
/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 27/03/17
 * Time: 12:51
 */

namespace Wastetopia\Model;
use Aws\S3\Exception\S3Exception;
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
            'region' => 'us-west-2'
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

    public function upload($files) {
        // this uploads a specified image
        // returns the url of the S3 upload.
        try {
            $urls = array();
            foreach($files as $file) {
                $randomKey = "test";
                $upload = $this->s3->upload($this->bucket, $randomKey, fopen($file['tmp_name'], 'rb'), 'public-read');
                array_push($urls, $upload->get('ObjectURL')); // add the url to the array
            }
            return \GuzzleHttp\json_encode($urls);
        } catch (S3Exception $e) {
            return $e->getMessage() . "\n";
        }
    }

    public function download($fileKey) {
        $result = $this->s3->getObject(array(
            'Bucket' => $this->bucket,
            'key' => $fileKey
        ));

        return $result;
    }
}