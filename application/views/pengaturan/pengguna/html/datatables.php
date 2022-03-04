<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- Page Content -->
<div class="content">
    <div class="block">
        <div class="block-header">
            <h3 class="block-title"><?php echo ucwords($title);?><small><?php echo ucwords($subtitle);?></small></h3>
            <div class="block-options">
                <a href="<?php echo $btn_add_new;?>" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-fw fa-plus-circle"></i> Add New
                </a>
            </div>
        </div>
        <div class="block-content block-content-full">
            <div class="table-responsive">
                <table id="<?php echo $id;?>" class="table table-bordered table-striped" width="100%">
                    <?php echo $thead . '<tbody></tbody>' . $tfoot;?>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- END Page Content -->