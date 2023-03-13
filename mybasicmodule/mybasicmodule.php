<?php

/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

use PhpParser\Node\Expr\Cast\Bool_;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

if(!defined('_PS_VERSION_'))
{
    exit();
}

//The main class

class MyBasicModule extends Module implements WidgetInterface {

    // constructor

    public function __construct()
    {
        $this->name = "mybasicmodule";
        $this->tab = "front_office_features";
        $this->version = "1.0";
        $this->author = "Martynas Dziugys";
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            "min" => 1.7,
            "max" => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l("My very first module");
        $this->description = $this->l("This is a great testing module");
        $this->confirmUninstall = $this->l("Are you sure you want to uninstall?");
    }

    // install method
    public function install()
    {
        return
         $this->sqlInstall()
        && $this->installtab()
        && parent::install() 
        && $this->registerHook('registerGDPRConsent')
        && $this->registerHook('displayCheckoutSubtotalDetails')
        && $this->registerHook('moduleRoutes');
    }

    // uninstall method
    public function uninstall() : Bool
    {
        return $this->sqlUninstall() && $this->uninstalltab() && parent::uninstall();
    }

    // sql install
    protected function sqlInstall() 
    {
        /*$sqlCreate = "CREATE TABLE `" . _DB_PREFIX_ . "testcomment` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `user_id` varchar(255) DEFAULT NULL,
            `comment` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`id_sample`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";*/
        $sqlCreate = "CREATE TABLE  ps_testcomment(
            id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id VARCHAR(255) DEFAULT NULL,
            comment VARCHAR(255) DEFAULT NULL,
            PRIMARY KEY (id)
        )";

        return Db::getInstance()->execute($sqlCreate);
    }

    protected function sqlUninstall()
    {
        $sql = "DROP TABLE ps_testcomment";
        return Db::getInstance()-> execute($sql);
    }

    public function installtab()
    {
        $tab = new Tab();
        $tab->class_name = 'AdminTest';
        $tab->module = $this->name;
        $tab->id_parent = (int)Tab::getIdFromClassName('DEFAULT');
        $tab->icon = 'settings_applications';
        $languages = Language::getLanguages();
        foreach ($languages as $lang)
        {
            $tab->name[$lang['id_lang']] = $this->l('TEST Admin Controller');
        }

        try
        {
            return $tab->save();
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
            return false;
        }
    }

    public function uninstalltab()
    {
        $idTab = (int)Tab::getIdFromClassName('AdminTest');

        if($idTab)
        {
            $tab = new Tab($idTab);
            try
            {
                $tab->delete();
            }
            catch (Exception $e)
            {
                echo $e->getMessage();
                return false;
            }
        }
        return true;
    }

    /*public function hookdisplayFooter($params)
    {

        $this->context->smarty->assign([
            'myparamtest' => "Martynas Dziugys"
        ]);
        return $this->display(__FILE__, 'views/templates/hook/footer.tpl');
    }*/

    public function renderWidget($hookName, array $configuration)
    {
        echo $this->context->link->getModuleLink($this->name, "test");
        if($hookName === 'displayNavFullWidth')
        {
            return "Hello this is an exception from the displayNavFullWidth hook";
        }
        if (!$this->isCached("module:mybasicmodule/views/templates/hook/footer.tpl", $this->getCacheId($this->name)))
        {
            $this->context->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        }
        return $this->fetch("module:mybasicmodule/views/templates/hook/footer.tpl", $this->getCacheId('blockreassurance'));
    }
    public function getWidgetVariables($hookName, array $configuration)
    {
        return [
            'idcart' => $this->context->cart->id,
            'myparamtest' => "Prestashop developer"
        ];
    }

    // configuration page

    public function getContent()
    {

        /*$message = null;

        if(Tools::getValue("courserating"))
        {
            Configuration::updateValue('COURSE_RATING', Tools::getValue("courserating"));
            $message = "Form saved correctly";
        }

        // field: courserating
        $courserating = Configuration::get('COURSE_RATING');
        $this->context->smarty->assign([
            'courserating' => $courserating,
            'message' => $message
        ]);
        return $this->fetch("module:mybasicmodule/views/templates/admin/configuration.tpl");*/

        $output = "";
        if(Tools::isSubmit('submit' . $this->name))
        {
            $courserating = Tools::getValue('courserating');
            if ($courserating && !empty($courserating) && Validate::isGenericName($courserating) )
            {
                Configuration::updateValue('COURSE_RATING', Tools::getValue("courserating"));
                $output .= $this->displayConfirmation($this->trans('Form submitted sucessfully'));
            }
            else
            {
                $output .= $this->displayError($this->trans('Form has not been submitted sucessfully'));
            }
        }

        return $output . $this->displayForm();
    }
    public function displayForm()
    {
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');

        // form inputs
        $fields[0]['form'] = [
            'legend' => [
                'title' => $this->trans('Rating setting')
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Course rating'),
                    'name' => 'courserating',
                    'size' => 20,
                    'required' => true
                ]
                ],
                'submit' => [
                    'title' => $this->trans('Save the rating'),
                    'class' => 'btn btn-primary pull-right'
                ]
        ];

        // Instance of the form helper
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name .'&save' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            ]
        ];

        $helper->fields_value['courserating'] = Configuration::get('COURSE_RATING');
        return $helper->generateForm($fields);
    }

    //hookModuleRoutes
    public function hookModuleRoutes($params)
    {
        return [
            'test' => [
                'controller' => 'test',
                'rule' => 'fc-test',
                'keywords' => [],
                'params' => [
                    'module' => $this->name,
                    'fc' => 'module',
                    'controller' => 'test'
                ]
            ]
        ];
    }
}

?>