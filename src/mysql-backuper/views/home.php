<div class="container">
    <div class="page-header">
        <button id="backup_now" class="btn btn-success pull-right hidden-xs"><i class="fa fa-download"></i> Backup Now!</button>
        <button class="btn btn-success btn-xs pull-right visible-xs" type="button" onclick="$(this).prev().click();">Backup Now</button>
        <h1 class="hidden-xs">mysql Backuper<small><em><sup> db (<?php echo database_name ?>)</sup></em></small></h1>
        <h3 class="visible-xs">mysql Backuper<small><em><sup> db (<?php echo database_name ?>)</sup></em></small></h3>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <button id="refresh_backups" type="button" class="btn btn-primary btn-xs pull-right"><i class="fa fa-refresh"></i></button>
                <h3 class="panel-title">Previous Backup Dates</h3>
            </div>
            <div class="panel-body" style="max-height:265px;overflow:auto">
                <div class="table-responsive">
                    <table class="table table-hover table-condensed table-bordered" id="backup_dates_table"></table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <button id="refresh_date_backups" data-date="" type="button" class="btn btn-primary btn-xs pull-right"><i class="fa fa-refresh"></i></button>
                <h3 class="panel-title">Backups<span id="num_backups"></span></h3>
            </div>
            <div class="panel-body" style="max-height:265px;overflow:auto">
                <div class="table-responsive">
                    <table class="table table-hover table-condensed table-bordered" id="date_backups_table"></table>
                </div>
            </div>
        </div>
    </div>
</div>
