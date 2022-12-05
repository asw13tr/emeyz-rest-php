<?php
namespace Atabasch\Models;

use Atabasch\Validator;

class Post extends \Atabasch\Model {

    protected $table = "articles";
    protected $fields = [
                    'title',
                    'slug',
                    'keywords',
                    'description',
                    'summary',
                    'author',
                    'status',
                    'content',
                    'views',
                    'cover',
                    'video',
                    'hide_cover',
                    'allow_comments',
                    'list_order',
                    'p_time',
                    ];

    private $rules = [
        'title'         => [
            'required'  => [true, 'Başlık boş bırakılamaz'],
            'type'      => ['string', 'Başlık alanı metinsel bir değer olmalı'],
            'minLength' => [16, 'Başlık en az %number karakterden oluşmalı'],
            'maxLength' => [70, 'Başlık en fazla %number karakter olabilir']
        ],
        'description'   => [
            'required'  => [true, 'Açıklama boş bırakılamaz'],
            'minLength' => [16, 'Açıklama en az %number karakterden oluşmalı'],
            'maxLength' => [160, 'Açıklama en fazla %number karakter olabilir']
        ],
        'author'        => [
            'required'  => [true, 'İçerik için bir yazar gerekli'],
            'type'      => ['int', 'Yazar alanı integer bir değer almalı'],
        ],
        'status'        => [
            'required'  => [true, 'İçerik yayımlama durumu gerekli.'],
            'enum'      => [['published', 'draft', 'trash']]
        ],
        'content'       => [
            'required'  => [true, 'İçerik alanı boş bırakılamaz'],
            'minLength' => [25, 'Lütfen en az %number karakterli bir içerik yazısı girin.']
        ],
        'views'         => [
            'type'      => ['number', 'Görüntülenme alanı sayısal bir değer içermelidir']
        ],
        'hide_cover'    => [
            'enum'      => [['on', 'off'], 'Geçersiz girdi']
        ],
        'allow_comments'=> [
            'enum'      => [['on', 'off'], 'Geçersiz girdi']
        ],
        'list_order'    => [
            'type'      => ['int', 'Sıra numarası 0 ile 100 arasında sayısal bir değer olmalıdır.'],
            'min'       => [0, 'Sıra numarası 0 ile 100 arasında sayısal bir değer olmalıdır.'],
            'max'       => [100, 'Sıra numarası 0 ile 100 arasında sayısal bir değer olmalıdır.'],
        ]

    ];

    public function getPosts($isAll=false){
        $where = !$isAll? "WHERE status='published'" : null;
        return $this->queryAll("SELECT * FROM articles {$where} ORDER BY id DESC");
    }
    public function getPost($id, $isAll=false){
        $where = !$isAll? "AND status='published'" : null;
        $sql = "SELECT * FROM articles WHERE id=? {$where}";
        $post = $this->queryOne($sql, [$id]);

        $categoriesSql = "SELECT cat.id,cat.title,cat.slug FROM blog_categories as cat
                          INNER JOIN conn_art_cat as conn
                          ON conn.blog_category_id=cat.id 
                          WHERE conn.article_id=?";
        $categories = $this->queryAll($categoriesSql, [$id]);

        $post->categories = $categories;

        return $post;
    }

//    public function getAllPublished(){
//        $sql = "SELECT * FROM articles WHERE status='published' ORDER BY id DESC";
//    }

    public function create($datas){

            $validator = new Validator($datas, $this->rules);
            echo '<pre>';
            print_r($validator->getResult());
    }



}
