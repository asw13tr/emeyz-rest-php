<?php

namespace Atabasch\Controllers;

class SearchController extends \Atabasch\BaseController
{


    public function index(){
        if($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['s']) && @strlen($_POST['s']) > 2){
            $this->getPosts($_POST['s']);
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
