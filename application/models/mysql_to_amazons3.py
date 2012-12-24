#if you are using only python then just remove the below 3 lines until  setup_environ(settings)
from django.core.management import setup_environ
import settings
setup_environ(settings)

import tarfile
import subprocess
import tempfile
import os
#please make sure that S3 module from Amazon was on your sys.path
import S3
import mimetypes



def upload_db_to_Amazons3():
    """ function which uploadd mysql database to amazon s3"""
    
    #If you are using this script without django then please 
    #fill the below five variables manually
    
    #S3 access key
    AWS_ACCESS_KEY_ID = settings.AWS_ACCESS_KEY_ID
    #S3 secret access key
    AWS_SECRET_ACCESS_KEY = settings.AWS_SECRET_ACCESS_KEY
    #S3 bucket name
    BUCKET_NAME = settings.BUCKET_NAME
    #MYSQL_DUMP_PATH should be the path to the mysqldump executable file
    #To know where  mysqldump executable is present in your local system
    #use the command "which mysqldump".
    MYSQL_DUMP_PATH = settings.MYSQL_DUMP_PATH
    #S3 database name
    DATABASE_NAME = settings.DATABASE_NAME
    
    #Archive name with out any extension(i.e.):
    #what name do you want for the file which is uploaded to AMazon S3
    #please give the name  without any extension
    #Also note that a copy of this file will be stored in the directory where the script resides
    ARCHIVE_NAME = settings.ARCHIVE_NAME
    
    
    #if there is no Archive name then use database name
    if len(settings.ARCHIVE_NAME.strip()) == 0 :
        ARCHIVE_NAME = settings.DATABASE_NAME   
    
           
    print "Preparing "+settings.ARCHIVE_NAME+"_archive.tar.gz from the database dump................"        
    #create structure file    
    proc1 = subprocess.Popen(settings.MYSQL_DUMP_PATH+" --no-data  -u root -pwelcome   -x  --databases  doloto",shell=True,stdout=subprocess.PIPE,stderr=subprocess.STDOUT)
    #create data file
    proc3 = subprocess.Popen(settings.MYSQL_DUMP_PATH+" --no-create-info  -u root -pwelcome   -x  --databases  doloto",shell=True,stdout=subprocess.PIPE,stderr=subprocess.STDOUT)
    
    #create temp files
    t1 = tempfile.NamedTemporaryFile()
    t2 = tempfile.NamedTemporaryFile()    
    t1.write(proc1.communicate()[0])
    t2.write(proc3.communicate()[0])
        
    #create  tar.gz for the above two files
    tar = tarfile.open( (os.path.join(os.curdir, settings.ARCHIVE_NAME+"_archive.tar.gz")), "w|gz")
    tar.add(t1.name,ARCHIVE_NAME+"_struct.sql")
    tar.add(t2.name,ARCHIVE_NAME+"_data.sql")    
    #delete temp files
    t1.close()    
    t2.close()    
    tar.close()   
    
    #upload the temp.tar.gz which is in the present direcotry to amazon S3
    print "uploading the "+settings.ARCHIVE_NAME+"_archive.tar.gz  file to Amazon S3..............."
    
    conn = S3.AWSAuthConnection(AWS_ACCESS_KEY_ID,AWS_SECRET_ACCESS_KEY)
    #get all buckets from amazon S3
    response =  conn.list_all_my_buckets()
    buckets = response.entries
    #is the bucket which you have specified is already there
    flag = False
    for bucket in buckets :
        if bucket.name == BUCKET_NAME :
            flag = True
    
    #if there is no bucket with that name     
    if flag == False:
       print "There is no bucket with name "+BUCKET_NAME+" in your Amazon S3 account"
       print "Error : Please enter an appropriate bucket name and re-run the script"
       return
    
    #upload file to Amazon S3    
    tardata = open(os.path.join(os.curdir, settings.ARCHIVE_NAME+"_archive.tar.gz") , "rb").read()
    response = conn.put(BUCKET_NAME,settings.ARCHIVE_NAME+"_archive.tar.gz",S3.S3Object(tardata))
    
    if response.http_response.status == 200 :
         print "sucessfully uploaded the archive to Amazon S3"
    else:
         print "Uploading database dump to Amazon S3 is not successful" 
         print "Error : "+response.message 



upload_db_to_Amazons3()



















