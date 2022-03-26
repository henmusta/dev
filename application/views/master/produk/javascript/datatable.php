<script type="text/javascript"
    src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>
<script type="text/javascript">
var dt;

function change_account(id) {

    $.ajax({
        url: "<?= $module['url']; ?>/cek_status/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data) {
            if (data.status == '1') {
                update_not_active(id);
                setTimeout(function() {
                    dt.draw();
                }, 100);

            } else {
                update_active(id);
                setTimeout(function() {
                    dt.draw();
                }, 100);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert("gagal");
        }
    });
}

function update_active(id) {
    // alert('aktif');
    $.ajax({
        url: "<?= $module['url']; ?>/update_active/" + id,
        type: "POST",
        dataType: "JSON",
        success: function(data) {
            dt.draw();
        }
    });
}

function update_not_active(id) {
    // alert('not_aktif');
    $.ajax({
        url: "<?= $module['url']; ?>/update_non_active/" + id,
        type: "POST",
        dataType: "JSON",
        success: function(data) {
            dt.draw();
        }
    });
}
$(function() {
    'use strict'
    $(document).ready(function() {
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }


        dt = $('#dt').DataTable({
            columnDefs: [{
                targets: 0,
                data: 0,
                checkboxes: {
                    selectRow: false
                }
            }],
            select: {
                style: 'multi'
            },
            lengthMenu: [
                [5, 10, 25, 50, 100, -1],
                ["5", "10", "25", "50", "100", "All"]
            ],
            displayLength: 10,
            order: [
                [3, 'desc']
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?= $module['url']; ?>/api-data/datatable",
                type: "POST",
                data: function(d) {
                    let obj = {};
                    $('[name^="filter"]').each(function() {
                        var key = $(this).attr('name');
                        var val = $(this).val();
                        obj[key] = val;
                    });
                    return $.extend({}, d, obj);
                }
            },
            columns: [{
                    data: 'id',
                    width: '40px',

                },
                {
                    data: 'id',
                    className: 'text-center',
                    width: '40px',
                    orderable: false,
                    render: function(columnData, type, rowData, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'kode_pemasok',
                    width: '300px',
                    render: function(columnData, type, rowData, meta) {
                        return columnData + ' || ' + rowData.nama_pemasok;
                    }
                },
                {
                    data: 'kode_produk'
                },
                {
                    data: 'nama'
                },
                {
                    data: 'harga_beli',
                    width: '140px',
                    className: 'text-right',
                    render: $.fn.dataTable.render.number(',', '.', 0, '')
                },
                {
                    data: 'harga_jual',
                    width: '140px',
                    className: 'text-right',
                    render: $.fn.dataTable.render.number(',', '.', 0, '')
                },

                {
                    data: 'status',
                    className: 'text-center',
                    width: '200px',
                    orderable: false,
                    render: function(columnData, type, rowData, meta) {

                        if (columnData == '0') {
                            return `<a type="button" class="btn btn-outline-warning js-click-ripple-enabled" data-toggle="click-ripple" style="overflow: hidden; position: relative; z-index: 1;">Arsip</a>`;
                        } else {
                            return `<a type="button" class="btn btn-outline-success js-click-ripple-enabled" data-toggle="click-ripple" style="overflow: hidden; position: relative; z-index: 1;">Tampil</a>`;
                        }

                    }
                },
                {
                    data: 'id',
                    className: 'text-center',
                    width: '80px',
                    orderable: false,
                    render: function(columnData, type, rowData, meta) {

                        if (rowData.status == '1') {
                            return '<div><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id=' +
                                columnData + ' name=' + columnData +
                                ' onclick="change_account(' + columnData +
                                ')" checked><label class="custom-control-label" for=' +
                                columnData + '></label></div></div>';
                        } else {
                            return '<div><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id=' +
                                columnData + ' name=' + columnData +
                                ' onclick="change_account(' + columnData +
                                ')"><label class="custom-control-label" for=' +
                                columnData + '></label></div></div>';
                        }
                    }
                },
                {
                    data: 'stok'
                },
                {
                    data: 'id',
                    className: 'text-center',
                    width: '40px',
                    orderable: false,
                    render: function(columnData, type, rowData, meta) {
                        return `
						<div class="dropdown dropleft">
							<button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-fw fa-cog"></i></button>
							<div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item" href="<?= $module['url']; ?>/update/` + columnData + `">Edit</a>
								<a class="dropdown-item" href="<?= $module['url']; ?>/barcode/` + columnData + `">Cetak Barcode</a>
								<a class="dropdown-item btn-row-delete" href="javascript:void(0);" data-pk="` + columnData + `">Delete</a>
							</div>
						</div>
						`;
                    }
                }
            ],
            // columnDefs: [
            // 		{
            // 			targets: 0,
            // 			data: 0,
            // 			checkboxes: {
            // 			selectRow: true
            // 			}
            // 		}
            // 	],
            // 	select: {
            // 		'style': 'multi'
            // },
            rowCallback: function(row, data) {
                let api = this.api();
                $(row).find('.btn-row-delete').click(function() {
                    Swal.fire({
                        title: "Anda Yakin ?",
                        text: "Data tidak dapat dikembalikan setelah di hapus!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Ya, Hapus!",
                        cancelButtonText: "Tidak, Batalkan",
                    }).then((result) => {
                        if (result.value) {
                            $.ajax({
                                url: '<?= $module['url']; ?>/crud/delete',
                                type: "POST",
                                data: {
                                    pk: data.id
                                },
                                dataType: "json",
                                error: function() {
                                    One.helpers('notify', {
                                        type: 'danger',
                                        icon: 'fa fa-exclamation mr-1',
                                        message: 'Server\'s response not found'
                                    });
                                },
                                success: function(response) {
                                    if (response.status ==
                                        "success") {
                                        One.helpers('notify', {
                                            type: 'success',
                                            icon: 'fa fa-check mr-1',
                                            message: response
                                                .message
                                        });
                                        api.ajax.reload(null,
                                            false);
                                    } else {
                                        One.helpers('notify', {
                                            type: 'danger',
                                            icon: 'fa fa-exclamation mr-1',
                                            message: response
                                                .message
                                        });
                                    }
                                }
                            });
                        }
                    });
                });
            }
        });

    });
    $('#barcode_multiple').on('submit', function(e) {
        var form = this;
        var tabel = $('#dt').DataTable();
        var rows_selected = tabel.column(0).checkboxes.selected();

        var row = [];
        $.each(rows_selected, function(index, rowid) {
            //   alert(rowid);
            // Create a hidden element 
            $(form).append(
                $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'id[]')
                .val(rowid)
            );
            row.push(rowid);
        });

        var kode = rows_selected.join(",");

        window.location.href = "<?= $module['url']; ?>/barcode_multiple?kode=" + row;
        e.preventDefault();
    });

    $('[name^="filter"]').change(function() {
        dt.draw(null, false);
    });
});
</script>