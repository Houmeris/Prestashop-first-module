<?php

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if(!defined('_PS_VERSION_'))
{
    exit;
}

class MartynPay extends PaymentModule
{
    private $_html = '';
    private $_postErrors = array();

    public $address;

    public function __construct()
    {
        $this->name = "martynpay";
        $this->tab = "payments_gateways";
        $this->version = "1.0";
        $this->author = "Martynas Dziugys";
        $this->controllers = array('payment', 'validation');
        $this->currencies = true;
        $this->currencies_mode = 'checkbox';
        $this->ps_versions_compliancy = [
            "min" => '1.7.0',
            "max" => _PS_VERSION_
        ];
        $this->bootstrap = true;
        $this->displayName = ("martynpay");
        $this->description = ("Sample Payment module developed for learning purposes.");
        $this->confirmUninstall = $this->l("Are you sure you want to uninstall?");

        parent::__construct();
    }

    public function install()
    {
        return parent::install()
        && $this->registerHook('paymentOptions')
        && $this->registerHook('paymentReturn');

    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function getContent()
    {
        return $this->_html;
    }

    public function hookPaymentOptions($params)
    {
        if(!$this->active)
        {
            return;
        }

        $formaction = $this->context->link->getModuleLink($this->name, 'validation', array(), true);
        $this->smarty->assign(['action' =>$formaction]);
        $paymentForm = $this->fetch('module:martynpay/views/templates/hook/payment_options.tpl');
        $paymentOptions = new PaymentOption;
        $paymentOptions->setModuleName($this->displayName)
            ->setCallToActionText($this->displayName)
            ->setAction($formaction)
            ->setForm($paymentForm);
        return [
            $paymentOptions
        ];
    }

    public function hookPaymentReturns($params)
    {
        
    }
}

?>