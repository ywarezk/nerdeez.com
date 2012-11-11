set :application, "nerdeez.com"
set :repository,  "https://github.com/ywarezk/nerdeez.com.git"
set :deploy_to, "/home/Y2w8A2r4E7z6K/public_html/nerdeez.com/"
set :zf_path, "/var/zend/framework/library"

set :scm, :git
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `git`, `mercurial`, `perforce`, `subversion` or `none`

role :web, "50.56.66.17:15493"                          # Your HTTP server, Apache/etc
set :user, "Y2w8A2r4E7z6K"
set :scm_username, "ywarezk"
set :scm_password, "CurhxHa45Pu,"
# gem install capistrano-tags to enable tag and branches deploying
# require 'capistrano-tags'
# gem install capistrano_colors to colorize capistano output
# require 'capistrano_colors'
