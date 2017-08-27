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
 * payzen_PayzenReturnAction
 * Payment return class. Wait for PayZen payment return, validate order (if not already done) and redirect to payment result page.
 * @package modules.payzen.actions
 */
class payzen_PayzenReturnAction extends f_action_BaseAction {
	
	/**
	 * @see f_action_BaseAction::_execute()
	 *
	 * @param Context $context
	 * @param Request $request
	 */
	protected function _execute($context, $request) {
		$remoteAddr = $_SERVER['REMOTE_ADDR'];
		$requestUri = $_SERVER['REQUEST_URI'];
		$custId = $request->getParameter('vads_cust_id');
		
		$ms = payment_ModuleService::getInstance();
		$ms->log("PayZen PAYMENT : client return from payment platform, custId: $custId, remoteData: [$remoteAddr : $requestUri]");
		
		if (($orderId = $request->getParameter('vads_order_id'))) {
			$connectorService = payzen_PayzenconnectorService::getInstance();
			
			// Reload order info
			$order = $connectorService->getPaymentOrderById($orderId);
			if ($order === null) {
				$this->redirectToErrorURL($context);
			}
			
			// Get connector instance
			$connector = $order->getPaymentConnector();
			if (!($connector instanceof payzen_persistentdocument_payzenconnector)) {
				$this->redirectToErrorURL($context);
			}
			
			/** @var PayzenResponse $payzenResponse */
			$payzenResponse = new PayzenResponse(
					$request->getParameters(),
					$connector->getPayzen_ctx_mode(),
					$connector->getPayzen_key_test(),
					$connector->getPayzen_key_prod()
			);
			
			// Check the authenticity of the request
			if (!$payzenResponse->isAuthentified()) {
				$this->redirectToErrorURL($context);
			}
			
			if ($order->getPaymentStatus() == order_OrderService::INITIATED || $order->getPaymentStatus() == order_OrderService::CANCELED) { // Order has not been accepted yet
				// create payment_transaction object
				if(! $connectorService->validatePayment($payzenResponse)) {
					$url = $this->getErrorURL();
					
				} else {
					$url = $order->getPaymentCallbackURL();
				}
			} else {
				// Order already registered
				
				if($payzenResponse->isAcceptedPayment()) {
					// Just display a confirmation message
					$url = $order->getPaymentCallbackURL();
				} else {
					// Order saved with success status while payment failed
					$url = $this->getErrorURL();
				}
			}
		}
		
		$context->getController()->redirectToUrl($url);
		
		return VIEW::NONE;
	}

	/**
	 * @return Integer
	 */
	public function getRequestMethods() {
		return Request::POST | Request::GET;
	}

	/**
	 * @return Boolean
	 */
	public final function isSecure() {
		return false;
	}
	
	private function getErrorURL() {
		$currentWebsite = website_WebsiteModuleService::getInstance()->getCurrentWebsite();
 		return $currentWebsite->getUrlForLang(RequestContext::getInstance()->getLang());
	}
	
	private function redirectToErrorURL($context) {
		$context->getController()->redirectToUrl($this->getErrorURL());
		die();
	}
	
}