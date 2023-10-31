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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoDevTools\models\persistence;

use Doctrine\DBAL\DBALException;
use PDO;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use oat\generis\persistence\PersistenceManager;

class SqlProxyDriver implements \common_persistence_sql_Driver, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    const OPTION_PERSISTENCE = 'persistenceId';

    /** @var QueryCounter */
    private $counter;

    /**
     * @var \common_persistence_sql_Driver
     */
    protected $persistence;

    /**
     * Allow to connect the driver and return the connection
     *
     * @param string $id
     * @param array  $params
     *
     * @return \common_persistence_Persistence
     */
    function connect($id, array $params)
    {
        $this->counter = new QueryCounter($id);

        $this->persistence = $this->getServiceLocator()->get(PersistenceManager::class)->getPersistenceById($params['persistenceId']);

        unset($params['persistenceId']);

        return new \common_persistence_SqlPersistence($params, $this);
    }

    /**
     * Proxy to query
     * @param string $statement
     * @param array $params
     * @param array $types
     *
     * @return mixed
     * @throws DBALException
     */
    public function query($statement, $params, array $types = [])
    {
        $this->counter->count(__FUNCTION__, $statement);
        try {
            return $this->persistence->query($statement, $params, $types);
        } catch (DBALException $e) {
            \common_Logger::w('Failed: ' . $statement);
            throw $e;
        }
    }

    /**
     * Proxy to exec
     * @param string $statement
     * @param array $params
     * @param array $types
     *
     * @return mixed
     * @throws DBALException
     */
    public function exec($statement, $params, array $types = [])
    {
        $this->counter->count(__FUNCTION__, $statement);
        try {
            return $this->persistence->exec($statement, $params, $types);
        } catch (DBALException $e) {
            \common_Logger::w('Failed: ' . $statement);
            throw $e;
        }
    }

    /**
     * Proxy to insert
     * @param string $tableName
     * @param array $data
     * @param array $types
     *
     * @return mixed
     * @throws DBALException
     */
    public function insert($tableName, array $data, array $types = [])
    {
        $this->counter->count(__FUNCTION__, $tableName);
        try {
            return $this->persistence->insert($tableName, $data, $types);
        } catch (DBALException $e) {
            \common_Logger::w('Failed insertion on table : ' . $tableName);
            throw $e;
        }
    }
    
    /**
     * Proxy to insertMultiple
     * @param string $tableName
     * @param array $data
     *
     * @return mixed
     * @throws DBALException
     */
    public function insertMultiple($tableName, array $data)
    {
        $this->counter->count(__FUNCTION__, $tableName);
        try {
            return $this->persistence->insertMultiple($tableName, $data);
        } catch (DBALException $e) {
            \common_Logger::w('Failed insertion on table : ' . $tableName);
            throw $e;
        }
    }

    /**
     * @param string $tableName
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function updateMultiple($tableName, array $data)
    {
        $this->counter->count(__FUNCTION__, $tableName);
        try {
            return $this->persistence->updateMultiple($tableName, $data);
        } catch (DBALException $e) {
            \common_Logger::w('Failed update multiple on table : ' . $tableName);
            throw $e;
        }
    }

    /**
     * Proxy to getSchemaManager
     * @return mixed
     */
    public function getSchemaManager()
    {
        return $this->persistence->getSchemaManager();
    }

    /**
     * Proxy to getPlatForm
     * @return mixed
     */
    public function getPlatForm()
    {
        return new PlatformProxy($this->getDbalConnection(), $this->counter);
    }

    /**
     * Proxy to lastInsertId
     * @param null $name
     *
     * @return mixed
     */
    public function lastInsertId($name = null)
    {
        return $this->persistence->lastInsertId($name);
    }

    /**
     * Proxy to quote
     * @param     $parameter
     * @param int $parameter_type
     *
     * @return mixed
     */
    public function quote($parameter, $parameter_type = PDO::PARAM_STR)
    {
        return $this->persistence->quote($parameter, $parameter_type);
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getDbalConnection()
    {
        return $this->persistence->getDriver()->getDbalConnection();
    }

    public function getCounter(): ?QueryCounter
    {
        return $this->counter;
    }
}
