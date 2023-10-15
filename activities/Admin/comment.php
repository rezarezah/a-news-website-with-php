<?php

namespace Admin;

use database\Database;

class comment extends Admin
{
    public function index()
    {
        $db = new Database();
        $comments = $db->select('SELECT comments.*, posts.title AS post_title, users.email AS email FROM comments LEFT JOIN posts ON comments.post_id = posts.id LEFT JOIN users ON comments.user_id = users.id ORDER BY `id` DESC');
        require_once BASE_PATH . '/template/admin/comments/index.php';
    }
    public function show($id)
    {
        $db = new Database();

        $sql = "WITH CommentsWithDetails AS (
        SELECT comments.*, posts.title AS post_title, users.email AS email
        FROM comments
        LEFT JOIN posts ON comments.post_id = posts.id
        LEFT JOIN users ON comments.user_id = users.id)
        SELECT * FROM CommentsWithDetails
        WHERE id = ?
        ORDER BY id DESC
";

        $comment = $db->select($sql, [$id])->fetch();

        // $comment = $db->select('SELECT * from comments WHERE id = ?;', [$id])->fetch();
        // $comments = $db->select('SELECT comments.*, posts.title AS post_title, users.email AS email FROM comments LEFT JOIN posts ON comments.post_id = posts.id LEFT JOIN users ON comments.user_id = users.id ORDER BY `id` DESC');

        $unseenComments = $db->select('SELECT * FROM comments WHERE status = ?', ['unseen']);
        foreach ($unseenComments as $comment) {
            $db->update('comments', $comment['id'], ['status'], ['seen']);
        }
        require_once BASE_PATH . '/template/admin/comments/show.php';
    }

    public function delete($id)
    {
        $db = new Database();
        $db->delete('comments', $id);
        $this->redirect('admin/delete');
    }
    public function changeStatus($id)
    {
        $db = new DataBase();
        $comment = $db->select('SELECT * FROM comments WHERE id = ?;', [$id])->fetch();
        if (empty($comment)) {
            $this->redirectBack();
        }
        if ($comment['status'] == 'seen') {
            $db->update('comments', $id, ['status'], ['approved']);
        } else {
            $db->update('comments', $id, ['status'], ['seen']);
        }
        $this->redirectBack();
    }
}