<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<div class="content">
    <form id="<?php echo $id;?>" method="<?php echo $method;?>" autocomplete="off" action="<?php echo $action;?>" enctype="multipart/form-data">
        <input type="hidden" name="pk" value="<?php echo isset($prev_data['id_pengguna']) ? $prev_data['id_pengguna'] : NULL;?>">
        <div class="block mb-2">
            <div class="block-header">
                <h3 class="block-title"><?php echo ucwords($title);?><small><?php echo ucwords($subtitle);?></small></h3>
                <div class="form-group align-items-center mb-0 d-none">
                    <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input" id="redirect" name="redirect" value="<?php echo $redirect;?>" checked="checked">
                        <label class="custom-control-label" for="redirect">Kembali</label>
                    </div>
                </div>
                <div class="block-options">
                    <a href="javascript:history.back();" class="btn btn-sm btn-outline-danger"><i class="far fa-window-close"></i> Cancel</a>
                    <button type="submit" class="btn btn-sm btn-outline-primary"><i class="fas fa-save"></i> Save</button>
                </div>
            </div>
        </div>
        <div class="block">
            <div class="block-content">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama</label>
                            <input id="check_nama" type="text" class="form-control" name="pengguna[nama]" required="required" maxlength="128" value="<?php echo isset($prev_data['nama']) ? $prev_data['nama'] : NULL;?>"/>
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input id="check_username" type="text" class="form-control" name="pengguna[username]" required="required" maxlength="32" value="<?php echo isset($prev_data['username']) ? $prev_data['username'] : NULL;?>"/>
                        </div>
                        <div class="form-group">
                            <label>Passsword</label>
                            <input type="text" class="form-control" name="pengguna[password]" <?= !isset($prev_data['username']) ? 'required="required"' : null ?>/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Cabang</label>
                            <select id="select-cabang" class="form-control" name="pengguna[id_cabang]" required="required"></select>
                        </div>
                        <div class="form-group">
                            <label>Hak Akses</label>
                            <input id="jenis-hak" type="hidden" value="Pusat">
                            <select id="select-hak-akses" class="form-control" name="pengguna[hak_akses]" required="required"></select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="pengguna[status]">
                                <option value="1" <?= isset($prev_data['status']) && $prev_data['status'] == 1 ? 'selected="selected"' : NULL;?>>Aktif</option>
                                <option value="0" <?= isset($prev_data['status']) && $prev_data['status'] == 0 ? 'selected="selected"' : NULL;?>>Non Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>