# general settings
default_run_options[:pty] = true
set :use_sudo, true
 
# source control settings
set :scm, :git
set :deploy_via, :remote_cache
set :repository, "ssh://git@github.com:ywarezk/nerdeez.com.git"
 
role :app, "nerdeez.com"
role :web, "nerdeez.com"
role :db, "nerdeez", :primary => true
 
set :deploy_to, "/home/ywarezk2824/public_html/"

set :user, "ywarezk2824"
set :password, "KhruiNhe$%Vhpv"

namespace :deploy do
 
task :migrate do
# overrides the standard Rails database migrations task
end

task :start, :roles => :app do
 
end
 
task :stop, :roles => :app do
 
end
 
task :restart, :roles => :app do
# no restart required for Apache/mod_php
end
 
end