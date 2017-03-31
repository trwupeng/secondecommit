#include "mysqltest.h"
#include "sharelib.h"
#include <iostream>
#include <mysql.h>
//#include "DbCtrl.h"
using namespace std;

void mysql_test()
{
	MYSQL mysql;
	if( mysql_init(&mysql) == NULL )
	{
		cout<<"inital mysql handle error"<<endl;
		return ;
	}    
	  
	if (mysql_real_connect(&mysql,"localhost","root","token123","ts",0,NULL,0) == NULL)
	{
		cout<<"Failed to connect to database: Error"<<endl;
		return ;
	}    
			 
	if(mysql_set_character_set(&mysql,"UTF8") != 0)
	{
		cout<<"mysql_set_character_set Error"<<endl;
		return ;
	}

	if ( 0 != mysql_query( &mysql, "select * from user" ) )
	{
		cout<<"query error"<<endl;
		return ;
	}
	MYSQL_RES* result = mysql_store_result( &mysql );
	unsigned int fieldNum = mysql_num_fields(result);
	MYSQL_ROW row;
	vector< string > vName;
	MYSQL_FIELD* field;
	while( NULL != (field=mysql_fetch_field( result ) ) )
	{
		vName.push_back( field->name );
	}
	while( NULL != (row= mysql_fetch_row( result )) )
	{
		unsigned long* len = mysql_fetch_lengths( result );
		for ( unsigned int i=0; i<fieldNum; ++i )
		{
			string line;
			if ( NULL != row[i] )
			{
				line.append( (char* )row[i], len[i] );
			}
			else
			{
				line = "NULL";
			}
			if ( "bb" == vName[i] )
			{
				int n = 0;
				memcpy( &n, line.data(), sizeof(int) );
				cout<<n<<endl;
			}
			else
			{
				cout<<line<<endl;
			}
		}
	}

/*
	if ( !DbCtrl::create_user( &mysql ) )
	{
		cout<<"create table failed:"<<mysql_error( &mysql )<<endl;
	}
	string bb;
	int i = 8;
	bb.append( (char*)&i, sizeof(i) );
	if ( !DbCtrl::insert_user( &mysql, "token", 1, "231231", "adfasfd", "dfs@sdf.com", bb ) )
	{
		cout<<"insert table failed:"<<mysql_error( &mysql )<<endl;
	}
*/

	cout<<"connect success"<<endl;

	mysql_close(&mysql);
}
