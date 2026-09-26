
Untuk Service Doco Listener doco-listen.service
--------------------------

```php

[Unit]
Description=Docotel Listen Proccessing

[Service]
Type=simple
ExecStart=/usr/bin/php7.2 -d "default_socket_timeout=-1" -f /home/vagrant/code/micro-service/service-internal/Bin/doco-listen.php
Restart=always

[Install]
WantedBy=multi-user.target

Save dengan nama doco-listen pada /etc/systemd/system/doco-listen.service
Terus aktifkan service dengan perintah sudo systemctl enable doco-listen.service
```

