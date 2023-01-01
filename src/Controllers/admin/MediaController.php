<?php 
namespace Atabasch\Controllers\Admin;

use Verot\Upload\Upload;

class MediaController extends \Atabasch\Controllers\AdminController{
    private $uploadPath = __DIR__.'/../../upload';

    private function fileUpload($file, $name=null, $options=[]){
        $handle = new Upload($file);
        if(!$handle->uploaded){
            return ['status' => false, 'message' => $handle->error];
        }

        $handle->allowed            = array('image/*');
        $handle->file_new_name_body =  $name;
        $handle->file_max_size      = (1024*1024)*5;
        
        $this->setOptionsToUploader($handle, $options);        
        $handle->process($this->uploadPath);

        if ($handle->processed){
            $datas = $handle;
            if(($options['pre'] ?? false)){
                $datas->file_name_without_pre = str_replace($options['pre'], '', $datas->file_dst_name);
            }
            return ['status' => true, 'data' => $datas];
        }else{
            return ['status' => false, 'message' => $handle->error];
        }
    }

    private function setOptionsToUploader($handle, $options=[]){
        $handle->image_ratio_crop   = $options['crop'] ?? false;
        $handle->image_resize       = $options['resize'] ?? false;
        $handle->image_x            = $options['width'] ?? 1600;
        $handle->image_y            = $options['height'] ?? 900;
        $handle->image_ratio_y      = !$options['cover']? true : false;
        $handle->file_name_body_pre = $options['pre'] ?? '';
        $handle->png_compression    = $options['compress'] ?? 3;
        $handle->jpeg_quality       = $options['quality'] ?? 85;
        return $handle;
    }


    public function index(){
        $offset = $this->get("offset", 0);
        $limit  = $this->get("limit",   20);
        $sql    = "SELECT * FROM media ORDER BY id DESC LIMIT {$offset}, {$limit}";
        $files   = $this->db()->queryAll($sql);
        $this->json($files);
    }


    public function upload(){
        if(!$this->hasRequestMethod("post")){
            return $this->json([]);
        }

        $selectedFile = $_FILES['file'];
        $name = time().'_'.rand(99999,999999);
        $upload = $this->fileUpload($selectedFile, $name, [
            'crop'      => true,
            'resize'    => true,
            'width'     => 1170,
            'height'    => 702,
            'cover'     => false,
            'pre'       => 'lg_',
        ]);

        if(!$upload['status']){ 
            return $this->json(['status' => false]);
        }else{
          $upladedFilePath = $this->uploadPath.'/'.$upload['data']->file_dst_name;
          $this->fileUpload($selectedFile, $name, ['crop' => true, 'resize' => true, 'width' => 850, 'height' => 510, 'cover' => true, 'pre' => 'md_']);
          $this->fileUpload($selectedFile, $name, ['crop' => true, 'resize' => true, 'width' => 600, 'height' => 450, 'cover' => true, 'pre' => 'sm_']);


            $data = [
                'title' => $name,
                'alt'   => $name,
                'src'   => $upload['data']->file_name_without_pre,
                'type'  => $upload['data']->file_src_mime,
                'info'  => [
                    'width'     => $upload['data']->image_dst_x,
                    'height'    => $upload['data']->image_dst_y,
                    'mime'      => $upload['data']->file_src_mime,
                    'ext'       => $upload['data']->file_dst_name_ext,
                    'size'      => $upload['data']->file_src_size
                ],
                'author'        => $_SESSION['account']->id ?? 1,
                'app'           => 'media'
            ];
            return $this->addToDb($data);

        }

    }

        private function addToDb($data){
            $sql = "INSERT INTO media(title, alt, src, `type`, info, author, app) VALUES(?,?,?,?,?,?,?)";
            $datas = [
                $data['title'],
                $data['alt'],
                $data['src'],
                $data['type'],
                json_encode($data['info']),
                $data['author'],
                $data['app']
            ];
            $insert = $this->db()->insert($sql, $datas);
            if(!$insert){
                return $this->json(['status' => false, 'message' => ""]);
            }else{

                $file = $this->db()->queryOne("SELECT * FROM media WHERE id=? LIMIT 1", [$insert]);
                return $this->json(['status' => true, 'file' => $file]);
            }
        }
        

        

}
?>