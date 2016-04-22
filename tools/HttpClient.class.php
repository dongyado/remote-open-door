<?php
/**
 * http client . 
 *
 *
 * @author dongyado<dongaydo@gmail.com>
 * */

//----------------------------------------

class HttpClient {
    
    const VERSION = '1.0';


    private $_host = NULL;
    private $_timeout     = 30;

    /**
     * constructor.
     *
     * @param $_host  host of segement server
     *
     * */
   
    public function __construct($_host){
        $this->_host = $_host;
    }

    public function get() {

    } 

    /**
     * post request
     *
     * */
    public function post($mode, $body ) {
        $headers = array( "Content-Type" => "application/x-www-form-urlencoded; charset=utf-8" );
        return $this->_do_request('POST', $mode, $headers, $body); 
    }


    /**
     * request method
     *
     *
     * */

    protected function _do_request(
        $method, 
        $path = NULL, 
        $headers = NULL, 
        $body= NULL, 
        $file_handle= NULL
    ) {

        $url = "http://{$this->_host}/{$path}";
        $ch = curl_init($url);
        $_headers = array('Expect:');
        if (!is_null($headers) && is_array($headers)){
            foreach($headers as $k => $v) {
                array_push($_headers, "{$k}: {$v}");
            }
        }

        $length = 0;
        $date = gmdate('D, d M Y H:i:s \G\M\T');

        if (!is_null($body)) {
            if(is_resource($body)){
                fseek($body, 0, SEEK_END);
                $length = ftell($body);
                fseek($body, 0);

                array_push($_headers, "Content-Length: {$length}");
                curl_setopt($ch, CURLOPT_INFILE, $body);
                curl_setopt($ch, CURLOPT_INFILESIZE, $length);
            } else {
                $body = http_build_query($body);
                $length = @strlen($body);
                array_push($_headers, "Content-Length: {$length}");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            }
        } else {
            array_push($_headers, "Content-Length: {$length}");
        }

        //array_push($_headers, "Authorization: {$this->sign($method, $uri, $date, $length)}");
        array_push($_headers, "Date: {$date}");

        curl_setopt($ch, CURLOPT_HTTPHEADER, $_headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeout);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($method == 'PUT' || $method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);

        } else {
            curl_setopt($ch, CURLOPT_POST, 0);
        }

        if ($method == 'GET' && is_resource($file_handle)) {
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FILE, $file_handle);
        }

        if ($method == 'HEAD') {
            curl_setopt($ch, CURLOPT_NOBODY, true);
        }

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($http_code == 0) 
            return false;

        curl_close($ch);

        $header_string = '';
        $body = '';

        if ($method == 'GET' && is_resource($file_handle)) {
            $header_string = '';
            $body = $response;
        } else {
            list($header_string, $body) = explode("\r\n\r\n", $response, 2);
        }
        
        $data = array(
            'status' => $http_code,
            'header' => $header_string,
            'body'   => $body
        );
        return $data;
    }
}
?>
