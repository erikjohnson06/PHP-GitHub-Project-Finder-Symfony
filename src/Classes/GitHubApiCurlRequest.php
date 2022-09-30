<?php

namespace App\Classes;

class GitHubApiCurlRequest 
{
    
    /**
     * Default to searching for PHP projects, sorted by the number of stargazers
     *
     * Notes on the search syntax: 
     * https://docs.github.com/en/enterprise-cloud@latest/search-github/getting-started-with-searching-on-github/understanding-the-search-syntax
     * 
     * Examples of query string: q=language:php stars:500..2000 created:2021-01-01..2021-12-31 
     * --> Search for projects written in PHP with between x to y stars created between date x and date y
     * 
     * @var string
     */
    private string $url = "https://api.github.com/search/repositories?q=language:php&sort=stars&order=desc"; 
    
    /**
     * @var string
     */
    private string $user_agent = "";
    
    /**
     * @var string
     */
    private string $error_msg = "";
    
    /**
     * @var int
     */
    private int $page_number = 1;
    
    /**
     * Max = 100
     * @var int
     */
    private int $per_page = 100;
    
    /**
     * @var CurlHandle
     */
    private $curl;
    
    /**
     * Initiate the request
     */
    public function init_cURL(){
        $this->curl = curl_init();
    }
    
    /**
     * Terminate the connection
     */
    public function close_cURL(){
        curl_close($this->curl);
    }
        
    /**
     * Set the current page (pagination) number of the result set
     * 
     * @param int $page
     */
    public function setPageNumber(int $page = 1){
        
        if (!$page){
            $page = 1;
        }
        
        $this->page_number = $page;
    }
    
    /**
     * Set the number of results per search
     * 
     * @param int $val - GitHub limits to 100 per request.
     */
    public function setPerPage(int $val = 50){
        
        if ($val > 100){
            $val = 100;
        }
        
        $this->per_page = $val;
    }
    
    /**
     * Required - GitHub requires a user agent for the request to be successful
     * 
     * @param string $agent
     */
    public function setUserAgent($agent = ""){
        $this->user_agent = $agent;
    }
    
    /**
     * Return any error messages generated in the request
     * 
     * @return string
     */
    public function getErrorMsg(){
        return $this->error_msg;
    }
    
    /**
     * Submit a search request to the GitHub API
     * 
     * @return array|boolean
     * @throws \Exception
     */
    public function submitGitSearchRequest(){
        
        try {
            
            if (!$this->curl){
                $this->init_cURL();
            }
            
            if (!$this->url){
                throw new \Exception("Invalid or missing URL.");
            }

            if (!$this->user_agent){
                throw new \Exception("Invalid or missing User Agent.");
            }
                        
            //log_message("error", $this->url . "&per_page=" . $this->per_page . "&page=" . $this->page_number);
            
            curl_setopt($this->curl, CURLOPT_URL, $this->url . "&per_page=" . $this->per_page . "&page=" . $this->page_number);
            curl_setopt($this->curl, CURLINFO_HEADER_OUT, true);
            curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->curl, CURLOPT_USERAGENT, $this->user_agent);
            curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = json_decode(curl_exec($this->curl));

            if (curl_errno($this->curl)){
                throw new \Exception("cURL ERROR (" . curl_errno($this->curl) . ") " . curl_error($this->curl));
            }
            
            //Return the item array from the cURL response
            if ($response && isset($response->items) && count($response->items)){
                return $response->items;
            }
        } catch (\Exception $ex) {
            $this->error_msg = "Error submitting cURL Request: " . $ex->getMessage();
            //log_message("error", $this->error_msg);
        }
        
        return false;
    }
}