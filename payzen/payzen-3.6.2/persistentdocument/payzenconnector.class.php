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
 * payment_persistentdocument_payzenconnector
 * @package payment.persistentdocument
 */
class payzen_persistentdocument_payzenconnector extends payzen_persistentdocument_payzenconnectorbase {
	public function getTemplateViewName() {
		return 'Payzen';
	}
}