<?php

/**
 * @project:   Addon list add-on for concrete5
 * 
 * @author     Fabian Bitter
 * @copyright  (C) 2017 Fabian Bitter (www.bitter.de)
 * @version    1.0
 */

namespace Concrete\Package\AddonList\Src;

defined('C5_EXECUTE') or die('Access denied');

use PhpQuery\PhpQuery as phpQuery;
use PhpQuery\PhpQueryObject;
use Package;

class Helpers {

    const secretKey = 'my_simple_secret_key';
    const secretIv = 'my_simple_secret_iv';

    private static $tempFile = null;
    
    /**
     * 
     * @param string $url
     * 
     * @return string
     */
    public static function fetchUrl($url, $postData = null) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        if (is_array($postData)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }

        curl_setopt($ch, CURLOPT_COOKIEJAR, self::getTempFile());
        curl_setopt($ch, CURLOPT_COOKIEFILE, self::getTempFile());

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    /**
     * 
     * @return string
     */
    public static function getTempFile() {
        if (is_null(self::$tempFile)) {
            // 1st try systems temp folder
            if (is_writable(sys_get_temp_dir())) {
                self::$tempFile = tempnam(sys_get_temp_dir(), 'Cookie');
                
                return self::$tempFile;
            } 
            
            // 2nd try cocnrete5 folder
            self::$tempFile = sprintf("%s/cookie.txt", DIR_FILES_UPLOADED_STANDARD);
        }
        
        return self::$tempFile;
    }
    
    /**
     * 
     * @param string $url
     * 
     * @return PhpQueryObject
     */
    public static function fetchDOM($url, $postData = null) {
        $html = self::fetchUrl($url, $postData);

        $doc = phpQuery::newDocument($html);

        return $doc;
    }
    
    /**
     * 
     * @param string $searchString
     * @param string $startToken
     * @param string $endToken
     * 
     * @return string
     */
    public static function getStringBetween($searchString, $startToken, $endToken) {
        $n = strpos($searchString, $startToken);

        if ($n > 0) {
            $searchString = substr($searchString, $n + strlen($startToken));

            $n = strpos($searchString, $endToken);

            if ($n > 0) {
                $searchString = substr($searchString, 0, $n);

                return $searchString;
            }
        }

        return "";
    }
    
    /**
     * 
     * @param string $searchString
     * @param string $endToken
     * 
     * @return string
     */
    public static function getStringTo($searchString, $endToken) {
        $n = strpos($searchString, $endToken);

        if ($n > 0) {
            $searchString = substr($searchString, 0, $n);

            return $searchString;
        }

        return $searchString;
    }
    
    /**
     * 
     * @param string $color
     * @return boolean
     */
    public static function isValidColor($color) {
        $allColors = array('transparent', 'aliceblue', 'antiquewhite', 'aqua', 'aquamarine', 'azure', 'beige', 'bisque', 'black', 'blanchedalmond', 'blue', 'blueviolet', 'brown', 'burlywood', 'cadetblue', 'chartreuse', 'chocolate', 'coral', 'cornflowerblue', 'cornsilk', 'crimson', 'cyan', 'darkblue', 'darkcyan', 'darkgoldenrod', 'darkgray', 'darkgreen', 'darkkhaki', 'darkmagenta', 'darkolivegreen', 'darkorange', 'darkorchid', 'darkred', 'darksalmon', 'darkseagreen', 'darkslateblue', 'darkslategray', 'darkturquoise', 'darkviolet', 'deeppink', 'deepskyblue', 'dimgray', 'dodgerblue', 'firebrick', 'floralwhite', 'forestgreen', 'fuchsia', 'gainsboro', 'ghostwhite', 'gold', 'goldenrod', 'gray', 'green', 'greenyellow', 'honeydew', 'hotpink', 'indianred', 'indigo', 'ivory', 'khaki', 'lavender', 'lavenderblush', 'lawngreen', 'lemonchiffon', 'lightblue', 'lightcoral', 'lightcyan', 'lightgoldenrodyellow', 'lightgreen', 'lightgrey', 'lightpink', 'lightsalmon', 'lightseagreen', 'lightskyblue', 'lightslategray', 'lightsteelblue', 'lightyellow', 'lime', 'limegreen', 'linen', 'magenta', 'maroon', 'mediumaquamarine', 'mediumblue', 'mediumorchid', 'mediumpurple', 'mediumseagreen', 'mediumslateblue', 'mediumspringgreen', 'mediumturquoise', 'mediumvioletred', 'midnightblue', 'mintcream', 'mistyrose', 'moccasin', 'navajowhite', 'navy', 'oldlace', 'olive', 'olivedrab', 'orange', 'orangered', 'orchid', 'palegoldenrod', 'palegreen', 'paleturquoise', 'palevioletred', 'papayawhip', 'peachpuff', 'peru', 'pink', 'plum', 'powderblue', 'purple', 'red', 'rosybrown', 'royalblue', 'saddlebrown', 'salmon', 'sandybrown', 'seagreen', 'seashell', 'sienna', 'silver', 'skyblue', 'slateblue', 'slategray', 'snow', 'springgreen', 'steelblue', 'tan', 'teal', 'thistle', 'tomato', 'turquoise', 'violet', 'wheat', 'white', 'whitesmoke', 'yellow', 'yellowgreen');

        if (in_array(strtolower($color), $allColors)) {
            return true;
        } else if (preg_match('/^#[a-f0-9]{6}$/i', $color)) {
            return true;
        } else if (preg_match('/^[a-f0-9]{6}$/i', $color)) {
            return true;
        }

        return false;
    }

    /**
     * 
     * @param string $imageFile
     * 
     * @return string
     */
    public static function getImageUrl($imageFile) {
        return Package::getByHandle('addon_list')->getRelativePath() . '/images/' . $imageFile;
    }
    
    /**
     * 
     * @param string $string
     * @return string
     */
    public static function encryptString($string) {
        if (function_exists("openssl_encrypt")) {
            $key = hash('sha256', self::secretKey);
            $iv = substr(hash('sha256', self::secretIv), 0, 16);

            return base64_encode(openssl_encrypt($string, "AES-256-CBC", $key, 0, $iv));
        } else {
            return $string;
        }
    }
    
    /**
     * 
     * @param string $string
     * @return string
     */
    public static function decryptString($string) {
        if (function_exists("openssl_decrypt")) {
            $key = hash('sha256', self::secretKey);
            $iv = substr(hash('sha256', self::secretIv), 0, 16);

            return openssl_decrypt(base64_decode($string), "AES-256-CBC", $key, 0, $iv);
        } else {
            return $string;
        }
    }
}
