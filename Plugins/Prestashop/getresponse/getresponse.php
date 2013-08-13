<?php
/*
 * This module hooks into the newOrder to add the customers
 * @author	 Grzegorz Struczynski <gstruczynski@implix.com>
 * @copyright  GetResponse
 * @license	http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_'))
	exit;

define('_PS_CLASS_DIR', dirname(__FILE__).'/../../classes');

class Getresponse extends Module
{
	public function __construct()
	{
		$this->name          = 'getresponse';
		$this->tab           = 'emailing';
		$this->version       = '2.1';
		$this->author        = 'GetResponse';
		$this->need_instance = 0;
		$this->displayName   = $this->l('GetResponse Integration');
		$this->description   = $this->l('Connects PrestaShop with GetResponse.');
		parent::__construct();

		require_once(dirname(__FILE__).'/GetresponseDatabaseModule.php');
		$instance = Db::getInstance();
		$this->db = new DbConnection($instance);
	}

/******************************************************************/
/** Install Methods ***********************************************/
/******************************************************************/

	private function installModuleTab($tab_class, $tab_name, $id_tab_name)
	{
		copy(_PS_MODULE_DIR_.$this->name.'/img/logo.png', _PS_IMG_DIR_.'t/'.$tab_class.'.gif');
		copy(_PS_MODULE_DIR_.$this->name.'/img/webform.png', _PS_IMG_DIR_.'t/Getresponse_webform.gif');

		$tab = new Tab();

		$tab->name = $tab_name;
		$tab->class_name = $tab_class;
		$tab->id_parent = $id_tab_name;
		if (!$tab->save())
			return false;
		return true;
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('newOrder') || !$this->registerHook('createAccount') ||
			$this->registerHook('leftColumn') == false || $this->registerHook('rightColumn') == false || $this->registerHook('header') == false)
			return false;

		//Update Version Number
		if (!Configuration::updateValue('GR_VERSION', $this->version))
			return false;

		$sql = array();

		//create getresponse settings table in Database
		$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'getresponse_settings` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`api_key` char(32) NOT NULL,
			`active_subscription` enum(\'yes\',\'no\') NOT NULL DEFAULT \'no\',
			`update_address` enum(\'yes\',\'no\') NOT NULL DEFAULT \'no\',
			`campaign_id` char(5) NOT NULL,
			PRIMARY KEY (`id`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

		$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'getresponse_customs` (
			`id_custom` int(11) NOT NULL AUTO_INCREMENT,
			`custom_field` char(32) NOT NULL,
			`custom_value` char(32) NOT NULL,
			`custom_name` char(32) NOT NULL,
			`default` enum(\'yes\',\'no\') NOT NULL DEFAULT \'no\',
			PRIMARY KEY (`id_custom`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

		$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'getresponse_webform` (
			`id` int(6) NOT NULL AUTO_INCREMENT,
			`webform_id` int(6) NOT NULL,
			`active_subscription` enum(\'yes\',\'no\') NOT NULL DEFAULT \'no\',
			`sidebar` enum(\'left\',\'right\') NOT NULL DEFAULT \'left\',
			`style` enum(\'webform\',\'prestashop\') NOT NULL DEFAULT \'webform\',
			PRIMARY KEY (`id`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

		$sql[] = 'INSERT INTO `'._DB_PREFIX_.'getresponse_settings` (
				`id` ,
				`api_key` ,
				`active_subscription` ,
				`update_address` ,
				`campaign_id`
				)
				VALUES (
				\'1\',  \'\',  \'no\', \'no\',  \'0\'
				)
				ON DUPLICATE KEY UPDATE
				`id` = `id`;
			)';

		$sql[] = 'INSERT INTO  `'._DB_PREFIX_.'getresponse_customs` (
				`id_custom` ,
				`custom_field`,
				`custom_value`,
				`custom_name`,
				`default`
				)
				VALUES
				(\'1\', \'firstname\', \'firstname\', \'firstname\', \'yes\'),
				(\'2\', \'lastname\', \'lastname\', \'lastname\', \'yes\'),
				(\'3\', \'email\', \'email\', \'email\', \'yes\'),
				(\'4\', \'address\', \'address1\', \'address\', \'no\'),
				(\'5\', \'postal\', \'postcode\', \'postal\', \'no\'),
				(\'6\', \'city\', \'city\', \'city\', \'no\'),
				(\'7\', \'phone\', \'phone\', \'phone\', \'no\'),
				(\'8\', \'country\', \'country\', \'country\', \'no\'),
				(\'9\', \'birthday\', \'birthday\', \'birthday\', \'no\'),
				(\'10\', \'company\', \'company\', \'company\', \'no\')';

		$sql[] = 'INSERT INTO  `'._DB_PREFIX_.'getresponse_webform` (
				`id` ,
				`webform_id` ,
				`active_subscription` ,
				`sidebar`,
				`style`
				)
				VALUES (
				\'1\',  \'\',  \'no\',  \'left\',  \'webform\'
				)
				ON DUPLICATE KEY UPDATE
				`id` = `id`;
			)';

		//Install SQL
		foreach ($sql as $s)
		{
			if (!Db::getInstance()->Execute($s))
				return false;
		}

		//Move class and tab files to proper folders within PrestaShop
		if (!copy(dirname(__FILE__).'/GetresponseSubTab.php', _PS_ADMIN_DIR_.'/tabs/GetresponseSubTab.php'))
			return false;

		//Create Admin Tabs
		if (!$this->installModuleTab('GetresponseTab', array(1 => 'GetResponse'), 0))
			return false;

		if (!$this->installModuleTab('GetresponseSubTab', array(1 => 'Settings & Actions'), Tab::getIdFromClassName('GetresponseTab')))
			return false;

		return true;
	}

/******************************************************************/
/** Uninstall Methods *********************************************/
/******************************************************************/

	private function uninstallModuleTab($tab_class)
	{
		$id_tab = Tab::getIdFromClassName($tab_class);
		if ($id_tab != 0)
		{
			$tab = new Tab($id_tab);
			$tab->delete();
			return true;
		}

		return false;
	}

	public function uninstall()
	{
		if (!parent::uninstall() || !$this->unregisterHook('newOrder') || !$this->registerHook('createAccount'))
			return false;

		//Delete Version Entry
		if (!Configuration::deleteByName('GR_VERSION'))
			return false;

		// Uninstall SQL
		$sql = array();
		$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'getresponse_settings`;';
		$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'getresponse_customs`;';
		$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'getresponse_webform`;';

		foreach ($sql as $s)
		{
			if (!Db::getInstance()->Execute($s))
				return false;
		}

		//Remove all files
		if (!unlink(_PS_ADMIN_DIR_.'/tabs/GetresponseSubTab.php'))
			return false;

		//Delete Admin Tabs
		if (!$this->uninstallModuleTab('GetresponseTab'))
			return false;

		if (!$this->uninstallModuleTab('GetresponseSubTab'))
			return false;

		return true;
	}

/******************************************************************/
/** Hook Methods **************************************************/
/******************************************************************/

	public function hookNewOrder($params)
	{
		$this->addSubscriber($params, 'order');
	}

	public function hookCreateAccount($params)
	{
		$this->addSubscriber($params, 'create');
	}

	private function addSubscriber($params, $action)
	{
		$settings = $this->db->getSettings();

		if (!empty($settings['api_key']))
		{
			if (isset($settings['active_subscription']) && $settings['active_subscription'] == 'yes' && !empty($settings['campaign_id']))
				$this->db->addSubscriber($params, $settings['api_key'], $settings['campaign_id'], $action);
		}
	}

	public function hookDisplayRightColumn()
	{
		$webform_settings  = $this->db->getWebformSettings();

		if (!empty($webform_settings) && $webform_settings['active_subscription'] == 'yes' && $webform_settings['sidebar'] == 'right')
			return $this->displayWebform($webform_settings);
	}

	public function hookDisplayLeftColumn()
	{
		$webform_settings  = $this->db->getWebformSettings();

		if (!empty($webform_settings) && $webform_settings['active_subscription'] == 'yes' && $webform_settings['sidebar'] == 'left')
			return $this->displayWebform($webform_settings);
	}

	private function displayWebform($webform_settings)
	{
		if (empty($webform_settings) || !is_array($webform_settings))
			return false;

		$set_style = null;
		if (!empty($webform_settings['style']) && $webform_settings['style'] == 'prestashop')
			$set_style = '&css=1';

		$this->smarty->assign(array('webform_id' => $webform_settings['webform_id'], 'style' => $set_style));

		return $this->display(__FILE__, 'views/templates/admin/webform.tpl');
	}
}
