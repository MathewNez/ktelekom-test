<?php
function process_query($conn, $query, $mode)
{
    $result = mysqli_query($conn, $query);
    $retval = [];
    switch ($mode) {
        case 'all':
            $retval = mysqli_fetch_all($result);
            break;
        case 'one':
            $retval = mysqli_fetch_row($result) [0];
            break;
    }
    mysqli_free_result($result);
    return $retval;
}
