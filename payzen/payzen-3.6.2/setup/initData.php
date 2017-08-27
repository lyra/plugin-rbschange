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
 * @package modules.payzen.setup
 */
class payzen_Setup extends object_InitDataSetup {
	
	public function install() {
		$this->executeModuleScript('init.xml');
		
		$mbs = uixul_ModuleBindingService::getInstance();
		$mbs->addImportInPerspective('payment', 'payzen', 'payment.perspective');
		$mbs->addImportInActions('payment', 'payzen', 'payment.actions');
		$result = $mbs->addImportform('payment', 'modules_payzen/payzenconnector');
		
		if ($result['action'] == 'create') {
			uixul_DocumentEditorService::getInstance()->compileEditorsConfig();
		}
		f_permission_PermissionService::getInstance()->addImportInRight('payment', 'payzen', 'payment.rights');
		
		$srcPath = f_util_FileUtils::buildWebeditPath('modules', 'payzen', 'templates');
		$destPath  = f_util_FileUtils::buildOverridePath('modules', 'payment', 'templates');
		
		f_util_FileUtils::cp($srcPath . DIRECTORY_SEPARATOR . 'Payment-Block-Payment-Payzen.all.all.html',
				$destPath . DIRECTORY_SEPARATOR . 'Payment-Block-Payment-Payzen.all.all.html', f_util_FileUtils::OVERRIDE);
		
		f_util_FileUtils::cp($srcPath . DIRECTORY_SEPARATOR . 'Payment-Inc-Selection-Payzen.all.all.html',
				$destPath . DIRECTORY_SEPARATOR . 'Payment-Inc-Selection-Payzen.all.all.html', f_util_FileUtils::OVERRIDE);	
	}

	/**
	 * @return String[]
	 */
	public function getRequiredPackages() {
		// Return an array of packages name if the data you are inserting in
		// this file depend on the data of other packages.
		// Example:
		// return array('modules_website', 'modules_users');
		return array();
	}
}