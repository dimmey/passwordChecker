# passwordChecker
Checks a list of passwords against a set of dynamically defined rules

The passwords to be checked are contained in the database. The script retrieves the list of passwords
and then checks them against a set of rules that are retrieved by a .yaml config file.

## Installation
In order to run the application you should take the following steps

1. Create a database and import the *'passwords.sql'* file
2. Update your database details in the *'application/configuration/config.php'* file.
3. If it is not installed, install the **yaml php extension** (http://php.net/manual/en/yaml.installation.php)

## Check Rules Definition
The rules against wich each password is checked, are contained in *'appclication/configuration/passwordRules.yaml'*
For each rule we define the regular expression that is evaluated and an error message that will be outputed in case the check fails. By adding or removing new rule items, the checking process can be easily expanded.

## Execution
The script is easily executed from command line by typing **php index.php**

## System Settings
The script was developed in Ubuntu 12.04 with PHP5.5 and MySQL 5.5
