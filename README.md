The webservice wizard enhances the Moodle webservice management experience in 3 main ways: 

1-Reducing the time it takes to add a web service by reducing the number of clicks and pages an administrator needs to visit to complete the new web service setup 

2-Centralizing all the steps of adding a new web service in one form.  

3-Having a central and global view (a dashboard) of the web services already in the Moodle instance. The dashboard offers an easy and fast access to all the web services, and for each of them, their tokens, functions and any specific option.  
 
Location:

Because there are several steps involved in setting up a web service, the web service wizard offers a one-stop form where all these steps are done. From the web service name to the new user, role, token, adding functions, etc. to create a new web service you can navigate to: 

Site administration > Server > Web Services > Add new web service with webservice wizard 

This will save you from going through all the other steps.  

Alternatively, you can use the navigation link to the web service wizard dashboard and from there create a new web service by clicking on the “Add New” tab. 

Link: 

The link to the dashboard would look like this {YourMoodleBaseLink}/local/wswizard/dashboard.php 

Features :

From the dashboard you also have a global view of all the web services in your Moodle site and can: 

Manage external services: Enable, disable, edit, delete.  

Enable/disable web service restrictions such as ability to upload/download files. 

Simply manage web service tokens 

Logs:

Logs are found in Site administration > Server > Web Services > Web Service Wizard Logs 

The link to the logs would look like this {YourMoodleBaseLink}/local/wswizard/logs.php 

 

Notes:

Deleting a web service also deletes: its functions, and its tokens but not the roles. A role and user may be reused.   

The Webservice Wizard only allows webservice authorization through tokens, as it is the recommended and most secure method to call web services externally. 

The Webservice Wizard does not allow management or editing of built-in external services such as the moodle mobile web service or those created by third party plugins. 

