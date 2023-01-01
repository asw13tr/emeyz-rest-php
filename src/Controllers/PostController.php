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


    public function index($idOrSlug=null){
        if(!$idOrSlug){
            $this->getAll();
        }else{
            $this->getOne($idOrSlug);
        }
    }

    private function getAll(){
        $offset     = $_GET["offset"] ?? 0;
        $limit      = $_GET["limit"] ?? 10;
        $orderBy    = $_GET["orderby"] ?? "id";
        $sort       = $_GET["sort"] ?? "DESC";

        $sql        = "SELECT a.id, a.title, a.slug, a.keywords, a.description, a.summary, a.content, a.views, a.cover, a.video, a.p_time, a.hide_cover,
                           (SELECT JSON_ARRAYAGG(JSON_OBJECT('id', c.id, 'title', c.title, 'slug', c.slug)) FROM blog_categories c
                               INNER JOIN conn_art_cat cac on c.id = cac.blog_category_id
                                WHERE cac.article_id=a.id AND c.status='published' AND c.hide=false) AS categories
                        FROM articles a
                        WHERE a.status='published'
                        ORDER BY a.{$orderBy} {$sort}
                        LIMIT {$offset}, {$limit}";

        $datas      = $this->db()->queryAll($sql);
        $this->json($datas);
    }



    private function getOne($idOrSlug=null){
        $post = $this->postModel->getPost($idOrSlug);
        if(!$post){
            $this->notFound();
        }else{
            $this->json($post);
        }
    }


    public function favorites(){
        if($this->hasRequestMethod("POST")){
           $favorites   = $this->post("favorites", false);
           $offset      = $this->get("offset", 0);
           $limit       = $this->get("limit", 15);

           $favorites   = explode(',', $favorites);
           $inChars     = str_repeat('?,', count($favorites) - 1) . '?';

           if($favorites){
               $sql = "SELECT a.id, a.title, a.keywords, a.description, a.summary, a.content, a.views, a.cover, a.video, a.p_time,
                           (SELECT JSON_ARRAYAGG(JSON_OBJECT('id', c.id, 'title', c.title, 'slug', c.slug)) FROM blog_categories c
                               INNER JOIN conn_art_cat cac on c.id = cac.blog_category_id
                                WHERE cac.article_id=a.id AND c.status='published' AND c.hide=false) AS categories
                        FROM articles a
                        WHERE a.status='published' AND a.id IN ({$inChars})
                        ORDER BY a.id DESC 
                        LIMIT {$offset},{$limit}";
               $posts = $this->db()->queryAll($sql, $favorites);
           }
        }
        return $this->json( $posts ?? [] );
    }



    // gGörüntülenme arttır
    public function views(){
        $id     = $this->post("id", null);
        $value  = $this->post('value', null);
        if($this->hasRequestMethod("POST")){
            if($id && $value){
                $sql = "UPDATE articles SET views=views+? WHERE id=?";
                $this->db()->execute($sql, [$value, $id]);
            }
        }
        return $this->json([ 'id'=>$id, 'value'=>$value ]);
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
