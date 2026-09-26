<?php

/**
 * @author ali.padilah@docotel.com
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\Traits;

use Yii;

use Doco\Repositories\HeaderRepositories;

trait HeaderTrait
{
    public function getHeaderEcoll()
    {
        return (new HeaderRepositories)->getHeaderEcoll();
    }
}
