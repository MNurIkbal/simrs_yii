<?php

namespace app\modules\v1\controllers;

use Yii;
use app\models\LoginForm;
use app\models\Loginpemakai;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use yii\rest\Controller;
use yii\web\Response;

class AuthController extends Controller
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

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["generate-access-token"] = ["POST"];
        return $verbs;
    }

    public function actionGetToken()
    {
        $token = Null;
        $signer = new Sha256();
        $model = new LoginForm();
        $post = Yii::$app->request->post();
        $loginPost["LoginForm"] = $post;
        
        $access_token = null;

        if ($model->load($loginPost) && $model->login()) {
            $user = Loginpemakai::find()->where(['nama_pemakai'=>$post["username"]])->one(); 
            $token = (New Builder())
                ->setHeader("alg", "HS256") // Configures the expiration time of the token (exp claim)
                ->setHeader("typ", "JWT") // Configures the expiration time of the token (exp claim)
                ->set('id', $user->loginpemakai_id) // Configures a new claim, called "uid"
                ->set('access_token', $user->additional_data) // Configures a new claim, called "uid"
                ->sign($signer, 'secret') // creates a signature using "testing" as key
                ->getToken(); // Retrieves the generated token

            ob_start();
            echo $token; // The string representation of the object is a JWT string (pretty easy, right?)
            $token = ob_get_clean();
        } else {
            $user = new Loginpemakai; 
        }
        
        return [
            "uid" => $user->loginpemakai_id,
            "access_token" => $token
        ];
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
        // $string = preg_replace('/(?<=\\w)(?=[A-Z])/',"_$1", $string);
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
