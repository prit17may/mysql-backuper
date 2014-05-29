<?php
$col = 12;
?>
<div class="container">
    <div class="row">
        <div class="col-lg-<?php echo $col ?> col-md-<?php echo $col ?> col-sm-<?php echo $col ?>">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <button id="refresh_tables" type="button" class="btn btn-primary btn-xs pull-right"><i class="fa fa-refresh"></i></button>
                    <h3 class="panel-title">All Tables</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-condensed table-bordered" id="all_tables">
                            <tr>
                                <td><center>Loading Tables...</center></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
