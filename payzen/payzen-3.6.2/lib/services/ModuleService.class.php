<?php
#####################################################################################################
#
#					Module pour la plateforme de paiement PayZen
#						Version : 1.0a (révision 41778)
#									########################
#					Développé pour RBSChange
#						Version : 3.6.2
#						Compatibilité plateforme : V2
#									########################
#					Développé par Lyra Network
#						http://www.lyra-network.com/
#						13/12/2012
#						Contact : support@payzen.eu
#
#####################################################################################################

/**
 * payzen_ModuleService
 * @package modules.payzen.lib.services
 */
class payzen_ModuleService extends ModuleBaseService {
	/**
	 * Singleton
	 * @var payzen_ModuleService
	 */
	private static $instance = null;

	/**
	 * @return payzen_ModuleService
	 */
	public static function getInstance() {
		if (is_null(self::$instance)) {
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}
}