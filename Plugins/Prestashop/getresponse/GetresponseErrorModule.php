<?php

/*
 * This module for exepctions
 * @author     Grzegorz Struczynski <gstruczynski@implix.com>
 * @copyright  GetResponse
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class GetresponseError extends Exception
{
	public function errorMsg($message)
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