# TECHNOVA SOLUTIONS WEB HOSTING DEPLOYMENT SYSTEM

## PROJECT OVERVIEW

This is a complete web hosting management system for TechNova Solutions. It includes a fully functional website, admin dashboard, backup system, security center, and user management. The system is ready for deployment on any PHP hosting provider like Hostinger.

## SYSTEM REQUIREMENTS

PHP Version 7.4 or higher
MySQL Version 5.7 or higher
Web Server Apache or Nginx
Browser Any modern browser Chrome Firefox Safari Edge

## INSTALLATION GUIDE

Step 1 Install Local Server
Download and install one of these free options
XAMPP from apachefriends.org
WAMP from wampserver.com
Laragon from laragon.org

Step 2 Start Services
Open your control panel
Start Apache Web Server
Start MySQL Database

Step 3 Create Project Folder
Go to your webroot directory
For XAMPP this is C xampp htdocs
For WAMP this is C wamp www
Create a new folder called technova

Step 4 Copy Files
Copy all PHP files into the technova folder
Files needed are
install.php
index.php
login.php
dashboard.php
forgot_password.php
reset_password.php
backup.php
security.php
profile.php
logout.php
db.php

Step 5 Run Installation
Open your web browser
Go to localhost technova install.php
Wait for the installation to complete
Save the admin credentials shown on screen

Step 6 Access the Website
Homepage localhost technova index.php
Login Page localhost technova login.php
Admin Dashboard localhost technova dashboard.php

## DEFAULT ADMIN CREDENTIALS

Email Address admin@technova.com
Password TechNova@2026

These credentials are shown only once during installation
Save them in a secure location
You can change them after first login

## SYSTEM FEATURES

Frontend Website
Fully responsive design works on all devices
Modern gradient color scheme
Animated statistics counter
Mobile friendly navigation menu
Services showcase section
About company information
Contact information footer

Admin Dashboard
Real time statistics display
Total users count
Backup count
Daily activity tracking
Server uptime display
Recent activity log
Quick action buttons
System information panel

Security Center
SSL certificate management
Firewall protection status
Security headers configuration
Password policy enforcement
Security score meter
Deployment checklist for Hostinger
One click SSL renewal option

Backup Manager
Create manual database backups
Restore from any backup point
Delete old backups
View backup size and date
Track who created each backup
Backup strategy guide included
Cron job setup instructions

User Profile Management
Update personal information
Change password with strength meter
View account details
Session management
Activity logging

Password Recovery
Forgot password email simulation
Secure reset token generation
One hour token expiration
Password strength requirements
Confirm password validation

## DATABASE STRUCTURE

Users Table
id Primary key auto increment
fullname User full name
email Unique email address
password_hash Hashed password
phone Optional phone number
role Admin or manager
reset_token Password reset token
reset_expires Token expiration time
last_login Last login timestamp
created_at Account creation time

Backups Table
id Primary key auto increment
filename Backup file name
filepath Full file path
size Backup file size
type Database or full backup
created_by User who created backup
created_at Backup creation time

Activity Logs Table
id Primary key auto increment
user_id User who performed action
action Description of action
ip_address User IP address
created_at Action timestamp

Security Settings Table
id Primary key auto increment
setting_key Setting name
setting_value Setting value
updated_at Last update time

## DEPLOYMENT TO HOSTINGER

Step 1 Sign Up
Go to Hostinger website
Create a new account
Choose a hosting plan or free trial

Step 2 Set Up Hosting
Access hPanel control panel
Go to File Manager
Upload all PHP files to public_html folder

Step 3 Create Database
Go to MySQL Databases in hPanel
Create new database
Create database user
Assign user to database
Note the database credentials

Step 4 Update Configuration
Edit db.php file
Update host database name username password
Use Hostinger provided credentials

Step 5 Run Installation
Access your domain install.php
Complete installation
Remove install.php for security

Step 6 Configure SSL
Go to SSL in hPanel
Install free Let's Encrypt SSL
Force HTTPS redirect
Verify SSL working

Step 7 Set Up Backups
Go to Backups in hPanel
Configure daily automatic backups
Set backup retention period

Step 8 Security Settings
Enable WAF protection
Set up bot blocker
Configure hotlink protection
Enable PHP security settings

## FILE STRUCTURE

technova
index.php Main website homepage
login.php Admin login page
dashboard.php Admin dashboard
backup.php Backup management system
security.php Security settings center
profile.php User profile management
forgot_password.php Password reset request
reset_password.php New password setup
logout.php Session destruction
db.php Database connection
install.php One time setup script
backups folder Database backup storage

## TROUBLESHOOTING

Problem Database connection failed
Solution Check MySQL is running in XAMPP WAMP
Verify db.php credentials match your setup

Problem Installation page not found
Solution Make sure files are in correct folder
Check Apache is running on port 80

Problem Backup creation fails
Solution Install MySQL command line tools
Check backups folder has write permissions

Problem Login not working
Solution Run install.php again to reset database
Check PHP sessions are enabled

Problem Website not responsive
Solution Clear browser cache
Test on different screen sizes

## RESPONSIVE DESIGN BREAKPOINTS

Desktop 1024px and above
Tablet 768px to 1023px
Mobile 480px to 767px
Small Mobile Below 480px

## SECURITY FEATURES

Password hashing using PHP password_hash
SQL injection prevention using PDO
XSS protection via htmlspecialchars
Session management with timeout
Password strength validation
Reset token expiration
IP address logging
Activity tracking

## BACKUP STRATEGY

Manual Backups Click create backup button anytime
Automated Backups Set up cron job for daily backups
Recovery Process Click restore button to recover
Retention Policy Keep at least 3 recent backups
Storage Location Backups folder in project root

## CRON JOB SETUP FOR AUTOMATED BACKUPS

Linux Server Command
0 2 \* \* \* wget -O dev null http yourdomain com technova backup php create 1

Windows Task Scheduler
Create new task
Set trigger to daily at 2 AM
Set action to run php backup.php

## FOR PRESENTATION

Screenshots to Capture
Installation completion screen
Website homepage
Login page
Admin dashboard with stats
Backup manager showing backups
Security center with active status
Forgot password page
Mobile view of website

Presentation Order
Introduction to hosting provider selection
Domain registration and DNS setup
Website deployment process
Security implementation SSL and firewall
Backup and recovery demonstration
Final remarks and lessons learned

## ASSESSMENT CRITERIA COVERAGE

Documentation 50 marks
Complete step by step guide with screenshots

Presentation 30 marks
Professional PowerPoint with clear structure

Individual Understanding 20 marks
Q and A session on system functionality

Total 100 marks contributing 35 percent to module

## CONTACT INFORMATION

For project related questions
Email your instructor
Include your group name and project details

For technical support
Check PHP documentation online
Refer to W3Schools PHP tutorials

## VERSION INFORMATION

System Version 2.0
Release Date June 2026
PHP Version Required 7.4 plus
MySQL Version Required 5.7 plus

## ACKNOWLEDGMENTS

This system was developed for TechNova Solutions
All code is original and tested
No external frameworks used
Pure PHP MySQL HTML CSS JavaScript

---
