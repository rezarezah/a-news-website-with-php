<?php

namespace Admin;

use database\Database;

class dashboard
{
    public function index()
    {
        $db = new Database;
        $categoryCount = $db->select('SELECT COUNT(*) FROM categories')->fetch();
        $postCount = $db->select('SELECT COUNT(*) FROM posts')->fetch();
        $commentCount = $db->select('SELECT COUNT(*) FROM comments')->fetch();
        $commentUnseenCount = $db->select('SELECT COUNT(*) FROM comments WHERE status = "unseen"')->fetch();
        $commentApprovedCount = $db->select('SELECT COUNT(*) FROM comments WHERE status = "approved"')->fetch();
        $postsViews = $db->select('SELECT SUM(view) FROM posts')->fetch();
        $userCount = $db->select('SELECT COUNT(*) FROM users WHERE permission = "user"')->fetch();
        $adminCount = $db->select('SELECT COUNT(*) FROM users WHERE permission = "admin"')->fetch();

        $mostViewedPosts = $db->select('SELECT * FROM posts ORDER BY view DESC limit 0,5')->fetchAll();
        $mostCommentedPosts = $db->select('SELECT posts.id, posts.title, COUNT(comments.post_id) AS comment_count FROM posts LEFT JOIN comments ON posts.id = comments.post_id GROUP BY posts.id ORDER BY comment_count DESC LIMIT 0,5')->fetchAll();
        $lastComments = $db->select('SELECT comments.id, comments.comment, comments.status, users.username FROM comments, users WHERE comments.user_id = users.id ORDER BY comments.created_at DESC LIMIT 0,5')->fetchAll();

        require_once BASE_PATH . '/template/admin/index.php';
    }
}
