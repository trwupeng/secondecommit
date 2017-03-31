#include "EnumData.h"

CEnumData::CEnumData()
{
}
CEnumData::~CEnumData()
{
}

void CEnumData::parse( tinyxml2::XMLElement* node )
{
	_name = Utils::getXmlAttrStr( node, "name" );
	Utils::trim( _name );
	_type = Utils::getXmlAttrStr( node, "type" );
	_value = Utils::getXmlAttrStr( node, "value" );
	_desc = Utils::getXmlAttrStr( node, "desc" );
}

void CEnumData::genForOc( ofstream& fout, const string& prefix )
{
	if ( "string" == _type )
	{
		fout<<prefix<<"static NSString* "<<_name<<" = @\""<<_value<<"\";";
	}
	else
	{
		fout<<prefix<<_name<<" = "<<_value<<",";
	}
	fout<<"	//"<<_desc<<endl;
}

void CEnumData::genForSwift( ofstream& fout )
{
	fout<<"	case "<<_name<<" = ";
	if ( "string" == _type )
	{
		fout<<"\""<<_value<<"\"";
	}
	else
	{
		fout<<_value;
	}
	fout<<"	//"<<_desc<<endl;
}

void CEnumData::genForJava( ofstream& fout )
{
	fout<<"		";
	if ( "string" == _type )
	{
		fout<<_name<<"(\""<<_value<<"\")";
	}
	else
	{
		fout<<_name<<"("<<_value<<")";
	}
}
