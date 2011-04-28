#!/usr/bin/env php
<?php
/*
 * StatusNet - a distributed open-source microblogging tool
 * Copyright (C) 2011, StatusNet, Inc.
 *
 * Script to print out current version of the software
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

define('INSTALLDIR', realpath(dirname(__FILE__) . '/../../..'));

$helptext = <<<END_OF_INSTALLFOREMAIL_HELP
installforemail.php [options] <email address>

END_OF_INSTALLFOREMAIL_HELP;

require_once INSTALLDIR.'/scripts/commandline.inc';

$email = $args[0];

$domain = DomainStatusNetworkPlugin::toDomain($email);

$sn = DomainStatusNetworkPlugin::siteForDomain($domain);

if (empty($sn)) {
    $installer = new DomainStatusNetworkInstaller($domain);

    $installer->verbose = have_option('v', 'verbose');

    // Do the thing
    $installer->main();
    
    $sn = $installer->getStatusNetwork();

    $config = $installer->getConfig();

    Status_network::$wildcard = $config['WILDCARD'];
}

StatusNet::switchSite($sn->nickname);

$confirm = EmailRegistrationPlugin::registerEmail($email);

$confirmUrl = common_local_url('register', array('code' => $confirm->code));

print $confirmUrl."\n";
