<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class html extends CI_Controller{

    private $viewArray = array();
    private $view_file = null;
    
    function __construct(){
        parent::__construct();
        $this->load->library('parser');

        $this->load->library('Adwords');
        $this->load->model('Userlist_io_model');
        $this->load->model('Userlist_vertical_model');
        $this->load->model('Country_model');
        
        $this->view_file = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/' .$this->uri->segment(3);
        
    }
        
    public function create_campaign() {
        
        $this->parser->parse($this->view_file, $this->viewArray);
        
        //die('create_campaign');die;
    }
    
    public function wizard(){

        $this->parser->parse($this->view_file, $this->viewArray);

        $this->load->helper('url');

        $this->viewArray['base_url'] = base_url();

        $this->viewArray['vertical_list'] = $this->Userlist_vertical_model->get_all_users_from_vertical();
        $this->viewArray["domain_list"] = null;
        $this->viewArray["files_uploaded"] = null;
        //$this->viewArray["files_uploaded"] = null;
        $this->parser->parse($this->view_file, $this->viewArray);
    }

    public function get_io(){

        $io = $this->Userlist_io_model->get_all_io();
        foreach($io as $item) {
            $io_array[] = $item['io'];
        }
        print json_encode(array("status" => "SUCCESS", "options" => $io_array));
        exit;
    }

    /*
     *
     * @description This function gets the files came by the Ajax Post request and saves them into the targetDir directory. Calls by ajax.
     * @return ajax response
     * 
     */

    public function get_states_by_country(){
        $country = $this->input->post('country');
        $states = $this->Country_model->get_states_by_country($country);

        if (count($states)) {
            print json_encode(array("status" => "SUCCESS", "states" => $states));
            exit;
        } else {
            print json_encode(array("status" => "ERROR", "message" => "NO states for this country"));
            exit;
        }
    }

    public function creat_campaign(){
        $post = $this->input->post();
        var_dump($post);
            print json_encode(array("status" => "SUCCESS", "states" => 'stat'));
            exit;

    }

    public function uploadFile()
    {
            $targetDir = 'uploads/tmp/';

        $name=$_POST["name"];


            $cleanupTargetDir = true; // Remove old files
            $maxFileAge = 5 * 3600; // Temp file age in seconds
            //
            // Create target dir
            if (!file_exists($targetDir)) {
                @mkdir($targetDir);
                @chmod($targetDir, 0777);
            }

            // Get a file name
//            if (isset($_REQUEST["name"])) {
//                $fileName = $_REQUEST["name"];
//            } elseif (!empty($_FILES)) {
//                $fileName = $_FILES["file"]["name"];
//            } else {
//                $fileName = uniqid("file_");
//            }
            
            $fileNameArray = pathinfo($this->input->post('name'));
            $fileExtension =  $fileNameArray['extension'];
            $fileName = md5(microtime());
            $fileName = $fileName.".".$fileExtension; 
            


            $filePath = $targetDir . DS . $fileName;

            // Chunking might be enabled
            $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
            $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;

            
            // Remove old temp files
            if ($cleanupTargetDir) {
                if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                    die('{"jsonrpc" : "2.0", "error" : {"0p1": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
                }

                while (($file = readdir($dir)) !== false) {
                    $tmpfilePath = $targetDir . DS . $file;

                    // If temp file is current file proceed to the next
                    if ($tmpfilePath == "{$filePath}.part") {
                        continue;
                    }

                    // Remove temp file if it is older than the max age and is not the current file
                    if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
                        @unlink($tmpfilePath);
                    }
                }


                closedir($dir);
            }

            // Open temp file
            if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            }

            if (!empty($_FILES)) {
                if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                    die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
                }

                // Read binary input stream and append it to temp file
                if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
                    die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                }
            } else {
                if (!$in = @fopen("php://input", "rb")) {
                    die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                }
            }

            while ($buff = fread($in, 4096)) {
                fwrite($out, $buff);
            }

            @fclose($out);
            @fclose($in);

            // Check if file has been uploaded
            if (!$chunks || $chunk == $chunks - 1) {
                // Strip the temp .part suffix off
                rename("{$filePath}.part", $filePath);
            }

            @chmod($filePath, 0777);

            if ($this->checkSize($targetDir)) {
                // Return Success JSON-RPC response
//                die('{"jsonrpc" : "2.0", "result" : ""}');
                print ('{"jsonrpc" : "2.0", "title" :' . "'$name'" . ', "status" : "true", "file_dir":"'.$filePath.'"}');
            } else {

                unlink($targetDir . $_FILES["file"]["name"]);
                $errorMessage = "The $name image\'s size doesn\'t correspond to Google Adword\'s required sizes.";

                print ('{"jsonrpc" : "2.0", "title" :' . "'$name'" . ', "status" : "false"}');
            }
    }

    /**
     * @description this function deletes Uploaded Files. Calls by Ajax
     * @return false on error
     */
    public function deleteUploadedFiles()
    {
            if ($this->input->is_ajax_request()) {
                $data = $this->input->post();
                $targetDir = 'uploads/tmp/';
                $fname = $targetDir . $data["fname"];

                if (file_exists($fname)) {
                    unlink($fname);
                } else {
                    echo 'Files deleted!';
                    die();
                }
            }
            return false;
    }

    /*

     *  @description this function checks wheather the image corresponds to the size or not
     *  @param targetDir is the image directory where is stored the image came by the porst
     *  @param size is the id of the size
     *  @returns true if the image's size corresponds to the size, otherwise false
     *
     */

    private function checkSize($targetDir)
    {
        $requiredSize = [[728, 90], [468, 60], [234, 60], [125, 125], [120, 600], [160, 600], [180, 150], [120, 240], [200, 200], [250, 250],
            [300, 250], [336, 280], [300, 600], [300, 1050], [320, 50], [970, 90], [970, 250]];

        $img = $targetDir . $_FILES["file"]["name"];
        $image = new SimpleImage();
        $image->load($img);

        $height = $image->getHeight();
        $width = $image->getWidth();

        if (in_array([$width, $height], $requiredSize)) {
            return true;
        } else {
            return true;
        }
    }

    /*
     * @description This function is used to get refresh token
     */
    
    public function htmlpage(){
        die('dasdasd');
        $this->parser->parse($this->view_file, $this->viewArray);
    }

   

}