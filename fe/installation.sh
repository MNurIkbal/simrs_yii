#!/bin/bash
#author : ali.padilah@docotel.com
#date : 9/6/2018

echo "-----------------------------------"
echo "Initial backend core untuk newsimrs"
echo "-----------------------------------"


if [ "$(whoami)" != 'root' ]; then
    echo "Anda harus menjalankan script ini sebagai pengguna root"
    exit 1;
fi


read -p "Jenis deploy (1=Master/2=Staging/3=Development/4=Lainnya/5=skip) : " jd


if [ $jd == 1 ]
then
	branch="master"
elif [ $jd == 2 ]
then
	branch="staging"
elif [ $jd == 3 ]
then
	branch="development"
elif [ $jd == 4 ]
then
   read -p "Nama branch : " branch
else 
	echo "lewati proses branching dan update"
fi

if [[ $jd == 1 ]] || [[ $jd == 2 ]] || [[ $jd == 3 ]] || [[ $jd == 4 ]]
then
	git checkout $branch
	git pull origin $branch

	composer install

	cd web
	bower install --allow-root
	cd ..

	cd nodejs
	npm install
	cd ..


fi

## Generate Vhost
read -p "Generate vhost server untuk Frontend (y/n): " vh
if [[ $vh == "y" ]] || [[ $vh == "Y" ]]
then
	read -p "Jenis Server (1=Apache/2=Nginx) : " js

	if [ $js == 1 ]
	then
		read -p "Masukan nama server (tanpa www) : " servn
		read -p "Masukan CNAME (contoh :www atau dev untuk dev.website.com) : " cname
		read -p "Masukan direktori yang akan di gunakan (e.g. : /var/www, tanpa "/" di akhir): " dir
		## read -p "Masukan nama user yang akan di gunakan (e.g. : root) : " usr
		read -p "Masukan listen port(e.g. : 8081): " port

		echo "#### $cname $servn
				listen $port
				
				<VirtualHost *:$port>
				ServerName $servn
				ServerAlias $cname.$servn
				DocumentRoot $dir/web
				<Directory $dir/web>
					Options Indexes FollowSymLinks MultiViews
					AllowOverride All
					Order allow,deny
					Allow from all
					Require all granted
				</Directory>
				ErrorLog /var/log/$servn-error.log
				</VirtualHost>" > /etc/httpd/conf.d/$cname_$servn.conf

		if ! echo -e /etc/httpd/conf.d/$cname_$servn.conf; then
		echo "Virtual host gagal di buat !"
		else
		echo "Virtual host berhasil di buat"
		fi

		echo "127.0.0.1 $cname.$servn" >> /etc/hosts
		if [ "$cname.$servn" != "$servn" ]; then
			echo "127.0.0.1 $alias" >> /etc/hosts
		fi

		echo "Test konfigurasi"
		service httpd configtest
		echo "Apakah Anda ingin me-restart server [y/n]? "
		read q

		if [[ "${q}" == "Y" ]] || [[ "${q}" == "y" ]]; then
			service httpd restart
		fi

	else

		sitesEnable='/etc/nginx/sites-enabled/'
		sitesAvailable='/etc/nginx/sites-available/'
		owner=$(who am i | awk '{print $1}')
		
		read -p "Masukan nama server (tanpa www) : " servn
		read -p "Masukan CNAME (contoh :www atau dev untuk dev.website.com) : " cname
		read -p "Masukan direktori yang akan di gunakan (e.g. : /var/www, tanpa "/" di akhir): " dir
		## read -p "Masukan nama user yang akan di gunakan (e.g. : root) : " usr
		read -p "Masukan listen port(e.g. : 8081): " port

		if ! echo 'server {
			    listen '"$port"';
			    server_name '"$cname"'.'"$servn"';
			    root '"$dir"'/web;

			    index index.html index.htm index.php;

			    charset utf-8;

			    location / {
			        try_files $uri $uri/ /index.php$is_args$args;
			    }

			    location = /favicon.ico { access_log off; log_not_found off; }
			    location = /robots.txt  { access_log off; log_not_found off; }

			    access_log off;
			    error_log  /var/log/'"$servn"'-error.log error;

			    sendfile off;

			    client_max_body_size 100m;

			    location ~ \.php$ {
			        fastcgi_split_path_info ^(.+\.php)(/.+)$;
			        fastcgi_pass unix:/var/run/php5-fpm.sock;
			        fastcgi_index index.php;
			        include fastcgi_params;
			        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
			        fastcgi_intercept_errors off;
			        fastcgi_buffer_size 16k;
			        fastcgi_buffers 4 16k;
			    }

			    location ~ /\.ht {
			        deny all;
			    }
			}' > $sitesAvailable$cname
		then
			echo "Virtual host gagal di buat !"
			exit;
		else
			echo "Virtual host berhasil di buat"
		fi

		echo "127.0.0.1 $cname.$servn" >> /etc/hosts
		if [ "$cname.$servn" != "$servn" ]; then
			echo "127.0.0.1 $alias" >> /etc/hosts
		fi

		if [ "$owner" == "" ]; then
			chown -R $(whoami):www-data $dir
		else
			chown -R $owner:www-data $dir
		fi

		### enable website
		ln -s $sitesAvailable$cname $sitesEnable$cname

		### restart Nginx
		echo "Apakah Anda ingin me-restart server [y/n]? "
		read q

		if [[ "${q}" == "Y" ]] || [[ "${q}" == "y" ]]; then
			service nginx restart
		fi

	fi
fi

##Generate env file
mkdir config
mkdir config/env

echo "======================================"
echo "========== Setup Database ============"
echo "======================================"
read -p "Masukan host DB: " dbhost
read -p "Masukan port DB : " dbport
read -p "Masukan nama DB : " dbname
read -p "Masukan user DB : " dbuser
read -p "Masukan pasword DB : " dbpass

echo '[db]
	  conn_str = "pgsql:host='"$dbhost"';port='"$dbport"';dbname='"$dbname"'"
	  user = '"$dbuser"'
	  password = "$dbpass"' > config/env/.env

echo 'dcms = ""
	  master = ""' > config/env/.api

##chmod runtime
chmod -R 777 runtime/

mkdir web/assets
chmod -R 777 web/assets/

echo "======================================"
echo "Initial selesai! Silakan cek situs web Anda di http://127.0.0.1:$port (ip disesuaikan)"
echo ""
echo "Docotel Bandung! <3"
echo "======================================"