#include "Utils.h"


string Utils::getXmlAttrStr( tinyxml2::XMLElement* node, const string& name, const char* Default )
{
	const char* p = node->Attribute( name.data() );
	if ( NULL == p )
	{
		return (NULL!=Default?Default:"");
	}
	return p;
}
int Utils::getXmlAttrInt( tinyxml2::XMLElement* node, const string& name, int Default )
{
	string s = getXmlAttrStr( node, name );
	if ( !s.empty() )
	{
		return atoi(s.data());
	}
	return Default;
}
bool Utils::getXmlAttrBool( tinyxml2::XMLElement* node, const string& name, bool Default )
{
	string s = getXmlAttrStr( node, name );
	if ( !s.empty() )
	{
		return ("true"==s);
	}
	return Default;
}
float Utils::getXmlAttrFloat( tinyxml2::XMLElement* node, const string& name, float Default )
{
	string s = getXmlAttrStr( node, name );
	if ( !s.empty() )
	{
		return atof(s.data());
	}
	return Default;
}

string Utils::transOcType( const string& type )
{
	if ( "int" == type )
	{
		return "int";
	}
	else if ( "float" == type )
	{
		return "float";
	}
	else if ( "double" == type )
	{
		return "double";
	}
	else if ( "bool" == type )
	{
		return "BOOL";
	}
	else if ( "string" == type )
	{
		return "NSString*";
	}
	else if ( "callback" == type )
	{
		return "void(^)(long code, NSString* msg, NSError* error)";
	}
	else if ( "view" == type )
	{
		return "UIView*";
	}

	return type;
}
string Utils::transSwiftType( const string& type )
{
	if ( "int" == type )
	{
		return "Int";
	}
	else if ( "float" == type )
	{
		return "Float";
	}
	else if ( "double" == type )
	{
		return "Double";
	}
	else if ( "bool" == type )
	{
		return "Bool";
	}
	else if ( "string" == type )
	{
		return "String";
	}
	else if ( "callback" == type )
	{
		return " @escaping ( _ code : Int, _ msg : String?, _ err : CommError? ) -> ()";
	}
	else if ( "view" == type )
	{
		return "UIView";
	}

	return type;
}
string Utils::transJavaType( const string& type )
{
	if ( "int" == type )
	{
		return "int";
	}
	else if ( "float" == type )
	{
		return "float";
	}
	else if ( "double" == type )
	{
		return "double";
	}
	else if ( "bool" == type )
	{
		return "boolean";
	}
	else if ( "string" == type )
	{
		return "String";
	}
	else if ( "callback" == type )
	{
		return "CallbackInterface";
	}
	else if ( "view" == type )
	{
		return "View";
	}

	return type;
}

string& Utils::trim( string& s )
{
	while( !s.empty() )
	{
		if ( ' ' == s[0] || '	' == s[0]
			|| '\n' == s[0] || '\r' == s[0] )
		{
			s.erase( 0, 1 );
		}
		else
		{
			break;
		}
	}
	while( !s.empty() )
	{
		if ( ' ' == s[s.size()-1] || '	' == s[s.size()-1]
			|| '\n' == s[s.size()-1] || '\r' == s[s.size()-1] )
		{
			s.erase( s.size()-1, 1 );
		}
		else
		{
			break;
		}
	}

	return s;
}
