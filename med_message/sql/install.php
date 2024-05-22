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
$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'message_comment (
    id_message_comment INT AUTO_INCREMENT NOT NULL,
    id_employe INT(11) NULL,
    id_customer INT(11) NOT NULL,
    title VARCHAR(64) NOT NULL,
    message LONGTEXT NOT NULL,
    id_thread INT(11),
    file VARCHAR(64) NULL,
    date DATETIME,
    id_state_comment INT(11),
    id_shop INT(11),
    PRIMARY KEY  (id_message_comment)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'shop_key (
    id_shop_key INT AUTO_INCREMENT NOT NULL,
    key VARCHAR(64) NOT NULL,
    PRIMARY KEY (id_shop_key)
    )ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'state_comment (
    id_state_comment INT AUTO_INCREMENT NOT NULL,
    description VARCHAR(64) NOT NULL,
    PRIMARY KEY (id_state_comment)
    )ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'INSERT INTO ' . _DB_PREFIX_ . 'state_comment (description) VALUES ("Envoyé"), ("Reçu")';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
