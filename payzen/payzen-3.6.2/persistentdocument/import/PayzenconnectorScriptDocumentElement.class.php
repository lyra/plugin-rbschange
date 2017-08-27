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
 * payzen_PayzenconnectorScriptDocumentElement
 * @package modules.payzen.persistentdocument.import
 */
class payzen_PayzenconnectorScriptDocumentElement extends import_ScriptDocumentElement {
    /**
     * @return payzen_persistentdocument_payzenconnector
     */
    protected function initPersistentDocument() {
    	return payzen_PayzenconnectorService::getInstance()->getNewDocumentInstance();
    }
    
    /**
	 * @return f_persistentdocument_PersistentDocumentModel
	 */
	protected function getDocumentModel() {
		return f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName('modules_payzen/payzenconnector');
	}
}