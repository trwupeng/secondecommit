#ifndef _SHARE_LIB_H_
#define _SHARE_LIB_H_
#include <string>
#include "tinyxml2.h"
#include <vector>
#include <map>
#include <iostream>
#include <fstream>
#include <assert.h>
#include "Obj.h"
using namespace std;

class ShareLib
{
public:
	static string getString( tinyxml2::XMLElement* node, const char* name, const char* Default = "" )
	{
		const char* p = node->Attribute( name );
		if ( NULL == p )
		{
			return string(Default);
		}
		return string(p);
	}

	static int getInt( tinyxml2::XMLElement* node, const char* name )
	{
		const char* p = node->Attribute( name );
		if ( NULL == p )
		{
			return 0;
		}
		return atoi(p);
	}

	static bool getBool( tinyxml2::XMLElement* node, const char* name )
	{
		const char* p = node->Attribute( name );
		if ( NULL == p )
		{
			return false;
		}

		return 0 == strcmp( "true", p );
	}
};


#endif //_SHARE_LIB_H_
