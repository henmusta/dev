<script type="text/javascript">
$(function(){
	'use strict'
	$(document).ready(function(){
		$(".flatpickrhri").datepicker( {
		format: "yyyy-mm-dd",
		autoclose: true
		});
		$(".flatpickrbln").datepicker( {
		format: "yyyy-mm",
		viewMode: "months", 
		minViewMode: "months",
		autoclose: true
		});
		$(".flatpickrthn").datepicker( {
			format: "yyyy",
			viewMode: "years", 
			minViewMode: "years",
		autoclose: true
		});


		$("#formFilter").on('submit', function(e) {
            if ($('#tglhari').val() == null || $("#tglhari").val() == '') {
                alert('silakan pilih tanggal');
                return false;
            }
            e.preventDefault();
            $.ajax({
                url: "<?= $module['url'];?>/print-out-hari",
                type: "POST",
                data: $(this).serialize(),
                beforeSend: function(e) {
                    $("#submitFilterhari").html(
                        "<i class='fa fa-spinner fa-spin'></i> Loading data..");
                },
                success: function(e) {
                    $("#submitFilterhari").html("Cetak");
                    $("#report").html(e);
                    $("#title").html('');
                }
            });
        });

		$("#formFilterbulan").on('submit', function(e) {
            if ($('#tglbulan').val() == null || $("#tglbulan").val() == '') {
                alert('silakan pilih tanggal');
                return false;
            }
            e.preventDefault();
            $.ajax({
                url: "<?= $module['url'];?>/print-out-bulan",
                type: "POST",
                data: $(this).serialize(),
                beforeSend: function(e) {
                    $("#submitFilterbulan").html(
                        "<i class='fa fa-spinner fa-spin'></i> Loading data..");
                },
                success: function(e) {
                    $("#submitFilterbulan").html("Cetak");
                    $("#report").html(e);
                    $("#title").html('');
                }
            })
        })

		$("#formFiltertahun").on('submit', function(e) {
            if ($('#tgltahun').val() == null || $("#tglbulan").val() == '') {
                alert('silakan pilih tanggal');
                return false;
            }
            e.preventDefault();
            $.ajax({
                url: "<?= $module['url'];?>/print-out-tahun",
                type: "POST",
                data: $(this).serialize(),
                beforeSend: function(e) {
                    $("#submitFiltertahun").html(
                        "<i class='fa fa-spinner fa-spin'></i> Loading data..");
                },
                success: function(e) {
                    $("#submitFiltertahun").html("Cetak");
                    $("#report").html(e);
                    $("#title").html('');
                }
            })
        })


		$( "#excel_hari" ).click(function() {
            var   tgl  = $('[name="filter[tglhari]"]').val();
		    var   id_cabang  = $('[name="filter[id_cabang]"]').val();
         window.open("<?php echo site_url('laporan/laporan-harian/excel_hari/')?>"+tgl+'/'+id_cabang)
        });

		$( "#excel_bulan" ).click(function() {
            var   tgl  = $('[name="filter[tglbulan]"]').val();
		    var   id_cabang  = $('[name="filter[id_cabang]"]').val();
         window.open("<?php echo site_url('laporan/laporan-harian/excel_bulan/')?>"+tgl+'/'+id_cabang)
        });


		$( "#excel_tahun" ).click(function() {
            var   tgl  = $('[name="filter[tgltahun]"]').val();
		    var   id_cabang  = $('[name="filter[id_cabang]"]').val();
         window.open("<?php echo site_url('laporan/laporan-harian/excel_tahun/')?>"+tgl+'/'+id_cabang)
        });
	});
});
</script>