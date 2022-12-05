<?php
namespace Atabasch;

class Database
{

    protected $db = null;

    public function __construct(){
        try {
            $this->db = new \PDO("mysql:host={$_ENV['DBHOST']};dbname={$_ENV['DBNAME']};charset={$_ENV['DBCHAR']}", $_ENV['DBUSER'], $_ENV['DBPASS']);
        }catch (\PDOException $error){
            print_r($error->getMessage());
        }

    }


    public function connection(){
        return $this->db;
    }

    public function queryAll($sql, $datas=[]){
        $query = $this->db->prepare($sql);
        $query->execute($datas);
        return $query->fetchAll(\PDO::FETCH_OBJ);
    }

    public function queryOne($sql, $datas=[]){
        $query = $this->db->prepare($sql);
        $query->execute($datas);
        return $query->fetch(\PDO::FETCH_OBJ);
    }

    public function insert($sql, $datas=[]){
        $query = $this->db->prepare($sql);
        $query->execute($datas);
        return $this->db->lastInsertId();
    }

    public function update($sql, $datas=[]){
        $query = $this->db->prepare($sql);
        $query->execute($datas);
        return $query->rowCount();
    }

    public function delete($sql, $datas=[]){
        $query = $this->db->prepare($sql);
        $query->execute($datas);
        return $query->rowCount();
    }




}
