<?php

/**
 * @author : Muhammad Rivaldi Irawan
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Extensions\bedah;


class CetakLaporanOperasiAdhy extends \Doco\processes\CetakLaporanOperasiProcess
{  
    public function getRenderView()
    {
        return 'index-adhyaksa';
    }
}
