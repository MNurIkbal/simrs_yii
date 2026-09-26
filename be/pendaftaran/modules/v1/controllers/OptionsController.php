<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;

class OptionsController extends Controller
{
    public $serializer = [
        'class' => '\app\components\DocoSerializer',
        'collectionEnvelope' => 'data',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['rateLimiter']['enableRateLimitHeaders'] = true;
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON; // Force XML Header to Json Response
        $behaviors['contentNegotiator']['formats']['application/xml'] = Response::FORMAT_JSON; // Force XML Header to Json Response
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }
    
    public function actionGetPages() 
    {
        $controllerlist = [];
        if ($handle = opendir('../modules/v1/controllers')) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && substr($file, strrpos($file, '.') - 10) == 'Controller.php') {
                    $controllerlist[] = $file;
                }
            }
            closedir($handle);
        }
        asort($controllerlist);
        $fulllist = [];

        // If Child of ActiveController
        foreach ($controllerlist as $controller) {
            if (substr($controller, 0, -4) != "AuthController") {
                $fulllist[substr($controller, 0, -4)] = [];
                $content = file_get_contents('../modules/v1/controllers/' . $controller, "r");
                if (preg_match("/( )*public( )+.modelClass( )*\=/", $content)) {
                    if ( ! in_array("index", $fulllist[substr($controller, 0, -4)]))
                        $fulllist[substr($controller, 0, -4)][] = "index";
                    if ( ! in_array("view", $fulllist[substr($controller, 0, -4)]))
                        $fulllist[substr($controller, 0, -4)][] = "view";
                    if ( ! in_array("create", $fulllist[substr($controller, 0, -4)]))
                        $fulllist[substr($controller, 0, -4)][] = "create";
                    if ( ! in_array("update", $fulllist[substr($controller, 0, -4)]))
                        $fulllist[substr($controller, 0, -4)][] = "update";
                    if ( ! in_array("delete", $fulllist[substr($controller, 0, -4)]))
                        $fulllist[substr($controller, 0, -4)][] = "delete";
                    if ( ! in_array("options", $fulllist[substr($controller, 0, -4)]))
                        $fulllist[substr($controller, 0, -4)][] = "options";
                }
            }
        }

        // Extract All Action Method
        foreach ($controllerlist as $controller) {
            if (substr($controller, 0, -4) != "AuthController") {
                $handle = fopen('../modules/v1/controllers/' . $controller, "r");
                if ($handle) {
                    while (($line = fgets($handle)) !== false) {
                        if (preg_match('/public function action(.*?)\(/', $line, $display)) {
                            if (strlen($display[1]) > 2) {
                                $string = preg_replace("/(?<=\\w)(?=[A-Z])/","-$1", $display[1]); 
                                $string = strtolower($string);
                                if ( ! in_array(strtolower($string), $fulllist[substr($controller, 0, -4)]))
                                    $fulllist[substr($controller, 0, -4)][] = strtolower($string);
                            }
                        }
                    }
                }
                fclose($handle);
            }
        }
        return $fulllist;
    }
}