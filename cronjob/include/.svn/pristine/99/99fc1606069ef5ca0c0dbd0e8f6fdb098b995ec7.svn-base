<?php 
// This file should not be read in a usual way!!
die();
?>

#########################################
# 			Database api				#
#########################################
# connections	= "conn1, conn2, conn3"	#
#########################################
[ Database ]
connections 	= "default"

#########################################
# 	Database for connection default		#
#########################################
# class	= "\my\namespace\ClassName"		#
# ... the rest is specific				#
#########################################
[ Database.default ]
class 			= "\io\db\MySQLDatabase"
host 			= "localhost"
user 			= "user"
password 		= "pass"
schema 			= "schema"
prefix 			= ""

#########################################
# 			Logging api					#
#########################################
# connections	= "appender1, appender2	#
#########################################
[ Logging ]
appenders 		= "appender1, appender2"

[ Logging.appender1 ]
class			= "\io\log\FileAppender"
level			= "INFO"
file.output		= "/var/log/logging.log"
file.size		= "5M"

[ Logging.appender1 ]
class			= "\io\log\DatabaseAppender"
level			= "INFO"
db.table		= "logging"
db.connection	= "default"

# there may be more to come

