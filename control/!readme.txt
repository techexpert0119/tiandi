This file explains some of the core files used in sites built by and all copyright Wotnot Web Works Limited.


1. CONTROL FOLDER *** DO NOT EDIT THESE *** AS THEY ARE PERIODICALLY UPDATED WITH PATCHES AND IMPROVEMENTS


functions.inc.php - Core functions file which is called by all pages.

mysql.inc.php - Core file for the CMS system.

export.inc.php - Core file for exporting from CMS to spreadsheet.

control.js - Core javascript functions for the CMS.

head.inc.php - Called by all header pages in CMS. If additional functionality is required add to head1.inc.php which is not updated.

mysqledit.php - Template for setting up new CMS pages. Always save as other file name before using.

listfiles.php - Popup page for the file selector in the CMS.

jselect.php - Javascript for the refresh buttons in the CMS.

checkradio.css - CSS for the checkboxes and radio buttons used in the CMS. 

_temp - This folder should be set with full write access as it is used for uploading files in the CMS. It is protected by being in the restricted control folder.

db_log.php - CMS file for the database log

db_make.php - CMS file for recording image make



2. CONTROL FOLDER - THESE CAN BE EDITED BUT WITH CARE AS FUNCTIONS MAY BE USED FROM MULTIPLE PLACES


settings.inc.php - Called by functions.inc.php for all pages. Contains settings to operate ths site. Can be edited by with care.   

includes.inc.php - Called by settings.inc.php for all pages. Contains specific functions for this site. Can be edited by with care.

jquery.php - Handles ajax calls from javascript used by some CMS pages.

styles.css / leftmenu.css - CSS styles for the CMS to set colours etc.

minify.php - Page to minify and combine CSS and JS files for PageSpeed

login.php - Used for CMS login handling



3. SCRIPTS FOLDER *** DO NOT EDIT THESE *** AS THEY ARE PERIODICALLY UPDATED WITH PATCHES AND IMPROVEMENTS


mouselayer.js - Javascript for image hover view in the CMS.

geoplugin.class.php - Used by the site for calling Geo locations based on IP

securimage folder - Old captcha function used by some sites

recaptcha.inc.php - Google reCaptcha function now more commonly used

phpmailer6 folder and phpmailer6.inc.php - php class used by site for sending emails

chosen folder and chosen.inc.php - php class used by CMS for better selectors

phpspreadsheet7 folder - php class used for exporting spreadsheets

vendor folder - Many php functions loaded and updated with composer
