#include "Where.h"
#include "Table.h"
#include "Field.h"

CWhere::CWhere()
: _field(NULL)
, _parent(NULL)
{
}
CWhere::~CWhere()
{
}

void CWhere::parse( tinyxml2::XMLElement* node, CTable* t, CWhere* parent )
{
	_parent = parent;
	_type = ShareLib::getString( node, "type" );
	_name = ShareLib::getString( node, "name" );
	if ( _type.empty() || "default" == _type )
	{
		_field = t->getFieldByName( _name );
		_comp = ShareLib::getString( node, "comp" );
		_suffix = ShareLib::getString( node, "suffix", "cond" );
		for ( CWhere* p = _parent; NULL != p; p=p->getParent() )
		{
			_suffix += ( "_" + p->getName() );
		}
		assert( NULL != _field );
	}
	else
	{
		for ( tinyxml2::XMLElement* child = node->FirstChildElement(); NULL != child; child = child->NextSiblingElement() )
		{
			CWhere* where = new CWhere;
			where->parse( child, t, this );
			_vChildren.push_back( where );
		}
	}
}

string CWhere::getYaf( int tabNum )
{
	string prefix;
	for ( int i=0; i<tabNum; ++i )
	{
		prefix += "	";
	}
	string ret;
	if ( _type.empty() || "default" == _type )
	{
		ret = prefix;
		//实际的where语句
		if ( _comp.empty() )
		{
			if ( _field->isString() )
			{
				ret += ( "( " + _name + " = '$" + _name + "_" + _suffix + "'" + " )" );
			}
			else
			{
				ret += ("( " + _name + " = $" + _name + "_" + _suffix + " )" );
			}
		}
		else
		{
			if ( _field->isString() )
			{
				ret += ( "( " + _name + " " + _comp + " '$" + _name + "_" + _suffix + "'" + " )" );
			}
			else
			{
				ret += ( "( " + _name + " " + _comp + " $" + _name + "_" + _suffix + " )" );
			}
		}
		ret += "\n";
	}
	else
	{
		ret += prefix;
		ret += "(\n";
		for ( size_t i=0; i<_vChildren.size(); ++i )
		{
			CWhere* where = _vChildren[i];
			if ( i > 0 )
			{
				ret += ( prefix + " " + _type + " \n" );
			}
			ret += where->getYaf( tabNum+1 );
		}
		ret += prefix += ")\n";
	}
	return ret;
}

void CWhere::getLeaves( vector<CWhere*>& v )
{
	if ( _type.empty() || "default" == _type )
	{
		v.push_back(this);
	}
	else
	{
		for ( size_t i=0; i<_vChildren.size(); ++i )
		{
			_vChildren[i]->getLeaves(v);
		}
	}
}
