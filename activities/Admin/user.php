<?php

namespace Admin;

use database\Database;

class user extends Admin
{
    public function index()
    {
        $db = new Database();
        $users = $db->select('SELECT * from users ORDER BY `id` DESC');
        require_once BASE_PATH . '/template/admin/users/index.php';
    }
    public function create()
    {
        require_once BASE_PATH . '/template/admin/users/create.php';
    }
    public function store($request)
    {
        $db = new Database();
        $db->insert('categories', array_keys($request), $request);
        $this->redirect('admin/user');
    }
    public function edit($id)
    {
        $db = new Database();
        $user = $db->select('SELECT * from users WHERE id = ?;', [$id])->fetch();
        require_once BASE_PATH . '/template/admin/users/edit.php';
    }
    public function update($request, $id)
    {
        $db = new Database();
        $db->update('users', $id, array_keys($request), $request);
        $this->redirect('admin/user');
    }
    public function delete($id)
    {
        $db = new Database();
        $db->delete('user', $id);
        $this->redirect('admin/user');
    }
    public function permission($id)
    {
        $db = new DataBase();
        $user = $db->select('SELECT * FROM users WHERE id = ?;', [$id])->fetch();
        if (empty($user)) {
            $this->redirectBack();
        }
        if ($user['permission'] == 'user') {
            $db->update('users', $id, ['permission'], ['admin']);
        } else {
            $db->update('users', $id, ['permission'], ['user']);
        }
        $this->redirectBack();
    }
}
