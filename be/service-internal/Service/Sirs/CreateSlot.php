<?php

namespace Integrasi\Service\Sirs;

use Integrasi\Components\DocoConstants;
use Integrasi\Service\Sirs\BussinesLogic\Slot;

class CreateSlot extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        return json_encode([
            'service' => 'Sirs-CreateSlot',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => (new Slot)->execute($this->user_identity, $this->id, [
                'state' => DocoConstants::STATE_CREATE,
            ])
        ]);
    }

}