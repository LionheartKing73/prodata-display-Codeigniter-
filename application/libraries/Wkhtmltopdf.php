<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH."third_party/Wkhtmltopdf/Wkhtmltopdf.class.php";

class CI_Wkhtmltopdf extends Wkhtmltopdf {

    // Codeigniter instance
    protected $CI;
    private $title;
    private $content;
    private $url;
    private $path;
    private $mode;
    private $mode_array = [
        'MODE_DOWNLOAD'=>'D',
        'MODE_STRING'=>'S',
        'MODE_EMBEDDED'=>'I',
        'MODE_SAVE'=>'F'
    ];

    //const MODE_DOWNLOAD = 'D';       // Force the client to download PDF file
    //const MODE_STRING = 'S';         // Returns the PDF file as a string
    //const MODE_EMBEDDED = 'I';       // When possible, force the client to embed PDF file
    //const MODE_SAVE = 'F';           // PDF file is saved on the server. The path+filename is returned.

    public function __construct()
    {
        parent::__construct();

        // Store the Codeigniter super global instance... whatever
        $this->CI = get_instance();

    }

    public function downloadPDF()	{
    	try {
			$wkhtmltopdf = new wkhtmltopdf();
			$wkhtmltopdf->setTitle($this->title);
			
			// decide where this is coming from URL or direct content load
			if ($this->url == "")	{
				$wkhtmltopdf->setHtml($this->content);
			} else {
				$wkhtmltopdf->setHttpUrl($this->url);
			}
			
			$wkhtmltopdf->output($this->mode_array[$this->mode], $this->path);
		} catch (Exception $e) {

			return ['status'=>false, 'message'=>$e->getMessage()]; exit;
		}
		return ['status'=>true]; exit;
    }
    
    public function __get($name)	{
    	return $this->$name;
    }
    
    public function __set($name, $value)	{
    	$this->$name = $value;
    }
    
}
