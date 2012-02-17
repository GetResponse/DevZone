<?php

/**
 * Add contact to your GetResponse campaign when order is made.
 *
 * @author Pawel Pabian
 * http://implix.com
 * http://dev.getresponse.com
 */

require_once(DIR_FS_CATALOG.'includes/classes/jsonRPCClient.php');

    class ot_getresponse {

        function ot_getresponse() {
            $this->code = 'ot_getresponse';
            $this->title = MODULE_ORDER_TOTAL_GETRESPONSE_TITLE;
            $this->description = MODULE_ORDER_TOTAL_GETRESPONSE_DESCRIPTION;

            if (MODULE_ORDER_TOTAL_GETRESPONSE_STATUS == 'true') {
                $this->enabled = true;
            }
            else {
                $this->enabled = false;
            }
        }

        function process() {
            global $order;

            if(!$this->enabled) {
                return;
            }

            $client = new jsonRPCClient('http://api2.getresponse.com');
            $result = NULL;

            try {
                $result = $client->get_campaigns(
                    MODULE_ORDER_TOTAL_GETRESPONSE_API_KEY,
                    array (
                        'name' => array (
                            'EQUALS' => MODULE_ORDER_TOTAL_GETRESPONSE_CAMPAIGN
                        )
                    )
                );

                if (empty($result)) {
                    throw new Exception(
                        'Missing GetResponse campaign: ' .
                        MODULE_ORDER_TOTAL_GETRESPONSE_CAMPAIGN
                    );
                }

                $client->add_contact(
                    MODULE_ORDER_TOTAL_GETRESPONSE_API_KEY,
                    array (
                        'campaign'  => array_pop(array_keys($result)),
                        'name'      => $order->customer['firstname'] . ' ' . $order->customer['lastname'],
                        'email'     => $order->customer['email_address'],
                        'cycle_day' => '0',
                        'customs' => array(
                            array(
                                'name'       => 'ref',
                                'content'    => STORE_NAME
                            ),
                            array(
                                'name'       => 'telephone',
                                'content'    => $order->customer['telephone']
                            ),
                            array(
                                'name'       => 'country',
                                'content'    => $order->customer['country']['title']
                            ),
                            array(
                                'name'       => 'city',
                                'content'    => $order->customer['city']
                            )
                        )
                    )
                );
            }
            catch (Exception $e) {
                error_log($e->getMessage());

                return;
            }
        }

        function check() {
            $check_query = tep_db_query(
                "SELECT configuration_value " .
                "FROM " . TABLE_CONFIGURATION . " " .
                "WHERE configuration_key = 'MODULE_ORDER_TOTAL_GETRESPONSE_STATUS'"
            );

            $this->_check = tep_db_num_rows($check_query);

            return $this->_check;
        }

        function keys() {
            return array(
                'MODULE_ORDER_TOTAL_GETRESPONSE_STATUS', 
                'MODULE_ORDER_TOTAL_GETRESPONSE_API_KEY',
                'MODULE_ORDER_TOTAL_GETRESPONSE_CAMPAIGN',
            );
        }

        function install() {
            tep_db_query(
                "INSERT INTO " . TABLE_CONFIGURATION . " " .
                "(
                    configuration_title,
                    configuration_key,
                    configuration_value,
                    configuration_description,
                    configuration_group_id,
                    sort_order,
                    set_function,
                    date_added
                ) " .
                "VALUES " .
                "(
                    'Active',
                    'MODULE_ORDER_TOTAL_GETRESPONSE_STATUS',
                    'true',
                    'Activate module?',
                    '6',
                    '1',
                    'tep_cfg_select_option(array(\'true\', \'false\'), ', NOW()
                ), " .
                "(
                    'API key',
                    'MODULE_ORDER_TOTAL_GETRESPONSE_API_KEY',
                    'ENTER_YOUR_API_KEY_HERE',
                    'Click <a href=\"http://www.getresponse.com/my_api_key.html\">HERE</a> to get your API key',
                    '6',
                    '2',
                    NULL,
                    NOW()
                ), " .
                "(
                    'Campaign',
                    'MODULE_ORDER_TOTAL_GETRESPONSE_CAMPAIGN',
                    NULL,
                    'Name of the campaign to which customers will be added',
                    '6',
                    '3',
                    NULL,
                    NOW()
                );"
            );
        }

        function remove() {
            foreach ($this->keys() as $key) {
                tep_db_query(
                    "DELETE FROM " . TABLE_CONFIGURATION . " " . 
                    "WHERE configuration_key = '$key'"
                );
            }
        }

    }

?>
