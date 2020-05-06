<?php
	/**
	* Hello World! Module Entry Point
	*
	* @package    Joomla.Tutorials
	* @subpackage Modules
	* @license    GNU/GPL, see LICENSE.php
	* @link       http://docs.joomla.org/J3.x:Creating_a_simple_module/Developing_a_Basic_Module
	* mod_helloworld is free software. This version may have been modified pursuant
	* to the GNU General Public License, and as distributed it includes or
	* is derivative of works licensed under the GNU General Public License or
	* other free or open source software licenses.
	*/

	// No direct access
	defined('_JEXEC') or die;

	// Include the agenda functions only once
	JLoader::register('Mod_Agenda_Helper', __DIR__ . '/helper.php');

	$events = Mod_Agenda_Helper::getEvents($params);
	require JModuleHelper::getLayoutPath('mod_agenda');
   
	// get Params
	$doc			= JFactory::getDocument();
	$baseURL		= JUri::base(true);
	$icsFile		= htmlentities($params->get('icsFile',''), ENT_COMPAT, 'UTF-8');
	$cssFile		= htmlentities($params->get('cssFile',''), ENT_COMPAT, 'UTF-8');
	$columns		= htmlentities($params->get('columns',''), ENT_COMPAT, 'UTF-8');
	$categories		= htmlentities($params->get('categories',''), ENT_COMPAT, 'UTF-8');
	$timeZone		= htmlentities($params->get('timeZone',''), ENT_COMPAT, 'UTF-8');
	$timeFormat		= htmlentities($params->get('timeFormat',''), ENT_COMPAT, 'UTF-8');
	
	// Load CSS/JS
	$doc->addStyleSheet($baseURL . '/modules/mod_agenda/assets/css/' . $cssFile);
?>