# Simple-Votes-Checker
So this is a first commit of the Simple Votes Checker tool. The tool is quite basic, but some thought has been put behind the coding.
Specifically, this project leverages the Namadexer DB to deliver data to a simple PHP application. The use in the Namada ecosystem is primarily (atm) the ability to see votes cast in governance and perhaps even more importantly, to see which proposals others have voted on, that the particular wallet in question has not yet voted on themselves, ie a sort of task list for pending votes.
# Prerequisites
The installation is pretty straightforward, but there are some prerequisites:
1. A working namadexer installation which serves a PostgreSQL database
Note: Namadexer has been discontinued from official development, but is developed on a community basis via Spork's fork.
You will find this fork here: https://github.com/vknowable/namadexer
2. PHP 8.1+, installed with either Apache or nginx, or another suitable webserver, with Postgre SQL module
# Installation
1. Place all php files in the package in the same directory, which is the directory served by your php-enabled webserver
2. Make sure php actually parses files before proceeding to the next step, as otherwise it could be a big security risk
3. Edit the db_settings423.php-file, and input the appropriate values for your local setup.
4. The pages should run out of the box if everything above is satisfied
# Further
People are welcome to contribute to this repo, or fork to their own and use in any further development.
