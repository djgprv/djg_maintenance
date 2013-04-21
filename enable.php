<?php
/*
 * Wolf CMS - Content Management Simplified. <http://www.wolfcms.org>
 * Copyright (C) 2009-2010 Martijn van der Kleijn <martijn.niji@gmail.com>
 *
 * This file is part of Wolf CMS. Wolf CMS is licensed under the GNU GPLv3 license.
 * Please see license.txt for the full license text.
 */

/* Security measure */
if (!defined('IN_CMS')) { exit(); }

/**
 * This plugin supports a maintenance mode for the Wolf CMS.
 *
 * @package wolf
 * @subpackage plugin.djg_maintenance
 *
 * @author Michał Uchnast <info@kreacjawww.pl>
 *
 
/* exceptions */
$PDO = Record::getConnection();
$driver = strtolower($PDO->getAttribute(Record::ATTR_DRIVER_NAME));
$PDO->exec("ALTER TABLE ".TABLE_PREFIX."page ADD djg_maintenance integer NOT NULL DEFAULT 0");

/* settings */
$settings = array('ver' => '0.0.9',
  'users_array' => '1',
  'status' => 'off',
  'redirect_page' => 'url',
  'url_page' => 'http://www.wolfcms.org/',
  'backdoor_key' => 'WTW039ar',
  'backdoor_key_session' => 'off',
  'global_ip' => '0.0.0.0'
  );

Plugin::setAllSettings($settings, 'djg_maintenance');