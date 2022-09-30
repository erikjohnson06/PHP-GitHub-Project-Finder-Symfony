<?php

namespace App\Classes;
use App\Classes\GitHubRepositoryRecordJS;

/**
 * Detail class for GitHub project records
 */
class GitHubRepositoryRecordDetailJS extends GitHubRepositoryRecordJS
{
    
    /**
     * @var string
     */
    public $html_url;
    
    /**
     * @var string
     */
    public $description;
    
    /**
     * @var string
     */
    public $created_at;
    
    /**
     * @var string
     */
    public $pushed_at;
}
