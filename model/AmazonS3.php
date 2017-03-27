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
    public function __construct()
    {
        $this->s3 = new S3Client(array(
            'version' => 'latest',
            'region' => 'eu-west-2',
            'credentials' => array(
                'key' => 'abc',
                'secret' => '123'
            )
        ));
    }

    public function upload($file) {
        // this uploads a specified image
        // returns the url of the S3 upload.
        $upload = $this->s3->upload('bucket', 'key-random', fopen($_FILES['userfile']['tmp_name'], 'rb'), 'public-read');
        return $upload->get('ObjectURL');
    }

    public function download($fileKey) {
        $result = $this->s3->getObject(array(
            'Bucket' => 'bucket',
            'key' => $fileKey
        ));

        return $result;
    }
}