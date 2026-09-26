#Cron Odoo - Daily Cutoff

cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-master-productcategory
sleep 60
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-master-uom
sleep 60
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-master-ruangan
sleep 60
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-master-partner
sleep 60
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-master-tindakanpaket
sleep 60
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-master-obat
sleep 60
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-master-barang
sleep 60
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-master-bank
sleep 60
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-master-edc
sleep 60
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-master-paymenttype
sleep 60
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-master-group-inacbg
sleep 60
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-master-patient
sleep 60
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-master-category-transaction
sleep 60
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-saleorder
sleep 60
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-saleorderline-tindakan
sleep 60
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-saleorderline-obat
sleep 60
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-saleorderbill
sleep 60
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-scrollcashier
sleep 60
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-inpatientdeposit
sleep 60
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-patientdebt
sleep 60
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-stockscrap
sleep 60
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-grnreceipt
sleep 60
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-grnreceiptdetail
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-grnissue
cd /var/www/sirs/backend/master && php5.6 yii acc-entry-console/extract-grnissuedetail
