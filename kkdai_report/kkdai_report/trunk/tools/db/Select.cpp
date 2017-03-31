#include "Select.h"
#include "Field.h"
#include "Table.h"
#include "Where.h"


CSelect::CSelect()
: _t(NULL)
{
}
CSelect::~CSelect()
{
}

void CSelect::parse( tinyxml2::XMLElement* node, CTable* t )
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
			_vFieldsName.push_back( fieldName );
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
			_vWheres.push_back( where );
		}
	}
}
	
void CSelect::genHeader( ofstream& fout )
{
	fout<<"	//"<<_desc<<endl;
	fout<<"	static bool "<<_name<<"( MYSQL* mysql, vector< tb_"<<_t->getName()<<"* >& vData"<<endl;
	for ( size_t i=0; i<_vWheres.size(); ++i )
	{
		CWhere* where = _vWheres[i];
		const CField* field = where->getField();
		fout<<"		, "<<field->getConst()<<field->getCType()<<field->getRef()<<" "<<field->getName()<<"_cond	//"<<field->getDesc()<<endl;
		fout<<"		);"<<endl;
	}
}
void CSelect::genCpp( ofstream& fout )
{
	fout<<"//"<<_desc<<endl;
	fout<<"bool DbCtrl::"<<_name<<"( MYSQL* mysql, vector< tb_"<<_t->getName()<<"* >& vData"<<endl;
	for ( size_t i=0; i<_vWheres.size(); ++i )
	{
		CWhere* where = _vWheres[i];
		const CField* field = where->getField();
		fout<<"	, "<<field->getConst()<<field->getCType()<<field->getRef()<<" "<<field->getName()<<"_cond	//"<<field->getDesc()<<endl;
		fout<<"	)"<<endl;
	}
	fout<<"{"<<endl;
	fout<<"	string sql = \"select \";"<<endl;
	if ( !_vFields.empty() )
	{
		bool first = true;
		for ( size_t i=0; i<_vFields.size(); ++i )
		{
			if ( first )
			{
				first = false;
			}
			else
			{
				fout<<"	sql += \", \";"<<endl;
			}
			CField* field = _vFields[i];
			fout<<"	sql += \"`"<<field->getName()<<"`\";"<<endl;
		}
	}
	else
	{
		fout<<"	sql += \"*\";"<<endl;
	}
	fout<<"	sql += \" from `"<<_t->getName()<<"`\";"<<endl;
	bool first = true;
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
	fout<<"	if ( 0 != ret )"<<endl;
	fout<<"	{"<<endl;
	fout<<"		return false;"<<endl;
	fout<<"	}"<<endl;
	fout<<"	MYSQL_RES* result = mysql_store_result( mysql );"<<endl;
	fout<<"	MYSQL_ROW row;"<<endl;
	fout<<"	vector< string > vFieldName;"<<endl;
	fout<<"	MYSQL_FIELD* field;"<<endl;
	fout<<"	while( NULL != (field=mysql_fetch_fields( result ) ) )"<<endl;
	fout<<"	{"<<endl;
	fout<<"		vFieldName.push_back( field->name );"<<endl;
	fout<<"	}"<<endl;
	fout<<"	while( NULL != (row=mysql_fetch_row( result ) ) )"<<endl;
	fout<<"	{"<<endl;
	fout<<"		unsigned long* len = mysql_fetch_lengths( result );"<<endl;
	fout<<"		for ( size_t i=0; i<vFieldName.size(); ++i )"<<endl;
	fout<<"		{"<<endl;
	fout<<"			string line;"<<endl;
	fout<<"			if ( NULL != row[i] )"<<endl;
	fout<<"			{"<<endl;
	fout<<"				line.append( (char*)row[i], len[i] );"<<endl;
	fout<<"			}"<<endl;
	fout<<"			else"<<endl;
	fout<<"			{"<<endl;
	fout<<"				line = \"NULL\";"<<endl;
	fout<<"			}"<<endl;
	fout<<"			tb_"<<_t->getName()<<"* data = new tb_"<<_t->getName()<<";"<<endl;
	fout<<"			string& fieldName = vFieldName[i];"<<endl;
	first = true;
	for ( size_t i=0; i<_vFields.size(); ++i )
	{
		CField* field = _vFields[i];
		if ( first )
		{
			fout<<"			if ( ";
			first = false;
		}
		else
		{
			fout<<"			else if ( ";
		}
		fout<<"\""<<field->getName()<<"\" == fieldName )"<<endl;
		fout<<"			{"<<endl;
		string cType = field->getCType();
		if ( "int" == cType )
		{
			fout<<"				data->"<<field->getName()<<" = atoi(line.data());"<<endl;
		}
		else if ( "float" == cType || "double" == cType )
		{
			fout<<"				data->"<<field->getName()<<" = atof(line.data());"<<endl;
		}
		else
		{
			fout<<"				data->"<<field->getName()<<" = line;"<<endl;
		}
		fout<<"			}"<<endl;
	}
	fout<<"			vData.push_back(data);"<<endl;
	fout<<"		}"<<endl;
	fout<<"	}"<<endl;
	fout<<"	mysql_free_result( result );"<<endl;

	fout<<endl;
	fout<<"	return true;"<<endl;
	fout<<"}"<<endl;
}
void CSelect::genPhp( ofstream& fout )
{
	fout<<"	//"<<_desc<<endl;
	fout<<"	public static function "<<_name<<"("<<endl;
	bool first = true;
	for ( size_t i=0; i<_vWheres.size(); ++i )
	{
		fout<<"		";
		if ( first )
		{
			fout<<" ";
			first = false;
		}
		else
		{
			fout<<",";
		}
		CWhere* where = _vWheres[i];
		const CField* field = where->getField();
		fout<<" $"<<field->getName()<<"_cond	//"<<field->getDesc()<<endl;
		fout<<"		)"<<endl;
	}
	fout<<"	{"<<endl;
	fout<<"		$sql = \'select \';"<<endl;
	if ( !_vFields.empty() )
	{
		first = true;
		for ( size_t i=0; i<_vFields.size(); ++i )
		{
			if ( first )
			{
				first = false;
			}
			else
			{
				fout<<"		$sql = $sql . \', \';"<<endl;
			}
			CField* field = _vFields[i];
			fout<<"		$sql = $sql . \'`"<<field->getName()<<"`\';"<<endl;
		}
	}
	else
	{
		fout<<"		$sql = $sql . \'*\';"<<endl;
	}
	fout<<"		$sql = $sql . \' from `"<<_t->getName()<<"`\';"<<endl;
	first = true;
	if ( !_vWheres.empty() )
	{
		fout<<"		$sql = $sql . \' where \';"<<endl;
	}
	for ( size_t i=0; i<_vWheres.size(); ++i )
	{
		if ( first )
		{
			first = false;
		}
		else
		{
			fout<<"		$sql = $sql . \' and \';"<<endl;
		}
		CWhere* where = _vWheres[i];
		const CField* field = where->getField();
		fout<<"		$sql = $sql . \'`"<<field->getName()<<"` \';"<<endl;
		if ( where->isLike() )
		{
			fout<<"		$sql = $sql . \'like \';"<<endl;
		}
		else
		{
			fout<<"		$sql = $sql . \'= \';"<<endl;
		}
		if ( "string" == field->getCType() )
		{
			fout<<"		$sql = $sql . \'\\\'\';"<<endl;
			fout<<"		$sql = $sql . $"<<field->getName()<<"_cond;"<<endl;
			fout<<"		$sql = $sql . \'\\\'\';"<<endl;
		}
		else
		{
			fout<<"		$sql = $sql . $"<<field->getName()<<"_cond;"<<endl;
		}
	}
	fout<<"		$d = M(\'\', \'\', \'DB_CONFIG\');"<<endl;
	fout<<"		return $d->query( $sql );"<<endl;
	fout<<"	}"<<endl;
}

void CSelect::genYaf( ofstream& fout )
{
	int count = 0;
	fout<<"	public static function "<<_name<<"("<<endl;
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
	fout<<"		$sql = <<<sql"<<endl;
	fout<<"select "<<endl;
	count = 0;
	for ( size_t i=0; i<_vFieldsName.size(); ++i )
	{
		fout<<"	";
		if ( count > 0 )
		{
			fout<<", ";
		}
		else
		{
			fout<<"  ";
		}
		fout<<_vFieldsName[i]<<endl;
	}
	fout<<"from tb_"<<_t->getName()<<endl;
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

	bool hasArray = false;
	for ( size_t i=0; i<_vFieldsName.size(); ++i )
	{
	
		CField* field = _t->getFieldByName( _vFieldsName[i] );
		if ( NULL != field && field->isArray() )
		{
			hasArray = true;
			break;
		}
	}

	if ( hasArray )
	{
		fout<<"		$data = $model->db()->fetchAssocThenFree($model->db()->execCustom(['sql'=>$sql]));"<<endl;
		fout<<"		$tmp = [];"<<endl;
		fout<<"		foreach( $data as $rs )"<<endl;
		fout<<"		{"<<endl;
		for ( size_t i=0; i<_vFieldsName.size(); ++i )
		{
			CField* field = _t->getFieldByName( _vFieldsName[i] );
			if ( NULL != field && field->isArray() )
			{
				fout<<"			$rs['"<<field->getName()<<"'] = explode( ',', $rs['"<<field->getName()<<"'] );"<<endl;
				fout<<"			array_pop( $rs['"<<field->getName()<<"'] );"<<endl;
				fout<<"			array_shift( $rs['"<<field->getName()<<"'] );"<<endl;
			}
		}
		fout<<"			$tmp[] = $rs;"<<endl;
		fout<<"		}"<<endl;
		fout<<"		return $tmp;"<<endl;
	}
	else
	{
		fout<<"		return $model->db()->fetchAssocThenFree($model->db()->execCustom(['sql'=>$sql]));"<<endl;
	}
	fout<<"	}"<<endl;
}
