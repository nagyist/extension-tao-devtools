<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @license GPLv2
 * @package taoDevTools
 *
 */

namespace oat\taoDevTools\helper;

use oat\tao\model\TaoOntology;
use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyRdfs;
use oat\taoQtiItem\model\qti\ImportService;
use helpers_TimeOutHelper;
use oat\taoGroups\models\GroupsService;

class DataGenerator
{
    public static function generateItems($count = 100)
    {

        $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoDevTools');

        $generationId = NameGenerator::generateRandomString(4);

        $topClass = new \core_kernel_classes_Class(TaoOntology::CLASS_URI_ITEM);
        $class = $topClass->createSubClass('Generation ' . $generationId);
        $fileClass = new \core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#File');

        $sampleFile = $ext->getDir() . 'data/items/sampleItem.xml';

        helpers_TimeOutHelper::setTimeOutLimit(helpers_TimeOutHelper::LONG);
        for ($i = 0; $i < $count; $i++) {
            $report = ImportService::singleton()->importQTIFile($sampleFile, $class, false);
            $item = $report->getData();
            $item->setLabel(NameGenerator::generateTitle());
        }
        helpers_TimeOutHelper::reset();

        return $class;
    }

    public static function generateGlobalManager($count = 100)
    {
        $topClass = new \core_kernel_classes_Class(TaoOntology::CLASS_URI_TAO_USER);
        $role = new \core_kernel_classes_Resource(TaoOntology::PROPERTY_INSTANCE_ROLE_GLOBALMANAGER);
        $class = self::generateUsers($count, $topClass, $role, 'Backoffice user', 'user');

        return $class;
    }

    public static function generateTesttakers($count = 1000)
    {

        $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoGroups');


        $topClass = new \core_kernel_classes_Class(TaoOntology::CLASS_URI_SUBJECT);
        $role = new \core_kernel_classes_Resource(TaoOntology::PROPERTY_INSTANCE_ROLE_DELIVERY);
        $class = self::generateUsers($count, $topClass, $role, 'Test-Taker ', 'tt');

        $groupClass = new \core_kernel_classes_Class(TaoOntology::CLASS_URI_GROUP);
        $group = $groupClass->createInstanceWithProperties([
            OntologyRdfs::RDFS_LABEL => $class->getLabel()
        ]);

        foreach ($class->getInstances() as $user) {
            GroupsService::singleton()->addUser($user->getUri(), $group);
        }

        return $class;
    }

    protected static function generateUsers($count, $class, $role, $label, $prefix)
    {

        $userExists = \tao_models_classes_UserService::singleton()->loginExists($prefix . '0');
        if ($userExists) {
            throw new \common_exception_Error($label . ' 0 already exists, Generator already run?');
        }

        $generationId = NameGenerator::generateRandomString(4);
        $subClass = $class->createSubClass('Generation ' . $generationId);

        helpers_TimeOutHelper::setTimeOutLimit(helpers_TimeOutHelper::LONG);
        for ($i = 0; $i < $count; $i++) {
            $tt = $subClass->createInstanceWithProperties([
                OntologyRdfs::RDFS_LABEL => $label . ' ' . $i,
                GenerisRdf::PROPERTY_USER_UILG  => 'http://www.tao.lu/Ontologies/TAO.rdf#Langen-US',
                GenerisRdf::PROPERTY_USER_DEFLG => 'http://www.tao.lu/Ontologies/TAO.rdf#Langen-US',
                GenerisRdf::PROPERTY_USER_LOGIN => $prefix . $i,
                GenerisRdf::PROPERTY_USER_PASSWORD => \core_kernel_users_Service::getPasswordHash()->encrypt('pass' . $i),
                GenerisRdf::PROPERTY_USER_ROLES => $role,
                GenerisRdf::PROPERTY_USER_FIRSTNAME => $label . ' ' . $i,
                GenerisRdf::PROPERTY_USER_LASTNAME => 'Family ' . $generationId
            ]);
        }

        helpers_TimeOutHelper::reset();
        return $subClass;
    }
}
