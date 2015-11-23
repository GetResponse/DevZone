{*
* @author     Grzegorz Struczynski <gstruczynski@implix.com>
* @copyright  GetResponse
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<div id="getresponse_webform" class="block">
    {if isset($webform_id) and $webform_id > 0}
    <script type="text/javascript" src="http://app.getresponse.com/view_webform.js?wid={$webform_id}{$style}"></script>
    {/if}
</div>
{$style}