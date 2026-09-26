<?php

use yii\db\Migration;

/**
 * Class m220318_063714_migrate_APD313_infopengajuanklaim_v
 */
class m220318_063714_migrate_APD313_infopengajuanklaim_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopengajuanklaim_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopengajuanklaim_v\" AS
            SELECT pengajuanklaim_t.pengajuanklaim_id,
            pengajuanklaim_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pengajuanklaim_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pengajuanklaim_t.tgl_pengajuanklaim,
            pengajuanklaim_t.no_pengajuanklaim,
            pengajuanklaim_t.tgl_jatuhtempo,
            pengajuanklaim_t.tgl_pelayanansampai,
            pengajuanklaim_t.tgl_pelayanandari,
            sum((pembayaran_t.total_dijamin + pembayaran_t.total_pembulatan)) AS total_piutang,
            pengajuanklaim_t.total_terbayar,
            pengajuanklaim_t.total_sisapiutang,
            pengajuanklaim_t.alamat_penjamin,
            pengajuanklaim_t.npwp,
            pengajuanklaim_t.totalbiaya_obat,
            pengajuanklaim_t.totalbiaya_tindakan,
            pengajuanklaim_t.pegawaimengetahui_id,
            pengajuanklaim_t.catatan,
            pengajuanklaim_t.status_pengajuanklaim,
            fgetnamalookup((pengajuanklaim_t.status_pengajuanklaim)::integer) AS s_pengajuanklaim,
            pengajuanklaim_t.is_deleted,
            pengajuanklaim_t.is_active,
            CASE
            WHEN (instalasi_m.instalasi_nama IS NULL) THEN 'Semua Instalasi'::character varying
            ELSE instalasi_m.instalasi_nama
            END AS instalasi_nama,
            CASE
            WHEN (ruangan_m.ruangan_nama IS NULL) THEN 'Semua Ruangan'::character varying
            ELSE ruangan_m.ruangan_nama
            END AS ruangan_nama,
            sum(pembayaran_t.total_discountpembayaran) AS total_discountpembayaran
            FROM (((((((pengajuanklaim_t
            JOIN pengajuanklaimdetail_t ON ((pengajuanklaim_t.pengajuanklaim_id = pengajuanklaimdetail_t.pengajuanklaim_id)))
            JOIN pembayaranpelayanan_t ON ((pengajuanklaimdetail_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
            JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
            JOIN carabayar_m ON ((pengajuanklaim_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN penjamin_m ON ((pengajuanklaim_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN instalasi_m ON ((pengajuanklaim_t.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ruangan_m ON ((pengajuanklaim_t.ruangan_id = ruangan_m.ruangan_id)))
            WHERE (pengajuanklaim_t.is_deleted = false)
            GROUP BY pengajuanklaim_t.pengajuanklaim_id, pengajuanklaim_t.carabayar_id, carabayar_m.carabayar_nama, pengajuanklaim_t.penjamin_id, penjamin_m.penjamin_nama, pengajuanklaim_t.tgl_pengajuanklaim, pengajuanklaim_t.no_pengajuanklaim, pengajuanklaim_t.tgl_jatuhtempo, pengajuanklaim_t.tgl_pelayanansampai, pengajuanklaim_t.tgl_pelayanandari, pengajuanklaim_t.total_piutang, pengajuanklaim_t.total_terbayar, pengajuanklaim_t.total_sisapiutang, pengajuanklaim_t.alamat_penjamin, pengajuanklaim_t.npwp, pengajuanklaim_t.totalbiaya_obat, pengajuanklaim_t.totalbiaya_tindakan, pengajuanklaim_t.pegawaimengetahui_id, pengajuanklaim_t.catatan, pengajuanklaim_t.status_pengajuanklaim, pengajuanklaim_t.is_deleted, pengajuanklaim_t.is_active, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama
            ;");
        $this->execute('
            ALTER TABLE public.infopengajuanklaim_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220318_063714_migrate_APD313_infopengajuanklaim_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220318_063714_migrate_APD313_infopengajuanklaim_v cannot be reverted.\n";

        return false;
    }
    */
}
