#include "Update.h"
#include "Table.h"
#include "Field.h"
#include "Where.h"

CUpdate::CUpdate()
: _t(NULL)
{
}
CUpdate::~CUpdate()
{
}

void CUpdate::parse( tinyxml2::XMLElement* node, CTable* t )
{
	_t = t;
	_name = ShareLib::getString( node, "name" );
	_desc = ShareLib::getString( node, "desc" );
	for ( tinyxml2::XMLElement* child = node->FirstChildElement(); NULL != child; child = child->NextSiblingElement() )
	{
		string name = child->Name();
		if ( "field" == name )
		{
			string fieldName = ShareLib::getString( child, "name" );
			CField* field = t->getFieldByName( fieldName );
			if ( NULL != field )
			{
				_vFields.push_back( field );
			}
		}
		else if ( "where" == name )
		{
			CWhere* where = new CWhere;
			where->parse( child, t );
			_vWheres.push_back(where);
		}
	}
}

void CUpdate::genHeader( ofstream& fout )
{
	fout<<"	//"<<_desc<<endl;
	fout<<"	static bool "<<_name<<"( MYSQL* mysql"<<endl;
	for ( size_t i=0; i<_vFields.size(); ++i )
	{
		CField* field = _vFields[i];
		fout<<"		, "<<field->getConst()<<field->getCType()<<field->getRef()<<" "<<field->getName()<<"	//"<<field->getDesc()<<endl;
	}
	for ( size_t i=0; i<_vWheres.size(); ++i )
	{
		CWhere* where = _vWheres[i];
		const CField* field = where->getField();

		fout<<"		, "<<field->getConst()<<field->getCType()<<field->getRef()<<" "<<field->getName()<<"_cond	//"<<field->getDesc()<<endl;
	}
	fout<<"		);"<<endl;
}
void CUpdate::genCpp( ofstream& fout )
{
	fout<<"//"<<_desc<<endl;
	fout<<"bool DbCtrl::"<<_name<<"( MYSQL* mysql"<<endl;
	for ( size_t i=0; i<_vFields.size(); ++i )
	{
		CField* field = _vFields[i];
		fout<<"	, "<<field->getConst()<<field->getCType()<<field->getRef()<<" "<<field->getName()<<"	//"<<field->getDesc()<<endl;
	}
	for ( size_t i=0; i<_vWheres.size(); ++i )
	{
		CWhere* where = _vWheres[i];
		const CField* field = where->getField();

		fout<<"	, "<<field->getConst()<<field->getCType()<<field->getRef()<<" "<<field->getName()<<"_cond	//"<<field->getDesc()<<endl;
	}
	fout<<"	)"<<endl;
	fout<<"{"<<endl;
	fout<<"	string sql = \"update `"<<_t->getName()<<"` set \";"<<endl;
	bool first = true;
	for ( size_t i=0; i<_vFields.size(); ++i )
	{
		CField* field = _vFields[i];
		string type = field->getCType();
		fout<<"	{"<<endl;
		if ( first )
		{
			first = false;
		}
		else
		{
			fout<<"		sql += \", \";"<<endl;
		}
		fout<<"		sql += \"`"<<field->getName()<<"` = \";"<<endl;
		fout<<"		sql += \"\\\'\";"<<endl;
		if ( "int" == type )
		{
			fout<<"		char p[50] = {0};"<<endl;
			fout<<"		sprintf( p, \"%d\", "<<field->getName()<<" );"<<endl;
			fout<<"		sql += p;"<<endl;
		}
		else if ( "float" == type )
		{
			fout<<"		char p[50] = {0};"<<endl;
			fout<<"		sprintf( p, \"%f\", "<<field->getName()<<" );"<<endl;
			fout<<"		sql += p;"<<endl;
		}
		else if ( "double" == type )
		{
			fout<<"		char p[50] = {0};"<<endl;
			fout<<"		sprintf( p, \"%db\", "<<field->getName()<<" );"<<endl;
			fout<<"		sql += p;"<<endl;
		}
		else
		{
			if ( field->needBinTrans() )
			{
				fout<<"		char* p = new char["<<field->getName()<<".size()*2];"<<endl;
				fout<<"		unsigned long l = mysql_real_escape_string( mysql, p, "<<field->getName()<<".data(), "<<field->getName()<<".size() );"<<endl;
				fout<<"		p[l] = 0;"<<endl;
				fout<<"		sql.append( (char*)p, l );"<<endl;
				fout<<"		delete [] p;"<<endl;
			}
			else
			{
				fout<<"		sql += "<<field->getName()<<";"<<endl;
			}
		}
		fout<<"		sql += \"\\\'\";"<<endl;
		fout<<"	}"<<endl;
	}

	first = true;
	if ( !_vWheres.empty() )
	{
		fout<<"	sql += \" where \";"<<endl;
	}
	for ( size_t i=0; i<_vWheres.size(); ++i )
	{
		if ( first )
		{
			first = false;
		}
		else
		{
			fout<<"	sql += \" and \";"<<endl;
		}
		CWhere* where = _vWheres[i];
		const CField* field = where->getField();
		fout<<"	sql += \"`"<<field->getName()<<"` \";"<<endl;
		if ( where->isLike() )
		{
			fout<<"	sql += \"like \";"<<endl;
		}
		else
		{
			fout<<"	sql += \"= \";"<<endl;
		}
		if ( "string" == field->getCType() )
		{
			fout<<"	sql += \"\\\'\";"<<endl;
			fout<<"	sql += "<<field->getName()<<"_cond;"<<endl;
			fout<<"	sql += \"\\\'\";"<<endl;
		}
		else
		{
			fout<<"	{"<<endl;
			string type = field->getCType();
			if ( "int" == type )
			{
				fout<<"		char p[50] = {0};"<<endl;
				fout<<"		sprintf( p, \"%d\", "<<field->getName()<<"_cond );"<<endl;
				fout<<"		sql += p;"<<endl;
			}
			else if ( "float" == type )
			{
				fout<<"		char p[50] = {0};"<<endl;
				fout<<"		sprintf( p, \"%f\", "<<field->getName()<<"_cond );"<<endl;
				fout<<"		sql += p;"<<endl;
			}
			else if ( "double" == type )
			{
				fout<<"		char p[50] = {0};"<<endl;
				fout<<"		sprintf( p, \"%db\", "<<field->getName()<<"_cond );"<<endl;
				fout<<"		sql += p;"<<endl;
			}
			fout<<"	}"<<endl;
		}
	}
	fout<<"	int ret = mysql_real_query( mysql, sql.data(), (unsigned int)sql.size() );"<<endl;
	
	fout<<"	return ( 0 == ret );"<<endl;
	fout<<"}"<<endl;
}

void CUpdate::genPhp( ofstream& fout )
{
	fout<<"	//"<<_desc<<endl;
	fout<<"	public static function "<<_name<<"("<<endl;
	bool first = true;
	for ( size_t i=0; i<_vFields.size(); ++i )
	{
		fout<<"		";
		if ( first )
		{
			first = false;
			fout<<" ";
		}
		else
		{
			fout<<",";
		}
		CField* field = _vFields[i];
		fout<<" $"<<field->getName()<<"	//"<<field->getDesc()<<endl;
	}
	for ( size_t i=0; i<_vWheres.size(); ++i )
	{
		fout<<"		";
		if ( first )
		{
			first = false;
			fout<<" ";
		}
		else
		{
			fout<<",";
		}
		CWhere* where = _vWheres[i];
		const CField* field = where->getField();

		fout<<" $"<<field->getName()<<"_cond	//"<<field->getDesc()<<endl;
	}
	fout<<"		)"<<endl;
	fout<<"	{"<<endl;
	fout<<"		$data = array();"<<endl;
	for ( size_t i=0; i<_vFields.size(); ++i )
	{
		CField* field = _vFields[i];
		fout<<"		$data[\'"<<field->getName()<<"\'] = $"<<field->getName()<<";"<<endl;
	}

	first = true;
	if ( !_vWheres.empty() )
	{
		fout<<"		$where = \'\';"<<endl;
	}
	for ( size_t i=0; i<_vWheres.size(); ++i )
	{
		if ( first )
		{
			first = false;
		}
		else
		{
			fout<<"		$where = $where . \' and \';"<<endl;
		}
		CWhere* where = _vWheres[i];
		const CField* field = where->getField();
		fout<<"		$where = $where . \'`"<<field->getName()<<"` \';"<<endl;
		if ( where->isLike() )
		{
			fout<<"		$where = $where . \'like \';"<<endl;
		}
		else
		{
			fout<<"		$where = $where . \'= \';"<<endl;
		}
		if ( "string" == field->getCType() )
		{
			fout<<"		$where = $where . '\\\'';"<<endl;
			fout<<"		$where = $where . $"<<field->getName()<<"_cond;"<<endl;
			fout<<"		$where = $where . '\\\'';"<<endl;
		}
		else
		{
			fout<<"		$where = $where . $"<<field->getName()<<"_cond;"<<endl;
		}
	}
	fout<<"		$d = M(\'"<<_t->getName()<<"\', \'\', \'DB_CONFIG\');"<<endl;
	if ( !_vWheres.empty() )
	{
		fout<<"		$d->where( $where )->save( $data );"<<endl;
	}
	else
	{
		fout<<"		$d->save( $data );"<<endl;
	}
	fout<<"	}"<<endl;
}

void CUpdate::genYaf( ofstream& fout )
{
	int count = 0;
	fout<<"	public static function "<<_name<<"("<<endl;
	for ( size_t i=0; i<_vFields.size(); ++i )
	{
		CField* field = _vFields[i];
		if ( field->isAuto() || field->hasValue() || field->isPrimary() )
		{
			continue;
		}
		fout<<"			";
		if ( count > 0 )
		{
			fout<<", ";
		}
		else
		{
			fout<<"  ";
		}
		fout<<"$"<<field->getName()<<endl;

		++count;
	}
	for ( size_t i=0; i<_vWheres.size(); ++i )
	{
		CWhere* where = _vWheres[i];
		vector<CWhere*> v;
		where->getLeaves(v);
		for ( size_t j=0; j<v.size(); ++j )
		{
			CWhere* where = v[j];
			fout<<"			";
			if ( count > 0 )
			{
				fout<<", ";
			}
			else
			{
				fout<<"  ";
			}
			fout<<"$"<<where->getName()<<"_"<<where->getSuffix()<<endl;

			++count;
		}
	}
	fout<<"		)"<<endl;
	fout<<"	{"<<endl;
	fout<<"		$model = self::getCopy('');"<<endl;
	fout<<"		$datetime = new \\DateTime;"<<endl;
	fout<<"		$curDateTime = $datetime->format('Y-m-d H:i:s');"<<endl;
	fout<<"		$sql = <<<sql"<<endl;
	fout<<"update tb_"<<_t->getName()<<" set "<<endl;
	fout<<" update_time = $curDateTime"<<endl;
	count = 1;
	for ( size_t i=0; i<_vFields.size(); ++i )
	{
		CField* field = _vFields[i];
		if ( field->isAuto() || field->hasValue() || field->isPrimary() )
		{
			continue;
		}
		if ( count > 0 )
		{
			fout<<", ";
		}
		else
		{
			fout<<"  ";
		}
		fout<<field->getName()<<" = ";
		if ( field->isString() )
		{
			fout<<"'";
		}
		fout<<"$"<<field->getName();
		if ( field->isString() )
		{
			fout<<"'";
		}
		fout<<endl;
	}
	if ( !_vWheres.empty() )
	{
		fout<<"where"<<endl;
	}
	for ( size_t i=0; i<_vWheres.size(); ++i )
	{
		CWhere* where = _vWheres[i];
		if ( i > 0 )
		{
			fout<<"and";
		}
		fout<<" "<<where->getYaf()<<endl;
	}
	fout<<"sql;"<<endl;
	fout<<"		var_log( $sql, 'execute sql' );"<<endl;
	fout<<"		return $model->db()->fetchAssocThenFree($model->db()->execCustom(['sql'=>$sql]));"<<endl;
	fout<<"	}"<<endl;
}
