<?php

/**
 * @author : Muhammad Rivaldi Irawan
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\processes;


class CetakLaporanOperasiProcess extends \Doco\components\DocoBaseProcessExtension
{  

    public function getRenderView()
    {
        return 'index';
    }

    protected function processFlow()
    {
        return $this->getRenderView();
    }
}
