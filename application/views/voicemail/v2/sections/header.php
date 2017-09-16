<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>ProDataFeed || Report :: Campaign List Page</title>

    <!-- Bootstrap -->
    <style type="text/css">
        .display_none {
            opacity: 0;
        }
    </style>
    <link rel="shortcut icon" type="image/png" href=""/>
    <link href="/v2/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

    <!--[if IE]>
        <script src="js/iscript-ieonly.js"></script>
    <![endif]-->

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- #Bootstrap -->

    <!-- LocaL CSS style sheet -->

    <link rel="stylesheet" type="text/css" href="/v2/css/reset.css">
    <link rel="stylesheet" type="text/css" href="/v2/css/bootstrap-datepicker.css">
    <link rel="stylesheet" type="text/css" href="/v2/css/animate.css">
    <link rel="stylesheet" type="text/css" href="/v2/css/style.css">
    <link rel="stylesheet" type="text/css" href="/v2/css/chart-sheet.css">
    <link rel="stylesheet" type="text/css" href="/v2/css/responsive.css">
    <link rel="stylesheet" type="text/css" href="/v2/fonts/fonts.css">
    <link rel="stylesheet" type="text/css" href="/v2/css/new_style.css">

    <!-- #LocaL CSS style sheet -->

    <!-- Google font library -->

    <link href='https://fonts.googleapis.com/css?family=Lato:100,100italic,300,300italic,400,400italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>

    <!-- #Google font library -->

</head>
<body class="theme-report-body theme-report-skin theme-report-skeleton">
    <div class="loader hidden" id="loader_div">
        <i class="fa fa-spinner fa-pulse fa-5x"></i>
    </div>
    <!-- Theme Report Page Structure -->

    <main id="theme-reportpage-main" class="theme-reportpage-main" role="main" >
        <section id="theme-reportpage-section" class="theme-reportpage-section theme-report-section">

            <header class="profile">
                <div class="container-fluid">
                    <div class="row">
                        <nav class="navbar navbar-default">
                            <div class="container-fluid">
                                <!-- Brand and toggle get grouped for better mobile display -->
                                <div class="navbar-header">
                                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false">
                                        <span class="sr-only">Toggle navigation</span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                    </button>
<!--                                    <a class="navbar-brand" href="#"><img src="/v2/images/domain_logos/966c307aaab12d6139dee5222af6c0ce.png" class="img-responsive"></a>-->
                                    <a class="navbar-brand" href="#"><img src="/v2/images/login-icons/logo-main.png" class="img-responsive"></a>

                                </div>

                                <!-- Collect the nav links, forms, and other content for toggling -->
                                <div class="collapse navbar-collapse text-center" id="navbar-collapse">
                                    {if !isset($pdf)}
                                    <ul class="nav navbar-nav text-center" id="nav">
                                        {if $user_type !== 'viewer'}
                                        <li role="presentation" class="dropdown">
                                            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                                Campaigns <span class="caret"></span>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li><a href="/v2/campaign/new_campaign">Create New Campaign</a></li>
                                                {/if}
                                                <li><a href="/v2/campaign/campaign_list">List Campaigns</a></li>
                                                {if $user_type !== 'viewer'}
                                            </ul>
                                        </li>

                                        {if !empty($is_admin)}
                                        <li role="presentation" class="dropdown">
                                            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                                Admin Tools <span class="caret"></span>
                                            </a>
                                            <ul class="dropdown-menu" id="a-drop">
                                                <li><a href="/v2/admin/domains">Domain Management</a></li>
                                                <li><a href="/v2/admin/users">User Management</a></li>
                                                <li><a href="/v2/campaign/bidadjustment">Bid adjustment</a></li>
                                                 
                                                <li><a href="/v2/admin/network_management">Network Management</a></li>
                                                <li><a href="/v2/admin/edit_multiple_networks?user_id={$user.id}">Multiple Network Management</a></li>
                                            </ul>
                                        </li>
                                        {/if}
                                        <li><a href="/v2/profile/index">Profile</a></li>
                                        {/if}
                                    </ul>
                                    {/if}
                                    <a class="logout-button" href="/auth/logout"><button class="btn btn-success pull-right"><i class="fa fa-user"></i> Logout</button></a>
                                </div><!-- /.navbar-collapse -->
                            </div><!-- /.container-fluid -->
                        </nav>
                    </div>
                </div>
            </header>

