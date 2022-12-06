<?php

namespace Atabasch\Controllers;

class SearchController extends \Atabasch\BaseController
{


    public function index(){
        $s = $this->post('s', null);
        if($_SERVER["REQUEST_METHOD"] == 'POST' && $s){
            $this->getPosts($s);
        }else{
         $this->json([]);
        }
    }


    private function getPosts($searchContent){
        $offset = $_GET["offset"] ?? 0;
        $limit  = $_GET["limit"] ?? 10;

        $sql = "SELECT id, title, views, IF(summary != '', summary, description) AS description FROM articles
                WHERE status='published' AND 
                      (title LIKE ? OR description  LIKE ? OR summary LIKE ?) 
                 ORDER BY views DESC 
                 LIMIT $offset, $limit";
        $sc = "%{$searchContent}%";
        $datas = $this->db()->queryAll($sql, [$sc, $sc, $sc]);
        $this->json($datas);
    }

}
?>
