<?php
/**
 * Nextcloud - NextNote
 *
 * @copyright Copyright (c) 2015, Ben Curtis <ownclouddev@nosolutions.com>
 * @copyright Copyright (c) 2017, Sander Brand (brantje@gmail.com)
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\NextNote\Db;

use \OCA\NextNote\Utility\Utils;
use OCP\AppFramework\Db\Entity;
use OCP\IDBConnection;
use OCP\AppFramework\Db\Mapper;

class GroupMapper extends Mapper {
	private $utils;

	public function __construct(IDBConnection $db, Utils $utils) {
		parent::__construct($db, 'nextnote_groups');
		$this->utils = $utils;
	}


	public function find($group_id, $user_id = null, $deleted = false) {
		$params = [];
		$where = [];
		if($group_id){
			$where[] = 'g.id= ?';
			$params[] = $group_id;
		}

		if ($user_id) {
			$params[] = $user_id;
			$where[] = 'g.uid = ?';
		}

		if ($deleted !== false) {
			$params[] = $deleted;
			$where[] = 'g.deleted = ?';
		}
		$where = implode(' AND ', $where);
		if($where){
			$where = 'WHERE '. $where;
		}
		$sql = "SELECT * FROM *PREFIX*nextnote_groups g $where";
		$results = [];
		foreach ($this->execute($sql, $params)->fetchAll() as $item) {
			$results[] = $this->makeEntityFromDBResult($item);
		}

		if(count($results) === 1){
			return reset($results);
		}

		return $results;
	}

   	/**
	 * Creates a group
	 *
	 * @param Group|Entity $note
	 * @return Note|Entity
	 * @internal param $userId
	 */
	public function create(Entity $note) {
		return parent::insert($note);
	}

	/**
	 * Update group
	 *
	 * @param Group|Entity $group
	 * @return Group|Entity
	 */
	public function update(Entity $group) {
		return parent::update($group);
	}

	/**
	 * Delete group
	 *
	 * @param Group|Entity $group
	 * @return Group|Entity
	 */
	public function delete(Entity $group) {
		return parent::delete($group);
	}

	/**
	 * @param $arr
	 * @return Group
	 */
	public function makeEntityFromDBResult($arr) {
		$group = new Group();
		$group->setId($arr['id']);
		$group->setName($arr['name']);
		$group->setParentId($arr['parent_id']);
		$group->setColor($group['color']);
		$group->setDeleted($arr['deleted']);

		return $group;
	}

}
