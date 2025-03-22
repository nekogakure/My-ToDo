<?php
/* LOG用の関数 */
function log_write($log) {
    $log_file = 'My-ToDo.log';
    $log = date('[Y-m-d H:i:s]') . ' ' . $log . "\n";
    file_put_contents($log_file, $log, FILE_APPEND);
}