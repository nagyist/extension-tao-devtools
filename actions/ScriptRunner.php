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
 *
 */

namespace oat\taoDevTools\actions;

use tao_actions_CommonModule;
use oat\taoDevTools\helper\LocalesGenerator;
use oat\taoDevTools\helper\DataGenerator;
use oat\tao\model\messaging\Message;
use oat\tao\model\messaging\MessagingService;
use oat\tao\model\search\SearchService;

/**
 * The Main Module of tao development tools
 *
 * @package taoDevTools
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
class ScriptRunner extends tao_actions_CommonModule
{

    public function index()
    {
        $this->setData('actions', [
            'emptyCache' => __('Empty Cache'),
            'reindex' => __('Reindex all resources'),
            'generatePo' => __('Regenerate locales files'),
            'createItems' => __('Create 100 qti Items'),
            'createTests' => __('Create 100 qti Tests'),
            'createTesttakers' => __('Create 1000 test takers'),
            'createGlobalManager' => __('Create 100 global managers'),
            'sendSelfMsg' => __('Send a message to yourself')
        ]);
        $this->setView('ScriptRunner/index.tpl');
    }

    public function emptyCache()
    {
        \common_cache_FileCache::singleton()->purge();
        return $this->returnJson([
            'success' => true,
            'message' => __('Cache has been emptied')
        ]);
    }

    public function compileJs()
    {
        return $this->returnJson([
            'success' => true,
            'message' => __('Javascripts have been compiled')
        ]);
    }

    public function generatePo()
    {
        $generator = new LocalesGenerator();
        $generator->generateAll();
        return $this->returnJson([
            'success' => true,
            'message' => __('Translation files have been regenerated')
        ]);
    }

    public function reindex()
    {
        $indexed = SearchService::runIndexing();
        return $this->returnJson([
            'success' => true,
            'message' => __('Reindexed %s resources', $indexed)
        ]);
    }

    public function createItems()
    {
        $class = DataGenerator::generateItems(100);
        return $this->returnJson([
            'success' => true,
            'message' => __('Items generated in class %s', $class->getLabel())
        ]);
    }

    public function createTests() {
        $class = DataGenerator::generateTests(10);
        return $this->returnJson(array(
            'success' => true,
            'message' => __('Tests generated in class %s', $class->getLabel())
        ));
    }

    public function createTesttakers()
    {
        $class = DataGenerator::generateTesttakers(1000);
        return $this->returnJson([
            'success' => true,
            'message' => __('Testtakers generated in class %s', $class->getLabel())
        ]);
    }

    public function createGlobalManager()
    {
        $class = DataGenerator::generateGlobalManager(100);
        return $this->returnJson([
            'success' => true,
            'message' => __('Global managers generated in class %s', $class->getLabel())
        ]);
    }

    public function sendSelfMsg()
    {
        $message = new Message();
        $message->setTo(\common_session_SessionManager::getSession()->getUser());
        $message->setBody('<h1>Tao Dev Message send on the ' . \tao_helpers_Date::displayeDate(time()) . '</h1>');
        $success = MessagingService::singleton()->send($message);
        return $this->returnJson([
            'success' => $success,
            'message' => __('Send a message')
        ]);
    }
}
