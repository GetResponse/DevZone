<?php

/*
 * This module integrate GetResponse and PrestaShop
 * Allows subscribe via checkout page and export your contacts to GetResponse's campaign.
 */


if(!class_exists('GetresponseDatabaseModule.php'))
{
    require_once(dirname(__FILE__).'/../../modules/getresponse/GetresponseDatabaseModule.php');
}

class GetresponseSubTab extends AdminTab
{
    public function __construct()
    {
        // structure
        $this->table         = 'getresponse_settings';
        $this->className     = 'GetresponseSubTabClass';
        $this->edit          = false;
        $this->delete        = true;
        $this->identifier    = 'api_key';

        $instance            = Db::getInstance();
        $this->db            = new DbConnection($instance);

        parent::__construct();
    }

    /*
     * Main view
     */
    public function displayList()
    {
        global $currentIndex;

        $url = $currentIndex. '&token=' .$this->token;
        $msg = new GetresponseError();

        if (isset($_POST['updategetresponse_settings']))
        {
            if ( !empty($_POST['api_key']))
            {
                $c = $this->db->getCampaigns($_POST['api_key']);
                if ( is_array($c))
                {
                    $this->db->updateApikey($_POST['api_key']);
                    $this->apikey = $_POST['api_key'];
                    echo $msg->success('API Key update successful');
                }
                else
                {
                    echo $msg->error_msg('Wrong API key');
                }
            }
            else
            {
                echo $msg->error_msg('Api Key field can\'t be empty');
            }
        }
        else if (isset($_POST['exportgetresponse_settings']))
        {
            $settings  = $this->db->getSettings();

            if ( !empty($settings))
            {
                $this->apikey = $settings['api_key'];
            }
            else
            {
                echo $msg->error_msg('Wrong API Key');
            }

            if ((isset($_POST['campaign']) and $_POST['campaign'] == '0'))
            {
                echo $msg->error_msg('No campaign selected');
            }
            else
            {
                $contacts = $this->db->getContacts();

                if ( empty($contacts))
                {
                    echo $msg->error_msg('No contacts to export');
                }
                else
                {
                    $add = $this->db->exportSubscriber($this->apikey, $_POST['campaign'], $contacts);

                    if ($add['status'] == 1 )
                    {
                        echo $msg->success($add['message']);
                    }
                    else
                    {
                        echo $msg->error_msg($add['message']);
                    }
                }
            }
        }
        else if (isset($_POST['ordergetresponse_settings']))
        {
            $settings  = $this->db->getSettings();

            if ( !empty($settings))
            {
                $this->apikey = $settings['api_key'];
            }
            else
            {
                echo $msg->error_msg('Wrong API Key');
            }

            if (isset($_POST['order_campaign']) and $_POST['order_campaign'] != '0' and isset($_POST['order_status']))
            {
                $update_address = empty($_POST['update_address']) ? 'no' : $_POST['update_address'];

                $this->db->updateSettings($_POST['order_status'], $_POST['order_campaign'], $update_address);
                $this->db->updateCustoms($_POST);

                echo $msg->success('Settings update successful');
            }
            else if (isset($_POST['order_campaign']) and $_POST['order_campaign'] == '0')
            {
                echo $msg->error_msg('No campaign selected');
            }

        }

        // apikey settings
        $settings  = $this->db->getSettings();
        if ( !empty($settings))
        {
            $this->apikey = $settings['api_key'];
        }

        $custom_fields = $this->db->getCustoms();
        if ( !empty($custom_fields))
        {
            $new_inputs = '';
            $inputs = '';

            foreach ($custom_fields as $custom_field)
            {
                if ($settings['update_address'] == 'yes')
                {
                    $value = !empty($custom_field['custom_name']) ? $custom_field['custom_name'] : '';
                }
                else
                {
                    $value = $custom_field['custom_field'];
                }

                if ($custom_field['default'] == 'yes')
                {
                    $inputs .= '<input style="margin-bottom:5px" id="' . $custom_field['custom_value'] . '" name="export_' . $custom_field['custom_value'] . '" value="' . $custom_field['custom_field'] . '" disabled="disabled"></input><span style="color: #AAAAAA"> ' . ucfirst($custom_field['custom_field']) . '</span><br/>';
                    $new_inputs .= '<input style="margin-bottom:5px" id="' . $custom_field['custom_value'] . '" name="' . $custom_field['custom_value'] . '" value="' . $custom_field['custom_field'] . '" disabled="disabled"></input><span style="color: #AAAAAA"> ' . ucfirst($custom_field['custom_field']) . '</span><br/>';
                }
                else
                {
                    $inputs .= '<input style="margin-bottom:5px" id="' . $custom_field['custom_value'] . '" name="export_' . $custom_field['custom_value'] .'" value="' . $custom_field['custom_field'] . '"></input> ' . ucfirst($custom_field['custom_field']) . '<br/>';
                    $new_inputs .= '<input style="margin-bottom:5px" id="' . $custom_field['custom_value'] . '" name="' . $custom_field['custom_value'] .'" value="' . $value . '"></input> ' . ucfirst($custom_field['custom_field']) . '<br/>';
                }
            }
        }

        echo <<<APIFORM
        <div class="toolbarBox toolbarHead">
        <span style="font-size: 2em; text-shadow:0 1px 0 white;line-height:52px;padding-left:10px">GetResponse Settings & Actions</span>
        </div>
        <form id="form-api" action="$url" method="post" class="width2">
            <fieldset>
                <legend>{$this->l('API Key Settings')}</legend>
                    <label>{$this->l('Api Key: ')}</label>
                        <div class="margin-form">
                            <input id="api_key" name="api_key" value="{$this->apikey}" style="width: 150px"></input>
                            <sup>*</sup>
                    <br><br>
                    <input type="submit" value="{$this->l('Save')}" name="update{$this->table}" class="button" />

                    </div><div class="small">
                        <sup>*</sup>{$this->l('API Key can be found here: https://app.getresponse.com/my_api_key.html')}
                    </div>
            </fieldset>
        <script>
            window.onload = function()
            {
                setTimeout(function ()
                {
                    $('.conf').remove();
                    $('.error').remove();
                }, 7000);

                if ($('#update_address').is(':checked') == true)
                {
                    $('#update_extra').html('<br/><span style="color:black;font-size: 12px">Name your custom fields:<sup>**</sup><br/>$new_inputs</span>');
                    $('#update_sup_extra').html('<sup>**</sup>{$this->l('Empty input fields won\'t be updated.')}');
                }

            }
        </script>
APIFORM;

        // export data
        if ($this->apikey)
        {
            $campaigns  = $this->db->getCampaigns($this->apikey);

            foreach($campaigns as $campaign)
            {
                $Options .= '<option value="' . $campaign['id'] . '">' . $campaign['name'] . '</option>';

                $seleted = '';
                if ($campaign['id'] == $settings['campaign_id'] or isset($_POST['order_campaign']) and $_POST['order_campaign'] == $campaign['id'])
                {
                    $seleted = "selected";
                }

                $Options2 .= '<option value="' . $campaign['id'] .'"' .$seleted . '>' . $campaign['name'] . '</option>';
            }

            echo <<<EXPORTFORM
            <br/>
                <fieldset>
                    <legend>{$this->l('Export Customers')}</legend>
                        <label>{$this->l('Select target campaign: ')}</label>
                            <div class="margin-form">
                                <select id="campaign" name="campaign" style="width: 150px">
                                    $Options
                                </select>
                        <br><br>

                        <div id="extra"></div>

                        <input type="submit" value="{$this->l('Export')}" name="export{$this->table}" class="button" />
                        </div>
                        <div class="small">
                            <div id="sup_extra"></div>
                        </div>
                </fieldset>

            <br/>
            <script>
            $('#campaign').change(function()
            {
                if(($('#campaign').val() != '0'))
                {
                    $('#extra').html('<span style="color:black;font-size: 12px">Name your custom fields:<sup>*</sup><br/>$inputs</span>');
                    $('#sup_extra').html('<sup>*</sup>{$this->l('Empty input fields won\'t be added.')}');
                }
                else
                {
                    $('#extra,#sup_extra').empty();
                }
            });
            </script>
EXPORTFORM;

            // order form
            $opt_yes = '';
            $opt_no = '';
            $opt_update = '';

            if ($settings['active_subscription'] == 'yes' or isset($_POST['order_status']) and $_POST['order_status'] == 'yes')
            {
                $opt_yes = "selected";

                if ($settings['update_address'] == 'yes' or isset($_POST['update_address']) and $_POST['update_address'] == 'yes')
                {
                    $opt_update = 'checked';
                }
            }
            else
            {
                $opt_no = "selected";
            }

            echo <<<ORDERFORM
            <br/>
            <fieldset>
                    <legend>{$this->l('Subscription via registration page')}</legend>
                        <label>{$this->l('Select target campaign: ')}</label>
                            <div class="margin-form">
                                <select id="order_campaign" name="order_campaign" style="width: 150px">
                                    $Options2
                                </select>
                        <br/>
                        </div>
                        <label>{$this->l('Subscription: ')}</label>
                            <div class="margin-form">
                                <select id="order_status" name="order_status" style="width: 150px">
                                    <option value="no" {$opt_no}>{$this->l('disabled')}</option>
                                    <option value="yes" {$opt_yes}>{$this->l('enabled')}</option>
                                </select>
                                <sup>*</sup>
                        <br/>
                        </div>
                        <label>{$this->l('Update contact data on checkout page:')}</label>
                            <div class="margin-form">
                                <input form="form-api" type="checkbox" name="update_address" id="update_address" value="yes" {$opt_update}/>
                                <div id="update_extra"></div>
                        <br>
                        <input type="submit" value="{$this->l('Save')}" name="order{$this->table}" class="button" />
                        </div>
                        <div class="small">
                            <sup>*</sup>{$this->l('If update isn\'t selected, only the following data will be imported: firstname, lastname, email.')}
                            <br/>
                            <div id="update_sup_extra"></div>
                        </div>
                </fieldset>
            </form>
            <br/>
            <script>
            $('#update_address').change(function()
            {
                if($('#update_address').is(':checked') == true)
                {
                    $('#update_extra').html('<br/><span style="color:black;font-size: 12px">Name your custom fields:<sup>**</sup><br/>$new_inputs</span>');
                    $('#update_sup_extra').html('<sup>**</sup>{$this->l('Empty input fields won\'t be updated.')}');
                }
                else
                {
                    $('#update_extra,#update_sup_extra').empty();
                }
            });

            $('#order_status').change(function()
            {
                if($('#order_status').val() == 'no')
                {
                    $('#update_extra,update_sup_extra').empty();
                    $('#update_address').removeAttr('checked');
                }
            });

            </script>
ORDERFORM;

        }

    }
}
