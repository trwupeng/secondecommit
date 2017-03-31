#ifndef _UTILS_H_
#define _UTILS_H_
#include "tinyxml2.h"
#include <string>
#include <vector>
#include <map>
#include <fstream>
#include <iostream>
#include <stdlib.h>
using namespace std;

class Utils
{
public:
	static string getXmlAttrStr( tinyxml2::XMLElement* node, const string& name, const char* Default = "" );
	static int getXmlAttrInt( tinyxml2::XMLElement* node, const string& name, int Default = 0 );
	static bool getXmlAttrBool( tinyxml2::XMLElement* node, const string& name, bool Default = false );
	static float getXmlAttrFloat( tinyxml2::XMLElement* node, const string& name, float Default = 0.0f );
	
	static string transOcType( const string& type );
	static string transSwiftType( const string& type );
	static string transJavaType( const string& type );

	static string& trim( string& s );
};

#endif //_UTILS_H_
