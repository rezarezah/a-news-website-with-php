<?php

namespace Admin;

use database\Database;

class setting extends Admin
{

    public function index()
    {
        $db = new DataBase();
        $setting = $db->select('SELECT * FROM setting ORDER BY `id` DESC')->fetch();
        require_once(BASE_PATH . '/template/admin/settings/index.php');
    }


    public function edit()
    {
        $db = new DataBase();
        $setting = $db->select('SELECT * FROM setting')->fetch();
        require_once(BASE_PATH . '/template/admin/settings/edit.php');
    }

    public function update($request)
    {
        $db = new DataBase();
        $setting = $db->select('SELECT * FROM setting')->fetch();
        if ($request['logo']['tmp_name'] != '') {
            $request['logo'] = $this->saveImage($request['logo'], 'setting', 'logo');
        } else {
            unset($request['logo']);
        }
        if ($request['icon']['tmp_name'] != '') {
            $request['icon'] = $this->saveImage($request['icon'], 'setting', 'icon');
        } else {
            unset($request['icon']);
        }
        if (!empty($setting)) {
            $db->update('setting', $setting['id'], array_keys($request), $request);
        } else {
            $db->insert('setting', array_keys($request), $request);
        }
        $this->redirect('admin/setting');
    }
}
