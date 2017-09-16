<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class dataload extends CI_Controller	{
	public $viewArray = array();
	
    public function __construct()	{
		parent::__construct();

		$dsn = "mysql://root:adamski@localhost/data_consumer";
		$this->load->database($dsn);
    }

	public function load($file = "")	{
		if ($file == "")	{
			print "File required\n";
			exit;
		}

		$fh = fopen("/mnt/logdata-backup/DB_DATA/{$file}", "r");

		$count = 0;
		do {
			$insert = array(
				"email" => $data[0],
				"fname" => $data[1],
				"lname" => $data[2],
				"address" => $data[3],
				"city" => $data[4],
				"state" => $data[5],
				"zip" => $data[6],
				"phone" => $data[7],
				"source" => $data[8],
				"ip_address" => $data[9],
				"gender" => $data[10],
				"date_of_birth" => date("Y-m-d", strtotime($data[11]))
			);

			$this->db->insert("consumer_sm", $insert);
			
			if ($count % 100 == 0) {
			    print $count . "\n";
			}
			$count++;
		} while ($data = fgetcsv($fh, 1000, ",", '"'));

		print mysql_error();
		
	}    

    public function click_cap($io = "") {
        $this->Campclick_model->io = $io;
        $c = $this->Campclick_model->get_current_click_cap();
        
        print_r($c);
    }
    
    public function email_append($file = "", $file2 = "")  {
        if ($file == "" || $file2 == "")	{
            print "File required\n";
            exit;
        }
        
        $fh = fopen("/mnt/logdata-backup/DB_DATA/{$file}", "r");
        $fp = fopen("/mnt/logdata-backup/DB_DATA/{$file2}", "a");

        //FIRST_NAME|MIDDLE_NAME|LAST_NAME|ORGANIZATION_NAME|ADDRESS_LINE_1|ADDRESS_LINE_2|CITY|STATE|ZIP_CODE|KEY_CODE|ID_NUMBER
        //    0           1           2           3               4               5           6      7    8       9           10
        
        $count = 0;
        $cnt=0;
        do {
            $count++;
            
            if ($cnt < 65900) {
                $cnt++;
                continue;
            } else { 
                $cnt++;
            }
            
            $fname = $data[0];
            $mname = $data[1];
            $lname = $data[2];
            $org = $data[3];
            $addr1 = $data[4];
            $addr2 = $data[5];
            $city  = $data[6];
            $state = $data[7];
            $zip = $data[8];
            $keycode = $data[9];
            $id_num = $data[10];
            
            $sql = "SELECT * FROM consumer_sm WHERE fname='{$fname}' AND lname='{$lname}' AND state='{$state}' LIMIT 1";
            //print $sql . "\n";
            $r = $this->db->query($sql);
            
            if (! $r) {
                print "Error Occurred: " . mysql_error() . "\n";
                continue;
            }
            
            if ($r->num_rows() > 0) {
                //print_r($r);
                $rd = $r->row_array();
                //"ID","Firstname","Lastname","Address","CITY","STATE","Zip","Email Address","MatchLevel"
                fwrite($fp, "{$data[0]}\t{$data[1]}\t{$data[2]}\t{$data[3]}\t{$data[4]}\t{$data[5]}\t{$data[6]}\t{$data[7]}\t{$data[8]}\t{$data[9]}\t{$data[10]}\t{$rd['email']}\t1\n");
            }
            
            if ($count % 100 == 0) {
                print $count . "\n";
            }
        } while ($data = fgetcsv($fh, 1000, "|"));
        
        fclose($fh);
        fclose($fp);
    }
    
    public function reverseappend($file = "", $file2 = "") {
        if ($file == "" || $file2 == "")	{
            print "File required\n";
            exit;
        }
        
        $fh = fopen("/mnt/logdata-backup/DB_DATA/{$file}", "r");
        $fp = fopen("/mnt/logdata-backup/DB_DATA/{$file2}", "w");
        
        $count = 0;
        do {
            $email = $data[7];
        
            $r = $this->db->query("SELECT * FROM consumer_sm WHERE email='{$email}' LIMIT 1");
        
            if ($r->num_rows() > 0) {
                print "Found ({$data[0]}) [{$count}]: {$data[7]}\n";
                $rd = $r->row_array();
                //"ID","Firstname","Lastname","Address","CITY","STATE","Zip","Email Address","MatchLevel"
                fwrite($fp, "{$data[0]}\t{$rd['fname']}\t{$rd['lname']}\t{$rd['address']}\t{$rd['city']}\t{$rd['state']}\t{$rd['zip']}\t{$data[7]}\t1\n");
            }

            $count++;
        } while ($data = fgetcsv($fh, 1000, ",", '"'));
        
        fclose($fh);
        fclose($fp);
    }
    
    public function agecheck($file = "", $file2 = "") {
        if ($file == "" || $file2 == "")	{
            print "File required\n";
            exit;
        }
    
        $fh = fopen("/mnt/logdata-backup/DB_DATA/{$file}", "r");
        $fp = fopen("/mnt/logdata-backup/DB_DATA/{$file2}", "w");
    
        $count = 0;
        while (($data = fgets($fh, 4096)) !== false) {
            $fname = trim(substr($data, 33, 14));
            $lname = trim(substr($data, 47, 30));
            $city  = trim(substr($data, 107, 20));
            $state = trim(substr($data, 127, 2));
            
            $r = $this->db->query("SELECT * FROM consumer_sm WHERE fname='{$fname}' AND lname='{$lname}' AND city='{$city}' AND state='{$state}' LIMIT 1");
            
            if ($r->num_rows() > 0) {
                $rd = $r->row_array();
                //"ID","Firstname","Lastname","Address","CITY","STATE","Zip","Email Address","MatchLevel"
                fwrite($fp, $data);
                print_r($rd);
            }
            
            if ($count % 100 == 0) {
                print $count . "\n";
            }
            $count++;
        }
    
        fclose($fh);
        fclose($fp);
    }

    public function ageparse($file = "", $file2 = "") {
        if ($file == "" || $file2 == "")	{
            print "File required\n";
            exit;
        }

        $fh = fopen("/mnt/logdata-backup/DB_DATA/{$file}", "r");
        $fp = fopen("/mnt/logdata-backup/DB_DATA/{$file2}", "w");

        $count = 0;
        while (($data = fgets($fh, 4096)) !== false) {
            $fname = trim(substr($data, 33, 14));
            $lname = trim(substr($data, 47, 30));
            $city  = trim(substr($data, 107, 20));
            $state = trim(substr($data, 127, 2));
            $addr = trim(substr($data, 77, 29));
            $zip = trim(substr($data, 129, 9));
    
            fwrite($fp, "{$fname}\t{$lname}\t{$addr}\t{$city}\t{$state}\t{$zip}\n");
        
            if ($count % 100 == 0) {
                print $count . "\n";
            }
            $count++;
        }

        fclose($fh);
        fclose($fp);
    }
    
    public function email_append2($file = "ERIC-420-MATCH.txt", $file2 = "ERIC-420-MATCH-FINAL.txt")  {
        if ($file == "" || $file2 == "")	{
            print "File required\n";
            exit;
        }
    
        $fh = fopen("/mnt/logdata-backup/DB_DATA/{$file}", "r");
        $fp = fopen("/mnt/logdata-backup/DB_DATA/{$file2}", "w");
    
        $count = 0;
        do {
            $name = addslashes($data[1]);
            
            print $name ."\n";
            
            list($fname, $lname) = explode(" ", $name, 2);
            $city  = $data[4];
            $state = $data[5];
    
            $r = $this->db->query("SELECT * FROM consumer_sm WHERE fname='{$fname}' AND lname='{$lname}' AND city='{$city}' AND state='{$state}' LIMIT 1");
    
            if ($r->num_rows() > 0) {
                $rd = $r->row_array();
                //"ID","Firstname","Lastname","Address","CITY","STATE","Zip","Email Address","MatchLevel"
                fwrite($fp, "{$data[0]}\t{$data[1]}\t{$data[3]}\t{$data[4]}\t{$data[5]}\t{$data[6]}\t{$rd['email']}\t1\n");
            }
    
            if ($count % 100 == 0) {
                print $count . "\n";
            }
            $count++;
        } while ($data = fgetcsv($fh, 1000, "\t", '"'));
    
        fclose($fh);
        fclose($fp);
    }
    
    public function eapp($file = "", $file2 = "")  {
        if ($file == "" || $file2 == "")	{
            print "File required\n";
            exit;
        }
    
        $fh = file_get_contents("/mnt/logdata-backup/DB_DATA/{$file}");
        $fp = fopen("/mnt/logdata-backup/DB_DATA/{$file2}", "w");
        
        $lines = explode("\n", $fh);
        
        $count = 0;
        foreach($lines as $line)    {
            
            $data = explode("\t", $line);
            
            $fname = $data[0];
            $lname = $data[1];
    
            $r = $this->db->query("SELECT * FROM consumer_sm WHERE fname='{$fname}' AND lname='{$lname}' LIMIT 1");
    
            if ($r->num_rows() > 0) {
                $rd = $r->row_array();
                fwrite($fp, "{$data[1]}\t{$data[0]}\t{$rd['email']}\t1\n");
            }
    
            if ($count % 100 == 0) {
                print $count . "\n";
            }
            $count++;
        }
        fclose($fp);
    }
    
}

?>
