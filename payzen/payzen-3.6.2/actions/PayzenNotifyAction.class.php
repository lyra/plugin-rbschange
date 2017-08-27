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
 * payzen_PayzenNotifyAction
 * Instant payment notification class. Wait for PayZen payment confirmation, then validate order.
 * @package modules.payzen.actions
 */
class payzen_PayzenNotifyAction extends f_action_BaseAction {
	
	/**
	 * @see f_action_BaseAction::_execute()
	 *
	 * @param Context $context
	 * @param Request $request
	 */
	protected function _execute($context, $request) {
		$remoteAddr = $_SERVER['REMOTE_ADDR'];
		$custId = $request->getParameter('vads_cust_id');
		
		$ms = payment_ModuleService::getInstance();
		
		$ms->log("PayZen PAYMENT : server call received from [$remoteAddr], custId: $custId, data: " . print_r($request->getParameters(), true));
		
		if ((stripos($request->getParameter('vads_payment_config'), 'MULTI') !== false)
				|| (stripos($request->getParameter('vads_contrib'), 'multi') !== false)) {
		
			// Multi payment : let multi module finish the work
			$ms->log("PayZen PAYMENT : order initiated by multiple-payment module, let it process payment.");
			
			// TODO redirect to multi module
			die();
		}

		if (($orderId = $request->getParameter('vads_order_id')) && ($hash = $request->getParameter('vads_hash'))) {
			$connectorService = payzen_PayzenconnectorService::getInstance();
			
			// Reload order info
			$order = $connectorService->getPaymentOrderById($orderId);
			if ($order === null) {
				die('<span style="display:none">KO-' . $hash . "=Impossible de retrouver la commande\n</span>");
			}
			
			// Get connector instance
			$connector = $order->getPaymentConnector();
			if (!($connector instanceof payzen_persistentdocument_payzenconnector)) {
				die('<span style="display:none">KO-' . $hash . "=Connecteur de paiement incorrect\n</span>");
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
				die($payzenResponse->getOutputForGateway('auth_fail'));
			}
		
			if ($order->getPaymentStatus() == order_OrderService::INITIATED || $order->getPaymentStatus() == order_OrderService::CANCELED) { // Order has not been accepted yet
				// create payment_transaction object
				if(! $connectorService->validatePayment($payzenResponse)) {
					// Payment KO
					die($payzenResponse->getOutputForGateway('ko', 'Une exception s\'est produite lors de l\'enregistrement de la commande.'));;
				} elseif ($payzenResponse->isAcceptedPayment()) {
					// Payment OK
					die ($payzenResponse->getOutputForGateway('payment_ok'));
				} else {
					// Payment KO
					die($payzenResponse->getOutputForGateway('payment_ko'));
				}
			} else {
				// Order already registered
				$ms->log("PayZen PAYMENT : order " . $payzenResponse->get('order_id') . " has already been processed.");
				
				if($payzenResponse->isAcceptedPayment()) {
					// Just display a confirmation message
					die($payzenResponse->getOutputForGateway('payment_ok_already_done'));
				} else {
					// Order saved with success status while payment failed
					die($payzenResponse->getOutputForGateway('payment_ko_on_order_ok'));
				}
			}
		}
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
}