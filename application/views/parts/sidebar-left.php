<nav id="sidebar" aria-label="Main Navigation">
    <div class="content-header bg-white-5">
        <a class="font-w600 text-dual" href="">
            <i class="fa fa-cubes"></i>
            <span class="smini-hide">
                <span class="font-w700 font-size-h5"><?= isset($brand) ? $brand : NULL; ?></span>
            </span>
        </a>
        <div>
            <a class="d-lg-none btn btn-sm btn-dual ml-2" data-toggle="layout" data-action="sidebar_close" href="javascript:void(0)">
                <i class="fa fa-fw fa-times"></i>
            </a>
        </div>
    </div>
    <div class="content-side content-side-full">
    	<?= isset($main_navigation) && is_array($main_navigation) && count($main_navigation) > 0 ? main_navigation($main_navigation): NULL;?>
    </div>
</nav>