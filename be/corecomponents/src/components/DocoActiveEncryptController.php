<?php

namespace Doco\components;

class DocoActiveEncryptController extends DocoActiveController
{
    public $serializer = [
        'class' => '\Doco\components\DocoSerializerEncrypt',
        'collectionEnvelope' => 'data',
    ];
}
