<?php

use app\modules\v1\controllers\SubSpesialisController;

class ApiSubspesialisTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $restMaster;
    protected $payload;
    
    protected function _before()
    {
        $this->restMaster =  Yii::$app->docoRest->master;
        $this->payload['headers'] = [
            'Content-Type' => 'application/json',
            'X-Owner' => 'YmRnLXNpbXJzLWRvY28tZGV2ZWxvcG1lbnQ', 
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiYWNjZXNzX3Rva2VuIjoiMTAwLXRva2VuIiwianRpIjoiNjIyOGMwNjc0MDMxMGJiZDc5NmE0MTQ2ZmJiZDQ2NTE4NWNkMTdiOGIwNWEzNmU4ZGMzYWUzYmNkN2I3NTdiNCIsImlzX21vYmlsZSI6MCwiaXNfYWxsX2V4cGVydGlzZV9sYWIiOnRydWUsInNpZ25hdHVyZV9wYXRoIjoiZHIgSGFydW4gUm9zaWRpIC0gMTA5NC5naWYifQ.V_hPVLy9J89Rs-37gDhuMjgOJvR8mJbNZAoxIsdqNME'
        ];
    }

    protected function _after()
    {
    }

    public function testGetDataSubspesialis()
    {
        $request = $this->restMaster->get('sub-spesialis/data-api', $this->payload);
        $response = json_decode($request->getBody(),true);
        if ($response['metadata']['status'] == 200){
            $hasil = true;
        }
        $this->assertTrue($hasil);
    }

    public function testFilterRuanganById()
    {
        $ruanganId = 936;
        $this->payload['query'] = [
            'ruangan_id' => $ruanganId
        ];
        
        $request = $this->restMaster->get('sub-spesialis/data-api', $this->payload);
        $response = json_decode($request->getBody(),true);

        if ($response['metadata']['status'] == 200 && !isset($response['response']['metadata'])){
            foreach($response['response'] as $value) {
                if($value['ruangan_id'] == $ruanganId) {
                    $hasil = true;
                    break;
                } else {
                    break;
                }
            }
        }
        $this->assertTrue($hasil);
    }

    public function testDataNotFound()
    {
        $this->payload['query'] = [
            'ruangan_id' => 999999
        ];
        
        $request = $this->restMaster->get('sub-spesialis/data-api', $this->payload);
        $response = json_decode($request->getBody(),true);
        $hasil = $code = null;
        if ($response['metadata']['status'] == 200){
            $code = $response['response']['meta']['code'];
            $hasil = $response['response']['message'];
        }
        $this->assertEquals('Data Tidak Ditemukan', $hasil);
        $this->assertEquals(201, $code);
    }
}