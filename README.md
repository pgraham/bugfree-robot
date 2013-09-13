# Bugfree Robot
Enhanced Mercurial Log

## Install

 1. Clone from github:

        git clone https://github.com/pgraham/bugfree-robot.git
        cd bugfree-robot/

 2. \[Optional] If you don't have Composer installed globally: `curl -sS https://getcomposer.org/installer | php`
 3. Install Composer dependencies: `composer install`
 4. Copy example config to home directory: `cp config.example.yaml ~/.my-hg-log.yaml`
 5. Edit config file to match your setup. Add as many users and repos as you
    would like, results will be aggregated.
 6. Make my-hg-log executable: `chmod +x my-hg-log.php`
 7. \[Optional] Add my-hg-log to PATH (assuming ~/bin is in your PATH):

        cd ~/bin
        ln -s /path/to/bugfree-robot/my-hg-log.php my-hg-log

## Running

The default execution of my-hg-log will print all commits by all configured
users in all configured repositories for the current month.

    $ my-hg-log
