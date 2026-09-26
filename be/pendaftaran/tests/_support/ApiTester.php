<?php

use Codeception\Util\HttpCode;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class ApiTester extends \Codeception\Actor
{
    use _generated\ApiTesterActions;

    private static $adminToken = null;
    protected $prev_payload;
    /**
    * Define custom actions here
    */

    private function getAdminToken()
    {
        if (self::$adminToken) {
            return self::$adminToken;
        }
        $this->generateAdminToken();
        return self::$adminToken;
    }

    /**
    * Define custom header here or use default header by superadmin
    */
    public function setHeader($Array = [])
    {
        $I = $this;
        if (empty($Array)) {
            $I->haveHttpHeader('Content-Type', 'application/json');
            $I->haveHttpHeader('Authorization', $I->getAdminToken());
            $I->haveHttpHeader('X-Owner', 'YmRnLXNpbXJzLWRvY28tZGV2ZWxvcG1lbnQ');
        } else {
            foreach ($Array as $key => $value) {
                $I->haveHttpHeader($key, $value);
            }
        }
        return $this;
    }

    private function generateAdminToken()
    {
        $I = $this;
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPost($I->getConfig('url_login'),[
            'username' => $I->getConfig('username'),
            'password' => $I->getConfig('password'),
        ]);
        $I->seeResponseCodeIs(HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $token = json_decode($I->grabResponse())->response->access_token;
        self::$adminToken = "Bearer {$token}";
    }

    /**
    * Define custom header here or use default header by superadmin
    * Params files hanya untuk PUT, PATCH, DELETE
    */
    public function generateApi($methode, $url, $payload = null, $expected_response = [], $files = null)
    {
        $I = $this;
        $methode_upper = strtoupper($methode);
        $response = $I->validateApi($methode_upper, $url);
        if (!empty($response) && (($response['metadata']['status'] != \Codeception\Util\HttpCode::OK)||$response['metadata']['status'] != ''.\Codeception\Util\HttpCode::OK.'')) {
            return $response;
        }
        // perlu improve agar bisa dicustom lagi headernya karena ini set default saat login superadmin belum mencoba menggunakan mockup api
        $I->setHeader();
        $I->sendingApi($methode, $url, $payload, $files);
        $this->checkingResponse($methode, $url, $payload, $expected_response, $files);
        // perlu improve karena bisa saja response berbeda beda
        $this->prev_payload = json_decode($I->grabResponse())->response;
        var_dump($I->grabResponse());
    }

    public function generateNextApi($methode, $url, $expected_response = [], $files = null)
    {
        $I = $this;
        $methode_upper = strtoupper($methode);
        var_dump($methode_upper);
        $response = $I->validateApi($methode_upper, $url);
        if (!empty($response) && (($response['metadata']['status'] != \Codeception\Util\HttpCode::OK)||$response['metadata']['status'] != ''.\Codeception\Util\HttpCode::OK.'')) {
            return $response;
        }
        $I->setHeader();
        $I->sendingApi($methode_upper, $url, $this->prev_payload, $files);
        $this->checkingResponse($methode, $url, $this->prev_payload, $expected_response, $files);
        var_dump($I->grabResponse());
    }

    private function sendingApi($methode, $url, $payload = null, $files = null)
    {
        $I = $this;
        $methode_upper = strtoupper($methode);
        switch ($methode_upper) {
            case 'GET':
                $I->sendGET($url, $payload);
                break;
            case 'POST':
                $I->sendPOST($url, $payload);
                break;
            case 'PUT':
                $I->sendPUT($url, $payload, $files);
                break;
            case 'PATCH':
                $I->sendPATCH($url, $payload, $files);
                break;
            case 'DELETE':
                $I->sendDELETE($url, $payload, $files);
                break;
            default:
                $I->getResponse(['metadata' => ['status' => \Codeception\Util\HttpCode::NOT_FOUND,
                'message' => 'Methode tidak valid',],
                'response' => [],
                ]);
                break;
        }
    }

    private function checkingResponse($methode = null, $url = null, $payload = null, $expected_response = [], $files = null)
    {
        $I = $this;
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        // $I->seeCurrentUrlMatches($I->getConfig('base_url').''.$url);
        // $I->seeInCurrentUrl($I->getConfig('base_url').''.$url.'/');
        // var_dump($I->grabResponse());
        // $I->seeResponseMatchesJsonType($expected_response);
        $I->seeResponseContainsJson($expected_response);
        // $I->seeResponseContainsJson(json_decode(json_encode($expected_response)));
        // $I->seeResponseJsonMatchesXpath(json_encode($expected_response));
        // $I->seeResponseContainsJson(json_encode($expected_response));
    }
}
