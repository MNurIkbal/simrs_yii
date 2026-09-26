<?php

namespace app\components\Services\Penjamin;

class GetInitFilterService extends BaseCurrentService
{
    public function execute()
    {
        $response = $this->restPenjaminAsuransi->get('informasi-pasien-non-bpjs/generate-api');
        $response = json_decode($response->getBody(), true);
        return $response['response'];
    }
}
