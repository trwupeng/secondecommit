bin_PROGRAMS=db
db_SOURCES=Field.cpp Index.cpp main.cpp mysqltest.cpp Table.cpp tinyxml2.cpp Update.cpp Where.cpp Obj.cpp Select.cpp Delete.cpp \
		   Utils.cpp

INCLUDES=-I /usr/local/mysql/include
db_LDFLAGS=-L /usr/local/mysql/lib -lmysqlclient

AUTOMAKE_OPTIONS=foreign

cp:
	cp db dbbak

gen:
	./dbbak db.xml
	cp DbCtrl.class.php /usr/local/nginx/html/app/Home/pub/
	chmod 700 /usr/local/nginx/html/app/Home/pub/DbCtrl.class.php
	chown www.www /usr/local/nginx/html/app/Home/pub/DbCtrl.class.php
