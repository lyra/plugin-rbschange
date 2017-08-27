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
 * payzen_patch_0362
 * @package modules.payzen.patch
 */
class payzen_patch_0362 extends patch_BasePatch {
	/**
	 * Entry point of the patch execution.
	 */
	public function execute() {
		try {
			$this->beginTransaction();
		
			$this->deleteLists();
			
			// Delete all payzenconnector instances
			$query = "DELETE FROM `m_payment_doc_connector` WHERE `document_model` = 'modules_payzen/payzenconnector'";
			$this->executeSQLQuery($query);
			
			$query = "DELETE FROM `f_relation` WHERE `document_model_id1` = 'modules_payzen/payzenconnector' OR  `document_model_id2` = 'modules_payzen/payzenconnector'";
			$this->executeSQLQuery($query);
			
			$query = "DELETE FROM `f_document` WHERE `document_model` = 'modules_payzen/payzenconnector'";
			$this->executeSQLQuery($query);
			
			// Delete payzen payment custom fields 
			$query = "ALTER TABLE `m_payment_doc_connector` " . 
							"DROP `payzen_developped_by`, " . 
							"DROP `payzen_contact`, " .
							"DROP `payzen_contrib_version`, " . 
							"DROP `payzen_platform_version`, " . 
							"DROP `payzen_cms_version`, " . 
							"DROP `payzen_site_id`, " . 
							"DROP `payzen_key_test`, " . 
							"DROP `payzen_key_prod`, " . 
							"DROP `payzen_ctx_mode`, " . 
							"DROP `payzen_platform_url`, " . 
							"DROP `payzen_language`, " . 
							"DROP `payzen_available_languages`, " . 
							"DROP `payzen_capture_delay`, " . 
							"DROP `payzen_validation_mode`, " . 
							"DROP `payzen_payment_cards`, " . 
							"DROP `payzen_redirect_enabled`, " . 
							"DROP `payzen_redirect_success_timeout`, " . 
							"DROP `payzen_redirect_error_timeout`, " . 
							"DROP `payzen_redirect_success_message`, " . 
							"DROP `payzen_redirect_error_message`, " . 
							"DROP `payzen_return_mode`, " . 
							"DROP `payzen_url_check`";
			$this->executeSQLQuery($query);
			
			$rootNodeId = $this->getPersistentProvider()->getSettingValue('modules_payzen', ModuleService::SETTING_ROOT_FOLDER_ID);
			
			if($rootNodeId) {
				TreeService::getInstance()->clearRootNode($rootNodeId);
				
				$query = "DROP TABLE IF EXISTS `f_tree_". $rootNodeId . "`";
				$this->executeSQLQuery($query);
			}
			
			$query = "DELETE FROM `f_settings` WHERE `package` LIKE '%payzen%'";
			$this->executeSQLQuery($query);
			
			@unlink(AG_MODULE_DIR . DIRECTORY_SEPARATOR . 'payzen');
			
			$this->commit();
		} catch (Exception $e) {
			$this->log("\n An error has occured. Have you executed this patch twice ? \n");
			$this->logError($e->getMessage());
			$this->rollBack($e);
		}
	}
	
	private function deleteLists() {
		$list = list_ListService::getInstance()->getByListId('modules_payzen/languages');
		if ($list !== null) {
			$list->delete();
		}
		
		$list = list_ListService::getInstance()->getByListId('modules_payzen/ctxmode');
		if ($list !== null) {
			$list->delete();
		}
		
		$list = list_ListService::getInstance()->getByListId('modules_payzen/validationmode');
		if ($list !== null) {
			$list->delete();
		}
		
		$list = list_ListService::getInstance()->getByListId('modules_payzen/redirectenabled');
		if ($list !== null) {
			$list->delete();
		}
		
		$list = list_ListService::getInstance()->getByListId('modules_payzen/returnoption');
		if ($list !== null) {
			$list->delete();
		}
		
		$list = list_ListService::getInstance()->getByListId('modules_payzen/cartes');
		if ($list !== null) {
			$list->delete();
		}
	}
	
	/**
	 * @return boolean
	 */
	public function isCodePatch() {
		return true;
	}
}