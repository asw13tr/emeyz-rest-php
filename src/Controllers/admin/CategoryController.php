<?php 
namespace Atabasch\Controllers\Admin;

class CategoryController extends \Atabasch\Controllers\AdminController{

    public function index(){
        $sql = "SELECT * FROM blog_categories ORDER BY title ASC";
        $categories = $this->db()->queryAll($sql);
        $this->json($categories);
    }


}

?>