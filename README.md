# README file

### This project consist of 2 different webforms.

#### **The first one is an internship evaluation form.** 
Admins have the ability to change the context of the webform to suit their needs such as questions and descriptions.
Attach is an documentation of how the database is set up to implement this feature. Users fill the form and submit it. The admins have the ability to access the submissions. 
The admins can view the data in a pdf.

#### **The second one is an TA application form.** 
Admins can change context of questions, and description and classes. Also can access to submitted applications. Details on database is in documentations.

#### **Set up**
If you want to use this you would need to put this on you webserver and construct an config.php . config.php will be consist of your db credentials. Also you would need to run the sql.txt on your db (either through command prompt or mySQL workbench)to set up the database for these webforms. This only works for sql db.

#### Sample config.php
```
<?php
  $dbname = "blah"
  $dbuser = "blah"
  $dbpassword = "blah"
?>
```
