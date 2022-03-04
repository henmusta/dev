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
				url 		: "<?= $module['url'];?>/api-data/detailkas", 
				type 		: "POST",
                datatype: 'json',
				data 		: function(d){
					return $.extend({},d,{
						filter : {
							date_start 	: $('[name="filter[date_start]"]').val(),
							date_end 	: $('[name="filter[date_end]"]').val(),
							id_cabang 	: $('[name="filter[id_cabang]"]').val()
						}
					});
				},
			},
            // data : data,
            columns: [
        {
            // title: "total #",
            data: "tanggal",
            visible: false
        }, {
            // title: "Tanggal",
            data: "keterangan",
            sortable: false
        },{
            // title: "Toko",
             defaultContent: "",
            sortable: false
        }, {
            // title: "Nomor",
            data: "debit",
            sortable: false,
            className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )
        }, {
            // title: "Jumlah",
            data: "kredit",
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
            group = group
            console.log(group);
            if (last !== group) {
                $(rows).eq(i).before(
                $("<tr></tr>", {
                    "class": "group",
                    "data-id": group
                }).append($("<td></td>", {
                    "colspan": 4,
                    "class": "pocell",
                    "text": "Total " + group
                })).append($("<td></td>", {
                    "id": "e" + group,
                    "class": "text-right",
                    "text": "0.00"
                })).prop('outerHTML'));
                last = group;
            }
           val = api.row(api.row($(rows).eq(i)).index()).data();
        //    console.log(val);
           $("#e" + val.tanggal).text(parseInt(val.rumus));
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
	});
});

//    
</script>