<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Helpers\SimpleImage;
use Intervention\Image\Facades\Image;

abstract class Controller extends BaseController {

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    public function __construct() {
        
    }

    function resizeImage($image, $width = 300, $height = 180) {
        if (!empty($image) && !empty($width)) {
            $arr = explode('.', $image);
            $ext = end($arr);
            $imgs = explode('/', $arr[0]);
            $image_name = end($imgs);
            array_pop($imgs);
            $image_tmp = Image::make($image);
            $image_tmp->resize($width, $height);

            $dir = implode('/', $imgs) . '/thumbnails';
            $new_image = $dir . '/' . $image_name . '_' . $width . 'x' . $height . '.' . $ext;
            if (!file_exists($new_image) && !is_dir($dir)) {
                mkdir($dir);
            }
//            $image_tmp->move($new_image);
            $image_tmp->save($new_image);
        }
    }

    function resizeImageOld_1($image, $width = 300, $height = 180) {
        if (!empty($image) && !empty($width)) {
            $objectImage = new SimpleImage();
            $arr = explode('.', $image);
            $ext = end($arr);
            $imgs = explode('/', $arr[0]);
            $image_name = end($imgs);
            array_pop($imgs); //remove image name
            $objectImage->load($image);
            $objectImage->resize($width, $height);

            $dir = implode('/', $imgs) . '/thumbnails';
            $new_image = $dir . '/' . $image_name . '_' . $width . 'x' . $height . '.' . $ext;
            if (!file_exists($new_image) && !is_dir($dir)) {
                mkdir($dir);
            }

            $objectImage->save($new_image);
        }
    }

    function resizeImage_old($image_url, $thumb_w = 300, $thumb_h = 180) {
        if (!empty($image_url) && !empty($thumb_w)) {
            $arr = explode('.', $image_url);
            $ext = end($arr);
            $imgs = explode('/', $arr[0]);
            $image_name = end($imgs);
            array_pop($imgs); //remove image name

            $format = '';
            if (preg_match("/.jpg/i", "$image_url")) {
                $format = 'image/jpeg';
                header('Content-type: image/jpeg');
            }
            if (preg_match("/.gif/i", "$image_url")) {
                $format = 'image/gif';
                header('Content-type: image/gif');
            }
            if (preg_match("/.png/i", "$image_url")) {
                $format = 'image/png';
                header('Content-type: image/png');
            }
            if ($format != '') {
                switch ($format) {
                    case 'image/jpeg':
                        $source = imagecreatefromjpeg($image_url);
                        break;
                    case 'image/gif';
                        $source = imagecreatefromgif($image_url);
                        break;
                    case 'image/png';
                        $source = imagecreatefrompng($image_url);
                        break;
                }
                list($width, $height) = getimagesize($image_url);


                $thumb = imagecreatetruecolor($thumb_w, $thumb_h);
                imagesavealpha($thumb, TRUE);
                imagealphablending($thumb, false);
                $white = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
                imagefill($thumb, 0, 0, $white);

                imagecopyresized($thumb, $source, 0, 0, 0, 0, $thumb_w, $thumb_h, $width, $height);

                $dir = implode('/', $imgs) . '/thumbnails';
                $new_image = $dir . '/' . $image_name . '_' . $thumb_w . 'x' . $thumb_h . '.' . $ext;

                if (!file_exists($new_image) && !is_dir($dir)) {
                    mkdir($dir);
                }
                imagefilter($thumb, IMG_FILTER_CONTRAST, 5);
                imagepng($thumb, $new_image);
            }
            imagedestroy($thumb);
        }
    }

}
