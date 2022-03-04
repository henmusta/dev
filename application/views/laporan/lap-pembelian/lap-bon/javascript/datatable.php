<script type="text/javascript">
var val;

$(function(){
	'use strict'
	$(document).ready(function(){	        
        const dt = $('#dt').DataTable({
            // displayLength 	: -1,
			info			: false,
			searching		: false,
            ordering        : true,
			paging 			: false, 
			processing 		: true,
			serverSide 		: true,
			ajax:{
				url 		: "<?= $module['url'];?>/api-data/datatable", 
				type 		: "POST",
                datatype: 'json',
				data 		: function(d){
					return $.extend({},d,{
						filter : {
							date_start 	: $('[name="filter[date_start]"]').val(),
							date_end 	: $('[name="filter[date_end]"]').val(),
							id_cabang 	: $('[name="filter[id_cabang]"]').val(),
							id_pemasok 	: $('[name="filter[id_pemasok]"]').val(),
                            cek_bon 	: $('[name="filter[cek_bon]"]').val()
						}
					});
				},
			},
            // data : data,
            columns: [
        {
            // title: "total #",
            data: "nama_pemasok",
            visible: false
        }, {
            // title: "Tanggal",
            data: "tgl_buat",
            sortable: false
        },{
            // title: "Toko",
            data: "nama_pemasok",
            sortable: false
        }, {
            // title: "Nomor",
            data: "nomor",
            sortable: false
        }, {
            // title: "Jumlah",
            data: "sisa_tagihan",
            sortable: false,
            className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )
        },
        {
            // title: "total",
            defaultContent: "",
            sortable: false,
            className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )
        }
    ],
    drawCallback: function (settings) {
        var api = this.api();
        var rows = api.rows({
            page: 'current'
        }).nodes();
        var last = null;
        api.column(0, {
            page: 'current'
        }).data().each(function (group, i) {
            group = group.split(' ').join('_').split('/').join('_').split('&').join('_')
            console.log(group);
            if (last !== group) {
                $(rows).eq(i).before(
                $("<tr></tr>", {
                    "class": "group",
                    "data-id": group
                }).append($("<td></td>", {
                    "colspan": 4,
                    "class": "pocell",
                    "text": "Total " + group.split('_').join(' ')
                })).append($("<td></td>", {
                    "id": "e" + group,
                    "class": "text-right",
                    "text": "0.00"
                })).prop('outerHTML'));
                last = group;
            }
           val = api.row(api.row($(rows).eq(i)).index()).data();
        //    console.log(val);
           $("#e" + val.nama_pemasok.split(' ').join('_').split('/').join('_').split('&').join('_')).text(parseInt($("#e" + val.nama_pemasok.split(' ').join('_').split('/').join('_').split('&').join('_')).text()) + parseInt(val.sisa_tagihan));
        });
    },

        "footerCallback": function (row, rowData, start, end, display) {
        var api = this.api(), rowData;
        var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
        $(api.column(5).footer()).html(
            api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0 )
        );
    }

		});

		$('.flatpickr').flatpickr({
			dateFormat 	: "Y-m-d"
		});
		$('[name="filter[date_start]"], [name="filter[date_end]"]').on({
			change: function(e){
				dt.draw();
			}
		});

		$('.select2-toko').select2({
			placeholder: "",
            width: 'resolve',
			allowClear: true,
			ajax : {
				url 		: '<?= $module['url'];?>/api-data/select2-toko',
				type 		: 'POST',
				dataType 	: 'json',
				data 		: function (params) {
					var query = {
						search: params.term,
						type: 'public'
					}
					return $.extend({},params,{ 'id_cabang': $('#id_cabang').val() });
				}
			}
		}).on('change',function(e){
			dt.draw();
			// $('#toko').val($('#select2-toko').val());
		});


        $('.cek-bon').select2({
			placeholder: "",
            width: 'resolve'
		}).on('change',function(e){
			dt.draw();
			// $('#toko').val($('#select2-toko').val());
		});

		// $('#select2-toko').on('change',function(){
		// 	$('#select2-barang').val(null).trigger('change');
		// });

        $( "#excel" ).click(function() {
            // alert($('[name="filter[id_pemasok]"]').val());
            var   date_start  = $('[name="filter[date_start]"]').val();
            var   date_end    = $('[name="filter[date_end]"]').val();
		    var   id_cabang  = $('[name="filter[id_cabang]"]').val();
            var   id_pemasok  = $('[name="filter[id_pemasok]"]').val();
            var   cek_bon  = $('[name="filter[cek_bon]"]').val();
         	window.open("<?php echo site_url('laporan/lap-pembelian/lap-bon/excel/')?>"+date_start+'/'+date_end+'/'+id_cabang+'/'+id_pemasok+'/'+cek_bon)
        });
		
	});
});

//    
</script>