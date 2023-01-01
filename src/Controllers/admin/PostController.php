<?php
namespace Atabasch\Controllers\Admin;

use Atabasch\Models\Post;
use Cocur\Slugify\Slugify;

class PostController extends \Atabasch\Controllers\AdminController{



    public function index($id=null){
        if(!$id){
            return $this->getAll();
        }else{
            return $this->getOne($id);
        }
    }

    private function getAll(){
        $sql = $this->getSql();
        $posts = $this->db()->queryAll($sql);
        $data = [
            'posts' => $posts,
            'total' => $this->db()->getTotalOfTable('articles')
        ];
        $this->json($data);
    }

    private function getOne($id){
        $sql = $this->getSql('WHERE p.id=?', true);
        $post = $this->db()->queryOne($sql, [$id]);
        if(!$post){
            return $this->json(['notfound'=>true]);
        }
        $items = $this->db()->queryAll('SELECT * FROM lists WHERE parent=? ORDER BY `order` ASC', [$post->id]);
        $categories = $this->db()->queryAll('SELECT c.id, c.title, c.slug FROM blog_categories AS c INNER JOIN conn_art_cat con ON con.blog_category_id=c.id WHERE con.article_id=?', [$post->id]);
        $post->items = $items ?? [];
        $post->categories = $categories ?? [];
        $this->json($post);
    }

    private function getSql($more=null, $single=false){
        $offset = $_GET['offset'] ?? 0;
        $limit  = $_GET['limit'] ?? 25;
        $orderby = 'p.'.($_GET['order'] ?? 'id');
        $sort = $_GET['sort'] ?? 'DESC';
        
        $csql = null;
        if(!$single){
            $csql = ",(SELECT JSON_ARRAYAGG(JSON_OBJECT('id', c.id, 'title', c.title, 'slug', c.slug)) FROM blog_categories AS c INNER JOIN conn_art_cat con ON con.blog_category_id=c.id WHERE con.article_id=p.id) AS categories";
        }

        $sql = "SELECT p.*, 
        u.name AS user_name, u.fullname AS user_fullname, u.cover AS user_cover, u.slug AS user_slug
        {$csql} 
        FROM articles AS p 
        INNER JOIN users AS u ON u.id=p.author
        {$more}
        ORDER BY {$orderby} {$sort} 
        LIMIT {$offset}, {$limit} 
        ";
        return $sql;
    }





    // YENİ MAKALE OLUŞTUR.
    public function create(){
        if($this->hasRequestMethod('POST')){
            $postDatas = $this->post("post");
            $postDatas['author'] = $_SESSION['account']->id ?? 1;
            $postModel = new Post();
            $post = $postModel->create($postDatas);
            if($post['status']){
                return $this->json(['id' => $post['id']]);
            }
        }
        return $this->renderError(['errors' => $post['errors'] ?? null]);
    }

    //MAKALE GÜNCELLE
    public function update($id=null){
        if(!$this->hasRequestMethod('POST') || !$id){
            return $this->renderError();
        }

        $postDatas = $this->post("post");
        $postDatas['author'] = $_SESSION['account']->id ?? 1;
        $postModel = new Post();
        $update = $postModel->update($postDatas, $id);
        $this->json($update);
    }

    //MAKELEYİ SİL
    public function delete($id=null){
        if($this->hasRequestMethod('DELETE') && $id){
            $postModel = new Post();
            $delete = $postModel->delete($id);
            if($delete){
                return $this->json($delete);
            }
        }
        return $this->renderError();
    }

    //MAKALE YAYIMLAMA DURUMUNU DEĞİŞTİR
    public function status($id=null){
        if($this->hasRequestMethod('POST') && $id){

            $status = $this->post("status");
            if(in_array($status, ['published', 'draft', 'trash'])){
                
                $postModel = new Post();
                $update = $postModel->updateStatus($status, $id);
                if($update){
                    return $this->json(['status' => $status]);
                }

            }

        }
        return $this->renderError();
    } // status



}

?>
