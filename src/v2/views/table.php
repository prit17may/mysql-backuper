<?php
$table_name = $vars[2];
if (!is_table($database, $table_name)) {
    header("Location: " . base_url('404'));
    exit;
}
$cols = get_cols($database, $table_name);
$saved_fields = get_table_config($table_name);
if (!empty($saved_fields)) {
    $cols = $saved_fields;
    if (has_pk($database, $table_name))
        array_unshift($cols, get_pk($database, $table_name));
} else {
    if (has_pk($database, $table_name))
        $cols = array(get_pk($database, $table_name));
}
$all_data = $database->select($table_name, $cols);
//pr($all_data);
?>
<div class="container">
    <div class="row">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <a class="pull-right" href="<?php echo base_url('table_config/' . $table_name) ?>">Select Fields to Show</a>
                <h3 class="panel-title"><?php echo $table_name ?></h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-condensed table-bordered table-hover">
                        <thead>
                            <tr class="success">
                                <?php foreach ($cols as $col): ?>
                                    <th><?php echo $col ?></th>
                                <?php endforeach; ?>
                                    <th style="width:1px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_data as $data): ?>
                                <tr>
                                    <?php foreach ($data as $value): ?>
                                        <td><?php echo $value ?></td>
                                    <?php endforeach; ?>
                                    <td><a href="#">Delete</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
