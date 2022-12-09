<?php
namespace  Atabasch\Controllers;

class PageController extends \Atabasch\BaseController{




    public function index($idOrSlug=null){
        if(!$idOrSlug){
            $this->getPages();
        }else{
            $this->getPage($idOrSlug);
        }
    }

    private function getPages(){
        $sql = "SELECT id,title,slug,description,cover,views,p_time,hide_cover,allow_comments   FROM pages WHERE status='published'";
        $pages = $this->db()->queryAll($sql);
        $this->json($pages);
    }

    private function getPage($idOrSlug){
        $sql = "SELECT 
                id,title,slug,description,content,cover,video,parent,views,p_time,hide_cover,allow_comments
                FROM pages WHERE status='published' AND (slug=? OR id=?)";
        $page = $this->db()->queryOne($sql, [$idOrSlug, $idOrSlug]);
        $this->json($page);
    }




}
