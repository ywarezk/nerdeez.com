# Add colours to the Capistrano output
require 'capistrano_colors'

# What is the name of the local application?
set :application, "MyApp"

# Is sudo required to manipulate files on the remote server?
set :use_sudo, false

# How are the project files being transferred to the remote server?
set :deploy_via, :copy

# Maintain a local repository cache. Speeds up the copy process.
set :copy_cache, false

# Ignore any local files?
set :copy_exclude, %w(.git)

######################################################
# Git
######################################################

# What version control solution does the project use?
set :scm, :git

# Where is the local repository?
set :repository, "file:///Users/derekbarber/vhosts/myapp"

#############################################################
# Stages
#############################################################
set :stages, %w(production development)
set :stage_dir, "application/configs/deploy"
set :default_stage, "application" #if we only do “cap deploy” this will be the stage used.
require 'capistrano/ext/multistage' #yes. First we set and then we require.

#############################################################
# Tasks
#############################################################

# Remove older realeases. By default, it will remove all older then the 5th.
after :deploy, 'deploy:cleanup'