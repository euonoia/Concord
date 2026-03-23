<?php
$cols = DB::select("show columns from employee_training_scores_hr2");
foreach ($cols as $c) {
    echo $c->Field . ' : ' . $c->Type . "\n";
}
