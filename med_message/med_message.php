<?php

/**
 * 2007-2023 PrestaShop
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
 *  @copyright 2007-2023 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;


class Med_Message extends Module
{

    public function __construct()
    {
        $this->name = 'med_message';
        $this->tab = 'administration';
        $this->version = '1.0';
        $this->author = 'M.Hadjadji - BioPoolTech';
        $this->need_instance = 0;

        $this->bootstrap = true;

        parent::__construct();


        $this->displayName = $this->trans('Med_message', array(), 'Modules.Message.Admin');
        $this->description = $this->trans('Send messages from the store to the customers email.', array(), 'Modules.Message.Admin');
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        require _PS_MODULE_DIR_ . 'med_message/sql/install.php';
        return parent::install()
            && $this->installTab();
               $this->registerHook('backOfficeHeader') &&
               $this->registerHook('actionAdminControllerSetMedia') &&
               $this->registerHook('displayAdminCustomers') &&
               $this->registerHook('displayAdminOrder') &&
               $this->registerHook('addWebserviceResources');
               $this->registerHook('AdminStatsModules');
               $this->registerHook('displayAdminCustomersForm');
               $this->registerHook('displayAdminCustomersGroupsForm');
    }

    public function uninstall()
    {
        require _PS_MODULE_DIR_ . 'med_message/sql/uninstall.php';
        return parent::uninstall()
            && $this->uninstallTab();
    }

    public function enable($force_all = false)
    {
        return parent::enable($force_all)
            && $this->installTab();
    }

    public function disable($force_all = false)
    {
        return parent::disable($force_all)
            && $this->uninstallTab();
    }

    private function installTab()
    {
        $tabId = (int) Tab::getIdFromClassName('MedMessageDemoController');
        if (!$tabId) {
            $tabId = null;
        }

        $tab = new Tab($tabId);
        $tab->active = 1;
        $tab->class_name = 'MedMessageDemoController';
        // Only since 1.7.7, you can define a route name
        $tab->route_name = 'Message-list';
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = $this->trans('Messagerie Demo', array(), 'Modules.MedMessage.Admin', $lang['locale']);
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentCustomer');
        $tab->module = $this->name;

        return $tab->save();
    }

    private function uninstallTab()
    {
        $tabId = (int) Tab::getIdFromClassName('MedMessageDemoController');
        if (!$tabId) {
            return true;
        }

        $tab = new Tab($tabId);

        return $tab->delete();
    }
}
