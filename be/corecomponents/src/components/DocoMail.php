<?php

namespace Doco\components;
use Yii;
use Doco\models\KonfigSystem;
use yii\base\BootstrapInterface;

class DocoMail implements BootstrapInterface
{
     /**
     * @todo sender mail
     * @author ali.padilah@docotel.com
     */

    public function bootstrap($app)
    {
        $modelKonfig = KonfigSystem::find()->one();
        $app->set('mailer', [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => gethostbyname($modelKonfig->smtp_hostname),
                'username' => $modelKonfig->smtp_username,
                'password' => $modelKonfig->smtp_password,
                'port' => $modelKonfig->smtp_port,
                'encryption' => 'ssl',
                'streamOptions' => [ 
                    'ssl' => [ 
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ],
            ],
        ]);
    }
}
