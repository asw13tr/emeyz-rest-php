<?php

namespace Atabasch\Controllers;

class CategoryController extends \Atabasch\BaseController
{


    public function index($id=null, $param3=null){
        if(!$id){
            $this->getAll();
        }else{
            $this->getOne($id, $param3);
        }
    }

    private function getAll(){
        $sql = "
        SELECT c.id, 
        c.title, 
        c.slug, 
        c.description, 
        c.parent,
        c.cover,
        (SELECT count(*) FROM conn_art_cat WHERE blog_category_id=c.id) as total 
        FROM blog_categories as c
        WHERE c.status='published' AND parent=0 AND hide=false
        ORDER BY total DESC";
        $categories = $this->db()->queryAll($sql);
        $this->json($categories);
    }


    private function getOne($id, $param3=null){
        $sql = "
        SELECT c.id, 
        c.title, 
        c.slug, 
        c.description, 
        c.parent,
        (SELECT count(*) FROM conn_art_cat WHERE blog_category_id=c.id) as total 
        FROM blog_categories as c
        WHERE c.status='published' AND c.id=? 
        ORDER BY total DESC";
        $category = $this->db()->queryOne($sql, [$id]);

        $offset = $_GET["offset"] ?? 0;
        $limit = $_GET["limit"] ?? 10;
        $orderBy    = $_GET["orderby"] ?? "id";
        $sort       = $_GET["sort"] ?? "DESC";

        if($param3=='posts'){
            $sqlForPosts = "SELECT 
            p.id, p.title, p.slug, p.description, p.summary, p.views, p.cover    
            FROM articles AS p 
            INNER JOIN conn_art_cat AS c ON c.article_id=p.id 
            WHERE c.blog_category_id=? AND p.status='published'  
            ORDER BY p.{$orderBy} {$sort} 
            LIMIT {$offset}, {$limit} ";
            $posts = $this->db()->queryAll($sqlForPosts, [$id]);
        }

        $this->json([
            'category'  => $category,
            'posts'     => $posts
        ]);
    }


}
