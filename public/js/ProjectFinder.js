
/****************************
 *                          *
 *  PHP Project Finder JS   *
 *                          *
 ****************************/

"use strict"; 

(function ()
{
    /**
     * PHP Project Finder. Main class to manage the functionality of the GitHub Project Finder site. 
     * 
     * @type object
     */
    var ProjectFinder = {
                
        csrfHash : "",
        csrfToken : "",
        
        eventListenersLoaded : false, 
        initialLoadComplete : false, 
        loadProcessRunning : false, 

        /**
         * Initialize the Project Finder object
         * 
         * @returns {void}
         */
        initialize : function(){
                        
            //Register event listenters
            addHandlers();
            
            //Retrieve projects list
            this.getProjectListData();
        },

        /**
         * Fetch the current list of project data in order to build the table
         * 
         * @returns {void}
         */
        getProjectListData : function(){
                        
            var self = this;
            var table = jQuery("table#projectListResults");

            if (!this.initialLoadComplete){
                displayMessage("<i class='fas fa-spinner fa-spin'></i> &nbsp;&nbsp; Loading Projects..", "info");
            }

            hideMessage(); //Hide any existing messages

            jQuery.ajax({
                type : "GET",
                url : self.baseUrl + "getProjectList",//  "ProjectFinderJS/getProjectListData", 
                data : {
                    id : "test"
                },
                dataType : 'json',
                cache : false,
                //contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                //headers: {'X-Requested-With': 'XMLHttpRequest', "X-CSRF-TOKEN" : self.csrfHash},
                complete : function (results){
                    
                    self.initialLoadComplete = true;
                    
                    //Update CSRF token
                    if (typeof results.responseJSON.data.token !== "undefined"){
                        self.csrfHash = results.responseJSON.data.token;
                    }
                },
                success : function (results){
                                    
                    console.log(results);
                    
                    if (results.error){
                        displayMessage("<i class='fa fa-exclamation-triangle'></i> &nbsp;&nbsp; Error: " + results.error_msg, "error", true);
                        return false;
                    }

                    if (results.data.project_data){
                        buildProjectListTable(results.data.project_data);
                    }
                    
                    //Update timestamp
                    if (results.data.last_updated){
                        updateLastUpdatedTimestamp(results.data.last_updated);
                    }
                },
                error : function (a, b, c){
                    table.find("tbody").html("<tr><td colspan='2' class='empty-table'>No Results</td></tr>");
                    console.log(a, b, c);
                }
            });
        },
        
        /**
         * Retreive the detail for a given project and display it in a modal 
         * 
         * @param {int} id
         * @returns {Boolean}
         */
        getProjectListDetail : function(id){
                        
            if (!id){
                console.log("Missing repository id:", id);
                return false;
            }
            
            var self = this;
            var modal = jQuery("div#project_detail_modal");
            var body  = modal.find("div.modal-body");
            var title = modal.find("div.modal-header span.modal-title");
            var html = "";
            
            jQuery.ajax({
                type : "GET",
                url : self.baseUrl + "getProjectListDetail", //"ProjectFinderJS/getProjectListDetail", 
                data : {
                    repo_id : id
                },
                dataType : 'json',
                cache : false,
                beforeSend : function (){
                    body.html("<div class='modal_overlay' style='display: block;'><i class='fas fa-spinner fa-spin'></i></div>");
                    modal.modal("show");
                },
                complete : function (results){
                                        
                    //Update CSRF token
                    if (typeof results.responseJSON.data.token !== "undefined"){
                        self.csrfHash = results.responseJSON.data.token;
                    }
                },
                success : function (results){
                    
                    hideMessage(); //Hide any existing messages

                    if (results.error){
                        displayMessage("<i class='fa fa-exclamation-triangle'></i> &nbsp;&nbsp; Error: " + results.error_msg, "error", true);
                        return false;
                    }
                    
                    if (results.data.project_data){
                        
                        html += "<table class='table table-bordered'><tbody>";
                        html += "<tr><td class='noWrap'>Repository ID</td><td>" + results.data.project_data.repository_id + "</td></tr>";
                        html += "<tr><td class='noWrap'>Name</td><td>" + results.data.project_data.name + "</td></tr>";
                        html += "<tr><td class='noWrap'>Description</td><td>" + (results.data.project_data.description ? results.data.project_data.description : "[None]")+ "</td></tr>";
                        html += "<tr><td class='noWrap'>Link to Project</td><td><a target='_blank' href='" + results.data.project_data.html_url + "'>" + results.data.project_data.html_url + "</a></td></tr>";
                        html += "<tr><td class='noWrap'>Stargazer Count</td><td>" + formatNumber(results.data.project_data.stargazers_count)  + "</td></tr>";
                        html += "<tr><td class='noWrap'>Date Created</td><td>" + results.data.project_data.created_at + "</td></tr>";
                        html += "<tr><td class='noWrap'>Last Push Date</td><td>" + results.data.project_data.pushed_at + "</td></tr>";
                        html += "</tbody></table>";
                        
                        body.html(html);
                    }
                },
                error : function (a, b, c){
                    console.log(a, b, c);
                    body.html("<div class='modal_overlay' style='display: block;'><i class='fa fa-exclamation-triangle'></i> &nbsp;&nbsp; Whoops.. an unexpected error has occurred.</div>");
                }
            });
        },
        
        /**
         * Refresh database and table with the most current GitHub project data
         * 
         * @returns {Boolean}
         */
        loadGitHubProjects : function(){

            var self = this;

            if (this.loadProcessRunning){
                displayMessage("<i class='fa fa-exclamation-triangle'></i> &nbsp;&nbsp; Processing is already running. Try again momentarily.", "error");
                return false;
            }

            this.loadProcessRunning = true; //Helps to prevent multiple rapid requests from user

            jQuery.ajax({
                type : "POST",
                url : self.baseUrl + "loadGitHubProjects", //"ProjectFinderJS/loadGitHubProjects",
                data : {},
                dataType : 'json',
                cache : false,
                beforeSend: function (){
                    displayMessage("<i class='fas fa-spinner fa-spin'></i> &nbsp;&nbsp; Working.. This may take a few moments.", "info");
                },
                complete : function (results){
                                     
                    self.loadProcessRunning = false;
                    
                    //Update CSRF token
                    if (typeof results.responseJSON.data.token !== "undefined"){
                        self.csrfHash = results.responseJSON.data.token;
                    }                    
                },
                success : function (results){

                    hideMessage(); //Hide any existing messages

                    if (results.error){
                        displayMessage("<i class='fa fa-exclamation-triangle'></i> &nbsp;&nbsp; Error: " + results.error_msg, "error", true);
                        console.log("results: ", results);
                        return false;
                    }

                    if (results.data.success_msg){
                        displayMessage("<i class='fa fa-check'></i> &nbsp;&nbsp; " + results.data.success_msg, "success", true);
                    }
                    
                    //Rebuild projects table
                    if (results.data.project_data){
                        buildProjectListTable(results.data.project_data);
                    }
                    
                    //Update timestamp
                    if (results.data.last_updated){
                        updateLastUpdatedTimestamp(results.data.last_updated);
                    }
                },
                error : function (a, b, c){
                    console.log(a, b, c);
                    displayMessage("<i class='fa fa-exclamation-triangle'></i> &nbsp;&nbsp; Whoops.. an unexpected error has occurred.", "error", true);
                }
            });
        }
    };
    
    /*
     * Helper functions
     */
    
    /**
     * Event listeners
     * 
     * @returns {Boolean}
     */
    var addHandlers = function(){
        
        //No need to attach the same listeners more than once
        if (ProjectFinder.eventListenersLoaded){
            return false;
        }
        
        jQuery("div#projectListContainer table#projectListResults").on("click", "tbody tr", function(){
            
            var id = jQuery(this).attr("data-repo-id");
            
            //Display modal with detail
            ProjectFinder.getProjectListDetail(id);
        });
        
        jQuery("div#projectListContainer span#launch_update_request").click(function(){     
            ProjectFinder.loadGitHubProjects();
        });
        
        ProjectFinder.eventListenersLoaded = true;
    };   
    
    /**
     * Given an array of projects, build the HTML table
     * 
     * @param {array} data
     * @returns {Boolean}
     */
    var buildProjectListTable = function(data){
        
        if (!data || !data.length){
            return false;
        }
        
        var table = jQuery("table#projectListResults");
        var html = "";
        var i = 0;
        
        for (i = 0; i < data.length; i++){
            html += "<tr data-repo-id='" + data[i].repository_id + "'>";
            html +=     "<td>" + data[i].name + "</td>";
            html +=     "<td>" + formatNumber(data[i].stargazers_count) + "</td>";
            html += "</tr>";
        }
        
        //Clear table and remove DataTable formatting before re-writing
        initDataTable.emptyTable(table);
        initDataTable.destroy(table);    
        
        //Populate table
        table.find("tbody").html(html);
        
        //Now use DataTable to format and add functionality to the table
        initDataTable.activate(table);
    };

    /**
     * Update the timestamp of the last GutHUb search request
     * 
     * @param {string} time
     * @returns {Boolean}
     */
    var updateLastUpdatedTimestamp = function(time){
        
        if (!time){
            return false;
        }
        
        var span = jQuery("div#projectListContainer span#last_update_timestamp");
        span.html("Last Updated: " + time).show();
    };
                
    /**
     * Display message to user
     * 
     * @param {string} msg
     * @param {string} type
     * @param {Boolean} dismissable
     * @returns {Boolean}
     */
    var displayMessage = function(msg, type, dismissable){
        
        if (!msg){
            return false;
        }

        var div = jQuery("div#projectListContainer div#info-msg-container");
        var html = "", cls = "";
        
        if (type){
            switch(type){
                case "error":
                    cls = "alert-danger";
                    break;
                case "success":
                    cls = "alert-success";
                    break;
                case "info":
                default: 
                    cls = "alert-info";
                    break;
            }
        }
                
        //Include dismissable button on alert
        if (dismissable === true){
            msg += "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";
        }
        
        html = "<div class='alert alert-dismissible " + cls + "' role='alert'>" + msg + "</div>";
        
        //Display message with proper style
        div.html(html).show(); //removeClass("alert-danger alert-success alert-primary").
    };
    
    /**
     * Hide message
     * 
     * @returns {void}
     */
    var hideMessage = function(){
        
        var div = jQuery("div#projectListContainer div#info-msg-container");
        
        div.html("").hide();
    };
        
    /**
     * Manage DataTable formatting
     * 
     * @type object
     */
    var initDataTable = {

        activate : function(el){

            if (!el.length){
                return false;
            }

            try {
                el.dataTable({
                    iDisplayLength: 50,
                    autoWidth: false,
                    aaSorting: [],
                    language: {
                        searchPlaceholder: 'Filter Results',
                        search: '',
                        emptyTable : 'No results',
                        infoEmpty : '',
                        lengthMenu : 'Display _MENU_'}
                });
            }
            catch (err){
                console.log(err);
            }
        },

        destroy : function(el){

            if (!el.length){
                return false;
            }

            var table = el.DataTable();
            table.clear().destroy(); //Remove dataTable formatting
        },
        
        emptyTable : function(el){

            if (!el.length){
                return false;
            }

            el.find('tbody').html("");
        }
    };

    var formatNumber = function(val){
        
        if (!val){
            return val;
        }
        
        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    };
    
    //Add the object to the window
    window.ProjectFinder = ProjectFinder;
})();