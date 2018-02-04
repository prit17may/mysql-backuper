<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--<link rel="shortcut icon" href="../../assets/ico/favicon.ico">-->
        <title>mysql Backuper</title>
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?php css_url('style.css') ?>">

    </head>
    <body>
        <div class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a href="<?php echo base_url() ?>" class="navbar-brand">mysql Backuper</a>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li class="<?php echo isset($vars[1]) && $vars[1] === 'tables' ? 'active' : '' ?>"><a href="<?php echo base_url('tables') ?>">Tables</a></li>
                        <li class="<?php echo isset($vars[1]) && $vars[1] === 'table_config' ? 'active' : '' ?>"><a href="<?php echo base_url('table_config') ?>">Table Config</a></li>
                    </ul>
                    <button id="del_db_config" type="button" class="btn btn-danger navbar-btn navbar-right hidden-xs"><i class="fa fa-warning"></i> Delete DataBase Configuration File</button>
                    <button class="btn btn-danger btn-xs navbar-btn navbar-right visible-xs" type="button" onclick="$(this).prev().click();">Delete Config File</button>
                </div>
            </div>
        </div>
        <noscript>
        <div class="container" style="padding: 70px 15px 0;">
            <div class="alert alert-danger" style="font-weight:bold;text-align:center;">Please enable Javascript for functionality of Backuper</div>
        </div>
        </noscript>
