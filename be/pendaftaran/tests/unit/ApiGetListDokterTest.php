<?php
use GuzzleHttp\Exception\RequestException;

class ApiGetListDokterTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    static protected $_headers;
    
    protected function _before()
    {
        self::$_headers = Yii::$app->request->headers;
    }

    protected function _after()
    {
    }

    // tests
    public function testApiGetListDoctor()
    {
        $ruangan_id = 937;
        $restPend = Yii::$app->docoRest->pendaftaran;
        $payload['headers'] = ['Content-Type' => 'application/json',
                                'X-Owner' => 'YmRnLXNpbXJzLWRvY28tZGV2ZWxvcG1lbnQ', 
                                'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiYWNjZXNzX3Rva2VuIjoiMTAwLXRva2VuIiwianRpIjoiNjIyOGMwNjc0MDMxMGJiZDc5NmE0MTQ2ZmJiZDQ2NTE4NWNkMTdiOGIwNWEzNmU4ZGMzYWUzYmNkN2I3NTdiNCIsImlzX21vYmlsZSI6MCwiaXNfYWxsX2V4cGVydGlzZV9sYWIiOnRydWUsInNpZ25hdHVyZV9wYXRoIjoiZHIgSGFydW4gUm9zaWRpIC0gMTA5NC5naWYifQ.V_hPVLy9J89Rs-37gDhuMjgOJvR8mJbNZAoxIsdqNME'
                            ];
        
        $request = $restPend->get('api/get-list-doctor?ruangan_id='.$ruangan_id, $payload);
        $response = json_decode($request->getBody(),true);
        if ($response['metadata']['status'] == 200){
            $hasil = true;
        }
        $this->assertTrue($hasil);
    }

    public function testApiGetListDoctorFalse()
    {
        $hasil = false;
        $ruangan_id = null;
        $restPend = Yii::$app->docoRest->pendaftaran;
        $payload['headers'] = ['Content-Type' => 'application/json',
                                'X-Owner' => 'YmRnLXNpbXJzLWRvY28tZGV2ZWxvcG1lbnQ', 
                                'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiYWNjZXNzX3Rva2VuIjoiMTAwLXRva2VuIiwianRpIjoiNjIyOGMwNjc0MDMxMGJiZDc5NmE0MTQ2ZmJiZDQ2NTE4NWNkMTdiOGIwNWEzNmU4ZGMzYWUzYmNkN2I3NTdiNCIsImlzX21vYmlsZSI6MCwiaXNfYWxsX2V4cGVydGlzZV9sYWIiOnRydWUsInNpZ25hdHVyZV9wYXRoIjoiZHIgSGFydW4gUm9zaWRpIC0gMTA5NC5naWYifQ.V_hPVLy9J89Rs-37gDhuMjgOJvR8mJbNZAoxIsdqNME'
                            ];
        
        $request = $restPend->get('api/get-list-doctor?ruangan_id='.$ruangan_id, $payload);
        $response = json_decode($request->getBody(),true);
        if ($response['metadata']['status'] == 200){
            $hasil = true;
        }
        $this->assertFalse($hasil);
    }
}