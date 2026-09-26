<?php

use yii\db\Migration;

/**
 * Class m241015_103525_migrate_PCP83_insert_logperubahanresep_r_reseptur
 */
class m241015_103525_migrate_PCP83_insert_logperubahanresep_r_reseptur extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE FUNCTION public.insert_logperubahanresep_r_reseptur()
 RETURNS trigger
 LANGUAGE plpgsql
AS \$function\$
	declare
		var_resepturdetail_id integer;
		var_jenisperubahan varchar;
		var_penjualanresep_id integer;
		var_created_date_resep timestamp;
		var_tanggalperubahan timestamp;
		var_obatalkes_id integer;
		var_obatalkesnama varchar;
		var_perubahansebelum varchar;
	    var_perubahansetelah varchar;
	    var_pegawai_id integer;
	    var_pegawainama varchar;
	   	var_signanama varchar;
	    var_satuanunitnama varchar;
	   	var_det float;
	    var_reseptur_id integer;
	BEGIN
		var_reseptur_id := new.reseptur_id;
		
		-- THIS FN() HANDLING RESEPTUR ONLY!!!
		-- ANY RESEPTUR WITH penjualanresep_id WILL BE SKIPPED!

		-- GENERAL FLOW
		-- 1. Check incoming transaction (new, update or delete).
		-- 2. Check are transaction has penjualanresep_id, if transaction has penjualanresep_id then skip insert log.
		-- 3. Preparation.
		-- 4. Insert process.
		
		select resepturdetail_id into var_resepturdetail_id from resepturdetail_t rt where resepturdetail_id = new.resepturdetail_id;
		select penjualanresep_id into var_penjualanresep_id from reseptur_t where reseptur_id = new.reseptur_id;
		select created_date into var_created_date_resep from reseptur_t where reseptur_id = new.reseptur_id; 
	
		if(var_penjualanresep_id is null)
			then
				select obatalkes_nama into var_obatalkesnama from obatalkes_m where obatalkes_id = new.obatalkes_id;
				select signa_nama into var_signanama from signaobat_m where signa_id = new.signa_id;
				select satuanunit_nama into var_satuanunitnama from satuanunit_m where satuanunit_id = new.satuankecil_id;
			
				if(var_signanama is null)
					then
						var_signanama := new.signa->>'text';
				end if;
			
				var_det := new.qty_konversi;
				if(new.det is not null) then var_det := new.det_konversi; end if;

				var_perubahansebelum := var_det || ' ' || var_satuanunitnama || ' | ' || var_signanama;
				var_perubahansetelah := var_det || ' ' || var_satuanunitnama || ' | ' || var_signanama;
			
				if(new.is_deleted = true)
					then
						var_jenisperubahan := 'HAPUS';
						var_tanggalperubahan := new.last_modified_date;
						var_pegawai_id := new.deleted_by;
				elseif(new.last_modified_by is not null)
					then
						with reseptur_detail as (
							select * from resepturdetail_t where resepturdetail_id = new.resepturdetail_id
						)
						select 
							concat(
								coalesce(reseptur_detail.det_konversi, reseptur_detail.qty_konversi),
								' ', satuanunit_m.satuanunit_nama,
								' | ',
								coalesce(signaobat_m.signa_nama, signa->>'text')
							) as perubahan_sebelum
						into var_perubahansebelum
						from reseptur_detail
						left join signaobat_m on reseptur_detail.signa_id = signaobat_m.signa_id
						left join satuanunit_m on reseptur_detail.satuankecil_id = satuanunit_m.satuanunit_id;
					
						var_jenisperubahan := 'PEMBAHARUAN';
						var_tanggalperubahan := new.last_modified_date;
						var_pegawai_id := new.last_modified_by;
				elseif(
					(new.created_date > (var_created_date_resep + interval '30 Seconds'))
					and new.last_modified_by is null
					-- and var_resepturdetail_id is null
				)
					then
						var_jenisperubahan := 'TAMBAH';
						var_tanggalperubahan := new.created_date;
						var_pegawai_id := new.created_by;
				else var_jenisperubahan := null;
				end if;
				
				if(
					var_jenisperubahan = 'TAMBAH'
					or var_jenisperubahan = 'HAPUS'
					or (var_jenisperubahan = 'PEMBAHARUAN' and var_perubahansebelum != var_perubahansetelah)
				)
				then
					select pegawai_m.nama_pegawai into var_pegawainama
					from loginpemakai_k
					join pegawai_m on loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
					where loginpemakai_k.loginpemakai_id = var_pegawai_id;
				
					if (var_pegawai_id is null) then var_pegawai_id := '1'; end if;
					if (var_pegawainama is null) then var_pegawainama := 'Super Admin'; end if;
		
					insert into logperubahanresep_r (
						reseptur_id, 
						resepturdetail_id, 
						tanggal_perubahan, 
						jenis_perubahan, 
						obatalkes_id, 
						obatalkes_nama, 
						perubahan_sebelum, 
						perubahan_setelah,
						pegawai_id,
						pegawai_nama)
					values (
						new.reseptur_id,
						new.resepturdetail_id,
						date_trunc('second', now()::timestamp),
						var_jenisperubahan,
						new.obatalkes_id,
						var_obatalkesnama,
						var_perubahansebelum,
						var_perubahansetelah,
						var_pegawai_id,
						var_pegawainama
					);
				end if;
		end if;

		return new;
	END;
\$function\$
;
");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241015_103525_migrate_PCP83_insert_logperubahanresep_r_reseptur cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241015_103525_migrate_PCP83_insert_logperubahanresep_r_reseptur cannot be reverted.\n";

        return false;
    }
    */
}
