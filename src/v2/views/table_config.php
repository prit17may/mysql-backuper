<?php
$tables = get_tables($database);
if (isset($vars[2]) && is_string($vars[2]) && is_table($database, $vars[2])) {
    $alt = TRUE;
    $table_name = $vars[2];
    $tables = array($table_name);
}
if (isset($_POST['num_cols']) && $_POST['num_cols']) {
    $_SESSION['num_cols'] = $_POST['num_cols'];
    if (isset($alt)) {
        $url = base_url("table_config/" . $table_name);
    } else {
        $url = base_url("table_config");
    }
    header("Location: " . $url);
    exit;
}
//get table config
$table_config = get_table_config();
if (isset($alt)) {
    $table_config = get_table_config($table_name);
    if (!empty($table_config))
        $table_config = array($table_name => $table_config);
}
if (empty($table_config)) {
    foreach ($tables as $table_name) {
        $table_config[$table_name] = array();
    }
}
$get_conf = $num_cols = isset($_SESSION['num_cols']) && $_SESSION['num_cols'] ? $_SESSION['num_cols'] : 6;
$num_cols_arr = array(1, 2, 3, 4, 6, 12);
$col_value = abs(12 / $num_cols);
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <?php if (isset($alt)): ?>
                    <a href="<?php echo base_url('table/' . $table_name) ?>" class="btn btn-link btn-primary text-muted"><i class="fa fa-arrow-left"></i> Back</a>
            <?php endif; ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="pull-right" style="color:#000">
                        <form method="post" name="num_col_selector">
                            <label>
                                <select name="num_cols" onchange="document.num_col_selector.submit()">
                                    <option value="">Cols</option>
                                    <?php foreach ($num_cols_arr as $value) { ?>
                                        <option <?php echo (isset($_SESSION['num_cols']) && $_SESSION['num_cols'] == $value) ? "selected" : "" ?>><?php echo $value; ?></option>
                                    <?php } ?>
                                </select>
                            </label>
                            <?php if (!isset($alt)): ?>
                                <button type="button" class="btn btn-success btn-xs hidden-xs" id="save_all_table_config" ><i class="fa fa-save"></i> Save All</button>
                            <?php endif; ?>
                        </form>
                    </div>
                    <h3 class="panel-title">Table Config <small style="color:darkkhaki">&LeftAngleBracket;Values for selected columns will be shown&RightAngleBracket;</small></h3>
                </div>
                <div class="panel-body">
                    <div class="panel-group" id="accordion">
                        <?php
                        foreach ($tables as $table_name) {
                            $cols = get_cols($database, $table_name);
                            ?>
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <div class="pull-right">
                                        <?php if (!has_only_pk($database, $table_name)) { ?><a href="javascript:void(0);" id="check_all" title="Check All" data-toggle="tooltip"><i class="fa fa-square-o"></i></a><a href="javascript:void(0);" id="uncheck_all" title="Uncheck All" data-toggle="tooltip" style="display:none"><i class="fa fa-check-square"></i></a> <button type="button" data-table_name="<?php echo $table_name ?>" class="btn btn-success btn-xs" id="save_table_config"><i class="fa fa-save"></i> Save</button><?php } ?>
                                    </div>
                                    <a data-toggle="collapse" href="#collapse<?php echo $table_name ?>">
                                        <?php echo $table_name ?>
                                    </a>
                                    <span title="Number of Columns" class="tip" data-placement="right">(<?php echo count($cols) ?>)</span>
                                </div>
                                <div id="collapse<?php echo $table_name ?>" class="panel-collapse collapse<?php echo!has_only_pk($database, $table_name) ? " in" : '' ?>">
                                    <div class="panel-body">
                                        <div class="row">
                                            <?php
                                            foreach ($cols as $col) {
                                                $is_pk = is_pk($database, $table_name, $col);
                                                ?>
                                                <div class="col-lg-<?php echo $col_value ?> col-md-<?php echo $col_value ?> col-sm-<?php echo $col_value ?> col-xs-<?php echo $col_value ?>" style="margin-bottom:0px<?php if ($col_value == 12 || $col_value == 6 || $col_value == 4 || $col_value == 4) { ?>;text-align:center<?php } ?>;">
                                                    <label style="padding:3px;border-radius:3px"><input type="checkbox"<?php echo $is_pk ? " checked disabled" : "" ?><?php echo in_array($col, $table_config[$table_name]) ? " checked" : "" ?> value="<?php echo $col ?>" /> <?php echo $is_pk ? '<span class="tip" style="color:#428bca" title="Primary Key" data-toggle="tooltip" data-placement="right">' . $col . '</span>' : $col ?></label>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
