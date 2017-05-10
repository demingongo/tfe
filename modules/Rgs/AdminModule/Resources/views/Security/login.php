<!DOCTYPE html>
{config_load file='file:[RgsCatalogModule]rgs.conf'}
<html>

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

{block name=stylesheets}
<!-- Bootstrap Core CSS -->
    <link href="{asset url='/startbootstrap-sb-admin-2-1.0.8/bower_components/bootstrap/dist/css/bootstrap.min.css' package='plugins'}" rel="stylesheet">

<!-- MetisMenu CSS -->
<link href="{asset url='/startbootstrap-sb-admin-2-1.0.8/bower_components/metisMenu/dist/metisMenu.min.css' package='plugins'}" rel="stylesheet">

<!-- Timeline CSS -->
<link href="{asset url='/startbootstrap-sb-admin-2-1.0.8/dist/css/timeline.css' package='plugins'}" rel="stylesheet">

<!-- Custom CSS -->
<link href="{asset url='/startbootstrap-sb-admin-2-1.0.8/dist/css/sb-admin-2.css' package='plugins'}" rel="stylesheet">

<!-- Morris Charts CSS -->
<link href="{asset url='/startbootstrap-sb-admin-2-1.0.8/bower_components/morrisjs/morris.css' package='plugins'}" rel="stylesheet">

<!-- Custom Fonts -->
<link href="{asset url='/startbootstrap-sb-admin-2-1.0.8/bower_components/font-awesome/css/font-awesome.min.css' package='plugins'}" rel="stylesheet" type="text/css">

<link rel="stylesheet" type="text/css" href="{asset url='/fancybox/source/jquery.fancybox.css' package='plugins'}" media="screen" />
<link rel="stylesheet" href="{asset url='/main.admin.css' package='css'}" type="text/css">
{/block}
<link rel="icon" href="{asset url='/img/retro_favicon.ico'}" />
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
<!--[if lte IE 7]>
<link rel="stylesheet" href="{asset url='/style_ie.css' package='css'}" type="text/css">
<![endif]-->

<title>{nocache}{block name=title}{/block}{#sitename#} - Administration{/nocache}</title>
{block name=head}{/block}

</head>

<body>

    <div class="container">
        <div class="row">
		{if $session_flash->has('danger')}
		{notification type="danger" message=$session_flash->get('danger') sign=true close=false}
		{/if}
		{if $session_flash->has('error')}
		{notification type="error" message=$session_flash->get('error') sign=true}
		{/if}
		{if $session_flash->has('warning')}
		{notification type="warning" message=$session_flash->get('warning') sign=true}
		{/if}
		{if $session_flash->has('success')}
		{notification type="success" message=$session_flash->get('success') sign=true} {*glyphicon="thumbs-up"*}
		{/if}
		{if $session_flash->has('info')}
		{notification type="info" message=$session_flash->get('info') sign=true}
		{/if}
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Please Sign In</h3>
                    </div>
                    <div class="panel-body">
                        <form data-toggle="validator" method="post" enctype="multipart/form-data" accept="image/gif">
                            <fieldset>
								{if $session_flash->has('notice')}
						{notification type="error" message=$session_flash->get('notice') sign=true close=false}
						{/if}
								{* begin login *}
								<div class="form-group">
									{form_build_widget form=$form field='login'}
								</div>
								{* end login *}

								{* begin password *}
								<div class="form-group has-feedback">
									{form_build_widget form=$form field='password'}
								</div>
								{* end password *}

								{* begin remember_me *}
									{form_build_widget form=$form field='remember_me'}
								{* end remember_me *}

								{* _csrf_token *}
								{form_build_widget form=$form field='_csrf_token'}

								{form_build_widget form=$form}

								{* begin submit *}
								<input type="submit" class="btn btn-lg btn-success btn-block" id="_submit" name="_submit" value="{'security.login.submit'|trans:[]:UserModule}" />
								{* end submit *}
                                {******************
								<div class="form-group">
                                    <input class="form-control" placeholder="E-mail" name="email" type="email" autofocus>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password" name="password" type="password" value="">
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="remember" type="checkbox" value="Remember Me">Remember Me
                                    </label>
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
                                <a href="index.html" class="btn btn-lg btn-success btn-block">Login</a>
                                ******************}
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

{block name=javascript}
<!-- jQuery -->
<script src="{asset url='/startbootstrap-sb-admin-2-1.0.8/bower_components/jquery/dist/jquery.min.js' package='plugins'}"></script>
<script src="{asset url='/jquery-ui.min.js' package='js'}"></script>
<script src="{asset url='/jquery-easing/1.3/jquery.easing.min.js' package='cdnjs'}"></script>

<!-- Bootstrap Core JavaScript -->
<script src="{asset url='/startbootstrap-sb-admin-2-1.0.8/bower_components/bootstrap/dist/js/bootstrap.min.js' package='plugins'}"></script>
<script src="{asset url='/1000hz-bootstrap-validator/0.8.1/validator.min.js' package='cdnjs'}"></script>

<!-- AngularJS -->
<script src= "http://ajax.googleapis.com/ajax/libs/angularjs/1.4.6/angular.min.js"></script>
<script src= "http://ajax.googleapis.com/ajax/libs/angularjs/1.4.6/angular-sanitize.js"></script>

<!-- Metis Menu Plugin JavaScript -->
<script src="{asset url='/startbootstrap-sb-admin-2-1.0.8/bower_components/metisMenu/dist/metisMenu.min.js' package='plugins'}"></script>
{***********************
<!-- Morris Charts JavaScript -->
<script src="{asset url='/startbootstrap-sb-admin-2-1.0.8/bower_components/raphael/raphael-min.js' package='plugins'}"></script>
<script src="{asset url='/startbootstrap-sb-admin-2-1.0.8/bower_components/morrisjs/morris.min.js' package='plugins'}"></script>
<script src="{asset url='/startbootstrap-sb-admin-2-1.0.8/js/morris-data.js' package='plugins'}"></script>
************************}
<!-- Custom Theme JavaScript -->
<script src="{asset url='/startbootstrap-sb-admin-2-1.0.8/dist/js/sb-admin-2.js' package='plugins'}"></script>

<script src="{asset url='/default.js' package='js'}"></script>
<script type="text/javascript" src="{asset url='/main.admin.js' package='js'}"></script>
{/block}

</body>

</html>