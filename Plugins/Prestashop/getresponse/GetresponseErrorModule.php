<?php

/*
 * This module for exepctions
 */

class GetresponseError extends Exception
{
    public function __construct()
    {
        parent::__construct();
    }

    public function error_msg($message)
    {
        print '<div class="error"><span style="float:right">';
        print '</a></span><img src="../img/admin/error2.png">';
        print $message;
        print '</div>';

    }

    public function success($message)
    {
        print '<div class="conf">';
        print '<img src="../img/admin/ok2.png" alt="">';
        print $message;
        print '</div>';
    }
}