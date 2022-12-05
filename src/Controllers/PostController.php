<?php
namespace Atabasch\Controllers;

use Atabasch\Models\Post;
use Cocur\Slugify\Slugify;

class PostController extends \Atabasch\BaseController {

    public $postModel = null;

    public function __construct(){
        parent::__construct();
        $this->postModel = new Post();
    }


    public function index($id=null){
        if(!$id){
            $this->getAll();
        }else{
            $this->getOne($id);
        }
    }

    private function getAll(){
        $offset     = $_GET["offset"] ?? 0;
        $limit      = $_GET["limit"] ?? 10;

        $sql        = "SELECT a.id, a.title, a.keywords, a.description, a.summary, a.content, a.views, a.cover, a.video, a.p_time,
                           (SELECT JSON_ARRAYAGG(JSON_OBJECT('id', c.id, 'title', c.title, 'slug', c.slug)) FROM blog_categories c
                               INNER JOIN conn_art_cat cac on c.id = cac.blog_category_id
                                WHERE cac.article_id=a.id AND c.status='published' AND c.hide=false) AS categories
                        FROM articles a
                        WHERE a.status='published'
                        ORDER BY a.id DESC
                        LIMIT {$offset}, {$limit}";

        $datas      = $this->db()->queryAll($sql);
        $this->json($datas);
    }

    private function getOne($id=null){
        $isAll = $_GET['all'] ?? false;
        if(!$id){
            $this->json($this->postModel->getPosts($isAll));
            // todo: gelen veriyi kontrol edip boş verri için çıktı üret
        }else{
            $post = $this->postModel->getPost($id, $isAll);
            if(!$post){
                $this->notFound();
            }else{
                $this->json($post);
            }
        }
    }



    public function create(){
//        $slugify = new Slugify();
//        $data = [
//            'title'         => $this->post('title', null),
//            'slug'          => $slugify->slugify($this->post('title')),
//            'keywords'      => $this->post('keywrods', null),
//            'description'   => $this->post('description', null),
//            'summary'       => $this->post('summary', null),
//            'author'        => $this->post('author', 0),
//            'status'        => $this->post('status', null),
//            'content'       => $this->post('content', null),
//            'cover'         => $this->post('cover', null),
//            'video'         => $this->post('video', null),
//            'hide_cover'    => $this->post('hide_cover', null),
//            'allow_comments'=> $this->post('allow_comments', null),
//            'list_order'    => $this->post('list_order', null),
//            'p_time'        => $this->post('p_time', null),
//
//        ];
//        $this->json([ "title" => "Deneme" ]);
            $data = [
               'title'          => 'Bu bir deneme başlığıdır',
               'description'    => 'bu deneme için bir açıklamadır',
               'author'         => 1,
                'status'        => 'published'
            ] ;
            $this->postModel->create($data);
    }

    public function update($id, $slug){
        echo "Bu $id numaralı yazının güncellemesi => " . $slug;;
    }

    public function delete($id){

    }


}
