<?php

/* This file is part of NextDom.
 *
 * NextDom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * (at your option) any later version.
 *
 * NextDom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NextDom. If not, see <http://www.gnu.org/licenses/>.
 */

namespace NextDom\Models\DAO;

use NextDom\Interfaces\DAOInterface;

abstract class DAO implements DAOInterface
{

    /**
     * Database connection
     *
     * @var db
     */
    protected $db;

    /**
     * Constructor
     *
     * @param db
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Grants access to the database connection object
     * @return db
     */
    protected function getDb()
    {
        return $this->db;
    }

    /**
     * 
     * @param array $array
     * @return array
     */
    protected function buildListDomainObject(array $array): array
    {
        $list = [];
        foreach ($array as $row) {
            $list[] = $this->buildDomainObject($row);
        }
        return $list;
    }

}
