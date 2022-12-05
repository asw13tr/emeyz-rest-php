<?php

namespace Atabasch\Controllers;

class RandomController extends \Atabasch\BaseController
{


    public function index(){
        $sql = "SELECT *,
                (SELECT JSON_ARRAYAGG(JSON_OBJECT('id', c.id, 'title', c.title, 'slug', c.slug)) FROM blog_categories c
                               INNER JOIN conn_art_cat cac on c.id = cac.blog_category_id
                                WHERE cac.article_id=a.id AND c.status='published' AND c.hide=false) AS categories 
                FROM articles a WHERE status='published' ORDER BY RAND() LIMIT 1";
        $post = $this->db()->queryOne($sql);
        $this->json($post);
    }

}
?>
