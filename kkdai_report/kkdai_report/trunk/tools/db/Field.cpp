#include "Field.h"

CField::CField()
: _size(0)
, _auto(false)
, _primary(false)
, _null(true)
, _bin(false)
, _unique(false)
, _zeroFill(false)
, _array(false)
, _needBinTrans(false)
{
}
CField::~CField()
{
}

string CField::get()
{
	string s = "`";
	s += getName();
	s += "` ";
	s += getRealDbType();
	if ( _size > 0 )
	{
		char szTemp[50] = {0};
		sprintf( szTemp, "%u", _size );
		s += "(" + string(szTemp) + ")";
	}

	if ( !isNull() )
	{
		s += " not null";
	}

	if ( isUnique() )
	{
		s += " unique";
	}

	if ( isZeroFill() )
	{
		if ( "int" == _type || "float" == _type || "double" == _type )
		{
			s += " zerofill";
		}
	}

	if ( isBin() )
	{
		if ( "varchar" == _realDbType )
		{
			s += " binary";
		}
	}

	if ( !getValue().empty() )
	{
		if ( string::npos == _realDbType.find( "blob" )
			&& string::npos == _realDbType.find( "text" ) )
		{
			s += " default \'" + getValue() + "\'";
		}
	}

	if ( isAuto() )
	{
		s += " auto_increment";
	}

	s += " comment '" + getDesc() + " '";

	return s;
}

bool CField::isString()
{
	return !("int" == _type || "float" == _type || "double" == _type);
}

void CField::parse( tinyxml2::XMLElement* node )
{
	_name = ShareLib::getString( node, "name" );
	if ( _name.empty() )
	{
		cout<<"name can not empty"<<endl;
		return ;
	}

	_type = ShareLib::getString( node, "type" );
	if ( _type.empty() )
	{
		cout<<"type can not empty"<<endl;
		return ;
	}
	_size = ShareLib::getInt( node, "size" );
	
	if ( "true" == ShareLib::getString( node, "auto" ) )
	{
		_auto = true;
	}

	if ( "true" == ShareLib::getString( node, "primary" ) )
	{
		_primary = true;
	}

	if ( "false" == ShareLib::getString( node, "null" ) )
	{
		_null = false;
	}

	if ( "true" == ShareLib::getString( node, "bin" ) )
	{
		_bin = true;
	}

	_value = ShareLib::getString( node, "value" );

	if ( "true" == ShareLib::getString( node, "unique" ) )
	{
		_unique = true;
	}

	if ( "true" == ShareLib::getString( node, "zerofill" ) )
	{
		_zeroFill = true;
	}

	if ( "true" == ShareLib::getString( node, "array" ) )
	{
		_array = true;
	}

	_desc = ShareLib::getString( node, "desc" );

	switchType();
}

void CField::switchType()
{
	if ( "int" == _type )
	{
		switch(_size)
		{
		case 1:
			_realDbType = "tinyint";
			break;

		case 2:
			_realDbType = "smallint";
			break;
			
		case 3:
			_realDbType = "mediumint";
			break;

		case 8:
			_realDbType = "bigint";
			break;

		default:
			_realDbType = "int";
			break;
		}
		_cType = "int";
		_size = 0;
		if ( isNull() )
		{
			_default = "0";
			_defaultPhp = "0";
		}
	}
	else if ( "varchar" == _type )
	{
		if ( _size <= 65535 )
		{
			_realDbType = "varchar";
		}
		else
		{
			_realDbType = "longtext";
			_size = 0;
		}
		_cType = "string";
		_const = "const ";
		_ref = "&";
		if ( isNull() )
		{
			_default = "\"\"";
			_defaultPhp = "\'\'";
		}
	}
	else if ( "text" == _type )
	{
		if ( _size <= 255 )
		{
			_realDbType = "tinytext";
		}
		else if ( _size <= 65535 )
		{
			_realDbType = "text";
		}
		else if ( _size <= 16777215 )
		{
			_realDbType = "mediumtext";
		}
		else
		{
			_realDbType = "longtext";
		}
		_cType = "string";
		_const = "const ";
		_ref = "&";
		if ( isNull() )
		{
			_default = "\"\"";
			_defaultPhp = "\'\'";
		}
	}
	else if ( "blob" == _type )
	{
		if ( _size <= 255 )
		{
			_realDbType = "tinyblob";
		}
		else if ( _size <= 65535 )
		{
			_realDbType = "blob";
		}
		else if ( _size <= 16777215 )
		{
			_realDbType = "mediumblob";
		}
		else
		{
			_realDbType = "longblob";
		}
		_cType = "string";
		_const = "const ";
		_ref = "&";
		if ( isNull() )
		{
			_default = "\"\"";
			_defaultPhp = "\'\'";
		}
		_needBinTrans = true;
	}
	else if ( "bin" == _type )
	{
		_realDbType = "varbinary";
		_cType = "string";
		_const = "const ";
		_ref = "&";
		if ( isNull() )
		{
			_default = "\"\"";
			_defaultPhp = "\'\'";
		}
		_needBinTrans = true;
	}
	else if ( "enum" == _type )
	{
		_realDbType = _type;
		_cType = "string";
		_const = "const ";
		_ref = "&";
		if ( isNull() )
		{
			_default = "\"\"";
			_defaultPhp = "\'\'";
		}
	}
	else
	{
		_realDbType = _type;
		_cType = _type;
		if ( "float" == _type || "double" == _type )
		{
			if ( isNull() )
			{
				_default = "0.0";
				_defaultPhp = "0.0";
			}
		}
	}
}

string CField::getPhpMap( bool needDefault )
{
	string s = "'";
	s += getName();
	s += "' => ";
	if ( needDefault && hasValue() )
	{
		s += "'";
		s += getValue();
		s += "'";
	}
	else
	{
		if ( isArray() )
		{
			s += "'__begin_flag__,' . ";
			s += "implode( ',', $";
			s += getName();
			s += " ) . ";
			s += "',__end_flag__'";
		}	
		else
		{
			s += "$";
			s += getName();
		}
	}

	return s;
}
