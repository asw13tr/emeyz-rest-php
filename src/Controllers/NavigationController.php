<?php

namespace Atabasch\Controllers;

class NavigationController extends \Atabasch\BaseController{


    public function index($idOrSlug=null){
        if(!$idOrSlug){
            $this->menuList();
        }else{
            $this->menuItems($idOrSlug);
        }
    }

    private function menuList(){
        $sql = "SELECT n.*, (SELECT COUNT(*) FROM navigations WHERE parent=n.id) AS total FROM navigations n WHERE type='menu'";
        $result = $this->db()->queryAll($sql);
        $this->json($result);
    }

    private function menuItems($idOrSlug){

        $inItems = explode(',', $idOrSlug);
        $in  = str_repeat('?,', count($inItems) - 1) . '?';
        $result = [];

        $menuSql = $sql = "SELECT n.*, (SELECT COUNT(*) FROM navigations WHERE parent=n.id) AS total FROM navigations n WHERE id IN ({$in}) OR slug IN ({$in}) ORDER BY menu_order ASC";
        $menu = $this->db()->queryAll($menuSql, array_merge($inItems, $inItems));

        if($menu){
            foreach($menu as $menuItem){
                $itemsSql   = "SELECT * FROM navigations WHERE type='item' AND parent=? ORDER BY menu_order ASC";
                $items      = $this->db()->queryAll($itemsSql, [$menuItem->id]);
                $menuItem->data = $items;
                $result[] =  $menuItem;
            }
        }

        return $this->json($result);
    }




}

?>
