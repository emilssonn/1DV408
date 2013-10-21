<?php

namespace common\model;

class DbConnection {

	private $mysqli;

	public function __construct(\mysqli $mysqli) {
		$this->mysqli = $mysqli;
	}

	public function runSql($sql, $paramsArray, $paramsTypeString) {
		$statement = $this->mysqli->prepare($sql);
		if ($statement === FALSE) {
			throw new \Exception("prepare of $sql failed " . $this->mysqli->error);
		}

		$params = array_merge(array($paramsTypeString), $paramsArray);

		if (call_user_func_array(array($statement, "bind_param"), $this->refValues($params)) === FALSE) {
			throw new \Exception("bind_param of $sql failed " . $statement->error);
		}

		if ($statement->execute() === FALSE) {
			throw new \Exception("execute of $sql failed " . $statement->error);
		}

		return $statement;
	}

	/**
	 * Source: http://php.net/manual/en/mysqli-stmt.bind-param.php#96770
	 * @param  array $arr
	 * @return array
	 */
	private function refValues($arr) { 
        $refs = array();

        foreach ($arr as $key => $value) {
            $refs[$key] = &$arr[$key]; 
        }

        return $refs; 
	}
}