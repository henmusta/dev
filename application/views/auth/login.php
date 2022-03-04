<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <base href="<?php echo base_url('/');?>">
        
        <title>Sistem Informasi Toko Ahsana</title>

        <meta name="description" content="">
        <meta name="author" content="ginktech">
        <meta name="robots" content="noindex, nofollow">

        <!-- Icons -->
        <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
        <link rel="shortcut icon" href="assets/media/favicons/favicon.png">
        <link rel="icon" type="image/png" sizes="192x192" href="assets/media/favicons/favicon-192x192.png">
        <link rel="apple-touch-icon" sizes="180x180" href="assets/media/favicons/apple-touch-icon-180x180.png">
        <!-- END Icons -->

        <!-- Stylesheets -->
        <!-- Fonts and OneUI framework -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400italic,600,700%7COpen+Sans:300,400,400italic,600,700">
        <link rel="stylesheet" id="css-main" href="assets/css/oneui.min.css">
    </head>
    <body>
        <div id="page-container">

            <!-- Main Container -->
            <main id="main-container">

                <!-- Page Content -->
                <div class="hero-static d-flex align-items-center">
                    <div class="w-100">
                        <!-- Sign In Section -->
                        <div class="content content-full bg-waves">
                            <div class="row justify-content-center">
                                <div class="col-md-8 col-lg-6 col-xl-4 py-4">
                                    <div class="block">
                                        <div class="block-content py-4">
                                            <!-- Header -->
                                            <div class="text-center">
                                                 <img src="<?php echo base_url() ?>assets\media\photos\<?php echo $aplikasi->gambar; ?>" alt="" style="width: 300px; height: 150px; margin-right: 10px;">
                                                <h1 class="h4 mb-1">Sistem Informasi Toko Ahsana</h1>
                                                <h2 class="h6 font-w400 mb-3">Silahkan Login</h2>
                                            </div>
                                            <form id="<?php echo $form['id'];?>" action="<?php echo $form['action'];?>" method="POST" autocomplete="off">
                                                <input type="hidden" name="redirect" value="<?php echo $form['redirect'];?>"/>
                                                <div class="py-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control form-control-lg form-control-alt" 
                                                            name="username" placeholder="Username" 
                                                            required="required"
                                                            maxlength="20"
                                                            minlength="3">
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="password" class="form-control form-control-lg form-control-alt" 
                                                            name="password" placeholder="Passsword"
                                                            required="required"
                                                            maxlength="20"
                                                            minlength="4">
                                                    </div>
                                                </div>
                                                <div class="form-group row justify-content-center mb-0">
                                                    <div class="col-md-6 col-xl-5">
                                                        <button type="submit" class="btn btn-block btn-primary">
                                                            <i class="fa fa-fw fa-sign-in-alt mr-1"></i> Masuk
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <!-- END Sign In Form -->
                                </div>
                            </div>
                        </div>
                        <!-- END Sign In Section -->
                    </div>
                </div>
                <!-- END Page Content -->

            </main>
            <!-- END Main Container -->
        </div>
        <!-- END Page Container -->
        <script src="assets/js/oneui.core.min.js"></script>
        <script src="assets/js/oneui.app.min.js"></script>
        <!-- Page JS Plugins -->
        <script src="assets/js/plugins/bootstrap-notify/bootstrap-notify.min.js"></script>
        <script src="assets/js/plugins/jquery-validation/jquery.validate.min.js"></script>        
        <script>
        $(function(){
            'use strict'
            $(document).ready(function(){

                $('form#<?php echo $form['id'];?>').validate({
                    validClass      : 'is-valid',
                    errorClass      : 'is-invalid',
                    errorElement    : 'span',
                    errorPlacement: function (error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-group').append(error);
                    },
                    submitHandler   : function(form,eve) {
                        eve.preventDefault();
                        var btnSubmit       = $(form).find("[type='submit']"),
                            btnSubmitHtml   = btnSubmit.html();

                        $.ajax({
                            cache       : false,
                            processData : false,
                            contentType : false,
                            type        : 'POST',
                            url         : $(form).attr('action'),
                            data        : new FormData(form),
                            dataType    : 'JSON',
                            beforeSend:function() { 
                                btnSubmit.addClass("disabled").html("<i class='fas fa-spinner fa-pulse fa-fw'></i> Loading ... ");
                            },
                            error       : function(){
                                btnSubmit.removeClass("disabled").html(btnSubmitHtml);
                                One.helpers('notify', {type: 'danger', icon: 'fa fa-exclamation mr-1', message: 'Server\'s response not found'});
                            },
                            success     : function(response) {
                                btnSubmit.removeClass("disabled").html(btnSubmitHtml);
                                if ( response.status == "success" ){
                                    One.helpers('notify', {type: 'success', icon: 'fa fa-check mr-1', message: response.message});
                                    setTimeout(function(){
                                        location.href = response.redirect;
                                    },1000);
                                } else {
                                    One.helpers('notify', {type: 'danger', icon: 'fa fa-exclamation mr-1', message: response.message});
                                }
                            }
                        });
                    }
                });
                $('input[name="username"]').rules("add",{
                    remote: { 
                        url     : "<?php echo $form['validation']['user_is_exsist_url']?>", 
                        type    : "POST"
                    },
                    messages : { 
                        remote: "User not found",
                    }
                });

            });
        });
        </script>
    </body>
</html>
