<?php

class MartynpayValidationModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        //Get current cart object from session
        $cart = $this->context->cart;
        $authorized = false;

        // Verify if this module is enabled and if the cart has a valid customer, delivery address and invoice address
        if(!$this->module->active || $cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0)
        {
            Tools::redirect('index.php?controller=order&step=1');
        }

        // Verify if this payment module is authorized
        foreach (Module::getPaymentModules() as $module)
        {
            if($module['name'] == 'martynpay')
            {
                $authorized = true;
                break;
            }
        }

        if (!$authorized)
        {
            die($this->l('This payment method is not available.'));
        }

        $customer = new Customer($cart->id_customer);

        // Check if valid custommer account
        if (!Validate::isLoadedObject($customer))
        {
            Tools::redirect('index.php?controller=order&step=1');
        }

        // Place the order

        $this->module->validateOrder(
            (int) $this->context->cart->id,
            Configuration::get('PS_OS_PAYMENT'),
            (float) $this->context->cart->getOrderTotal(true, Cart::BOTH),
            $this->module->displayName,
            null,
            null,
            (int) $this->context->currency->id,
            false,
            $customer->secure_key
        );

        // Redirect the customer to the order confirmation page

        Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int)$cart->id.'&id_module='.(int)$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
    }
    
    public function hookPaymentReturn($params)
    {
        
    }
}

?>