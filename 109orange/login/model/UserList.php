<?php

namespace login\model;


require_once("UserCredentials.php");
require_once("common/model/PHPFileStorage.php");

/**
 * represents All users in the system
 *
 */
class UserList {
	/**
	 * Temporary solution with only one user "Admin" in PHPFileStorage
 	 * You might want to use a database instead.
	 * @var \common\model\PHPFileStorage
	 */
	private $phpFileStorage;

	/**
	 * We only have one user in the system right now.
	 * @var array of UserCredentials
	 */
	private $users;


	public function  __construct() {
		$this->phpFileStorage = new \common\model\PHPFileStorage("data/users.php");

		$this->users = array();
		$this->loadAll();
	}

	/**
	 * Do we have this user in this list?
	 * @throws  Exception if user provided is not in list
	 * @param  UserCredentials $fromClient
	 * @return UserCredentials from list
	 */
	public function findUser(UserCredentials $fromClient) {
		foreach($this->users as $user) {
			if ($user->isSame($fromClient) ) {
				\Debug::log("found User");
				return  $user;
			}
		}
		throw new \Exception("could not find user");
	}

	/**
	 * Do we have this user in this list?
	 * @param  UserCredentials $fromClient
	 * @return bool
	 */
	public function userExists(UserCredentials $fromClient) {
		foreach($this->users as $user) {
			if ($user->getUserName() == $fromClient->getUserName() ) {
				return true;
			}
		}
		return false;
	}

	public function update(UserCredentials $changedUser) {
		//this user needs to be saved since temporary password changed
		$this->phpFileStorage->writeItem($changedUser->getUserName(), $changedUser->toString());

		\Debug::log("wrote changed user to file", true, $changedUser);
		$this->users[$changedUser->getUserName()->__toString()] = $changedUser;
	}

	/**
	 * Temporary function to store "Admin" user in file "data/admin.php"
	 * If no file is found a new one is created.
	 * 
	 * @return [type] [description]
	 */
	private function loadAdmin() {
		
		try {
			//Read admin from file
			$adminUserString = $this->phpFileStorage->readItem("Admin");
			$admin = UserCredentials::fromString($adminUserString);

		} catch (\Exception $e) {
			\Debug::log("Could not read file, creating new one", true, $e);

			//Create a new user
			$userName = new UserName("Admin");
			$password = Password::fromCleartext("Password");
			$admin = UserCredentials::create( $userName, $password);
			$this->update($admin);
		}

		$this->users[$admin->getUserName()->__toString()] = $admin;
	}

	/**
	 * Load all users from file
	 * @return array of \login\model\UserCredentials
	 */
	private function loadAll() {
		try {
			$usersStrings = $this->phpFileStorage->readAll();

			foreach ($usersStrings as $username => $value) {
				$user = UserCredentials::fromString($value);
				$this->users[$user->getUserName()->__toString()] = $user;
			}
		} catch (\Exception $e) {
			\Debug::log("Could not read file", true, $e);
		}
		
	}
}