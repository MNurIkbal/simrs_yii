<?php

class RuanganTest extends \Codeception\Test\Unit
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
    public function testGetRuanganApiTrue()
    {
        $ruangan_nama = 'Test Foto';
        $is_online = false;
        $_restMaster = Yii::$app->docoRest->master;
        $payload['headers'] = ['Content-Type' => 'application/json',
                                'X-Owner' => 'YmRnLXNpbXJzLWRvY28tZGV2ZWxvcG1lbnQ', 
                                'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiYWNjZXNzX3Rva2VuIjoiMTAwLXRva2VuIiwianRpIjoiNjIyOGMwNjc0MDMxMGJiZDc5NmE0MTQ2ZmJiZDQ2NTE4NWNkMTdiOGIwNWEzNmU4ZGMzYWUzYmNkN2I3NTdiNCIsImlzX21vYmlsZSI6MCwiaXNfYWxsX2V4cGVydGlzZV9sYWIiOnRydWUsInNpZ25hdHVyZV9wYXRoIjoiZHIgSGFydW4gUm9zaWRpIC0gMTA5NC5naWYifQ.V_hPVLy9J89Rs-37gDhuMjgOJvR8mJbNZAoxIsdqNME'
                            ];
        
        $request = $_restMaster->get('ruangan?ruangan_nama='.$ruangan_nama.'&is_online='.$is_online, $payload);
        $response = json_decode($request->getBody(),true);
        if ($response['metadata']['status'] == 200){
            $hasil = true;
        }
        $this->assertTrue($hasil);

    }

    public function testGetRuanganApiFalse()
    {
        $ruangan_nama = true;
        $is_online = '123';
        $_restMaster = Yii::$app->docoRest->master;
        $payload['headers'] = ['Content-Type' => 'application/json',
                                'X-Owner' => 'YmRnLXNpbXJzLWRvY28tZGV2ZWxvcG1lbnQ', 
                                'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiYWNjZXNzX3Rva2VuIjoiMTAwLXRva2VuIiwianRpIjoiNjIyOGMwNjc0MDMxMGJiZDc5NmE0MTQ2ZmJiZDQ2NTE4NWNkMTdiOGIwNWEzNmU4ZGMzYWUzYmNkN2I3NTdiNCIsImlzX21vYmlsZSI6MCwiaXNfYWxsX2V4cGVydGlzZV9sYWIiOnRydWUsInNpZ25hdHVyZV9wYXRoIjoiZHIgSGFydW4gUm9zaWRpIC0gMTA5NC5naWYifQ.V_hPVLy9J89Rs-37gDhuMjgOJvR8mJbNZAoxIsdqNME'
                            ];
        
        $request = $_restMaster->get('ruangan?ruangan_nama='.$ruangan_nama.'&is_online='.$is_online, $payload);
        $response = json_decode($request->getBody(),true);
        if ($response['metadata']['status'] == 200){
            $hasil = true;
        }
        $this->assertFalse($hasil);

    }
}