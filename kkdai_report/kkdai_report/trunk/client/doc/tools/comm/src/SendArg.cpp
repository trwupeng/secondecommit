#include "SendArg.h"

CSendArg::CSendArg()
: _null(false)
, _real(true)
{
}
CSendArg::~CSendArg()
{
}

CSendArg* CSendArg::create( const string& name, const string& type, const string& desc, const string& value, bool null )
{
	CSendArg* p = new CSendArg;
	p->_name = name;
	p->_type = type;
	p->_value = value;
	p->_desc = desc;
	p->_null = null;
	p->_real = false;

	return p;
}

void CSendArg::parse( tinyxml2::XMLElement* node )
{
	_name = Utils::getXmlAttrStr( node, "name" );
	Utils::trim( _name );
	_type = Utils::getXmlAttrStr( node, "type" );
	_value = Utils::getXmlAttrStr( node, "value" );
	_desc = Utils::getXmlAttrStr( node, "desc" );
	_null = Utils::getXmlAttrBool( node, "null" );
}
