cd /var/www/sirs/backend/penjaminasuransi && php5.6 yii cron-unduh-dokumen-eklaim -s="$(date -d '-1days'  '+%Y-%m-%d')" -e="$(date -d '-8days' +'%Y-%m-%d')"
