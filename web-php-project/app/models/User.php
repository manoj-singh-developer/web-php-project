<?php 

class User {

	private $_db,
			$_data,
			$_sessionName,
			$_isLoggedIn;

	public function __construct($user = null) {
		$this->_db = DB::getInstance();
		$this->_sessionName = Config::get('session/session_name');
		if (!$user) {
			if (Session::exists($this->_sessionName)) {
				$user = Session::get($this->_sessionName);
				if ($this->find($user)) {
					$this->_isLoggedIn = true;
				}
			}
		} else {
			$this->find($user);
		}
	}

	public function create($fields = array()) {
		if (!$this->_db->insert('usuario', $fields)) {
			throw new Exception ('There was a problem creating a account.');
		}
	}

	public function update($fields = array(), $id = null) {
		if (!$id && $this->isLoggedIn()) {
			$id = $this->data()->usu_username;
		}
		if (!$this->_db->update('usuario', $id, 'usu_username', $fields)) {
			throw new Exception ('There was a problem updating the account.');
		}
	}

	public function delete($username) {
		$user_id = DB::getInstance()->get('usuario', array('usu_username', '=', $username))->first()->usu_id_PK;
		$this->_db->delete('asignado', array('usu_id_PK', '=', $user_id));				
		if (!$this->_db->delete('usuario', array('usu_username', '=', $username))) {
			throw new Exception ('There was a problem deleting the account.');
		}
	}

	public function delete_all_with_category($id) {
		$users = $this->_db->get('usuario', array('cat_id_FK', '=', $id))->results();
		foreach ($users as $key => $value) {
			$this->_db->delete('asignado', array('usu_id_PK', '=', $value->usu_id_PK));				
		}		
		if (!$this->_db->delete('usuario', array('cat_id_FK', '=', $id))) {
			throw new Exception ('There was a problem deleting the account.');
		}	
	}

	public function find($user = null) {
		if ($user) {
			$data = $this->_db->get('usuario', array('usu_username', '=', $user));
			if ($data->count()) {
				$this->_data = $data->first();
				return true;
			}
		}
		return false;
	}

	public function login($username = null, $password = null) {
		$user = $this->find($username);

		if ($user) {
			if ($this->data()->usu_password === $password) {
				Session::put($this->_sessionName, $this->data()->usu_username);
				return true;
			}
		}

		return false;
	}

	public function hasPermission($str) {
		$category = DB::getInstance()->get('categoria',
			array('cat_id_PK', '=', $this->data()->cat_id_FK));
		
		if ($category->count()) {
			$category_title = strtolower($category->first()->cat_titulo);
			switch ($str) {
				case 'normal':
						if ('normal' === $category_title) {
							return true;
						}
						return false;
					break;
				case 'admin':
						if ('admin' === $category_title) {
							return true;
						}
						return false;
					break;
				case 'superuser':
						if ('superuser' === $category_title) {
							return true;
						}
						return false;
					break;
				default:
					return false;
			}
		}
		return false;
	}

	public function logout() {
		Session::delete(($this->_sessionName));
	}

	public function data() {
		return $this->_data;
	}

	public function isLoggedIn() {
		return $this->_isLoggedIn;
	}

}

?>