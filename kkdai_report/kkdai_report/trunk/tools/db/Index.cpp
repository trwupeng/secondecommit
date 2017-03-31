#include "Index.h"
#include "Utils.h"

CIndex::CIndex()
{
}

CIndex::~CIndex()
{
}

void CIndex::parse( tinyxml2::XMLElement* node )
{
	_name = ShareLib::getString( node, "name" );
	_value = ShareLib::getString( node, "value" );
	_vValue = Utils::split( _value, "," );
}

string CIndex::get()
{
	string s = "index index_" + _name + "(";
	for ( size_t i=0; i<_vValue.size(); ++i )
	{
		if ( i > 0 )
		{
			s += ",";
		}
		s += "`";
		s += _vValue[i];
		s += "`";
	}
	s += ")";
	return s;
}
