#include "Table.h"
#include "Field.h"
#include "Index.h"
#include "Update.h"
#include "Select.h"
#include "Delete.h"


CTable::CTable()
{
}
CTable::~CTable()
{
}

void CTable::parse( tinyxml2::XMLElement* node )
{
	_name = ShareLib::getString( node, "name" );
	_desc = ShareLib::getString( node, "desc" );
	for ( tinyxml2::XMLElement* child = node->FirstChildElement();
			NULL != child; child = child->NextSiblingElement() )
	{
		string name = child->Name();
		if ( "field" == name )
		{
			CField* p = new CField;
			p->parse( child );
			_vFields.push_back(p);
		}
		else if ( "index" == name )
		{
			CIndex* p = new CIndex;
			p->parse( child );
			_vIndexes.push_back(p);
		}
		else if ( "update" == name )
		{
			CUpdate* p = new CUpdate;
			p->parse( child, this );
			_vUpdates.push_back(p);
		}
		else if ( "select" == name )
		{
			CSelect* p = new CSelect;
			p->parse( child, this );
			_vSelects.push_back(p);
		}
		else if ( "delete" == name )
		{
			CDelete* p = new CDelete;
			p->parse( child, this );
			_vDeletes.push_back(p);
		}
	}
}

string CTable::get()
{
	CField* primary = NULL;
	string s = "create table if not exists `tb_" + _name + "` (";
	
	for ( size_t i=0; i<_vFields.size(); ++i )
	{
		CField* p = _vFields[i];
		if ( i > 0 )
		{
			s += ", ";
		}
		s += p->get();
		if ( NULL == primary && p->isPrimary() )
		{
			primary = p;
		}
	}

	s += ", `iRecordVerID` bigint";
	s += ", `create_time` datetime";
	s += ", `update_time` datetime";
	s += ", `del` tinyint";

	if ( NULL != primary )
	{
		s += ", ";
		s += "primary key(`" + primary->getName() + "`)";
	}

	for ( size_t i=0; i<_vIndexes.size(); ++i )
	{
		CIndex* p = _vIndexes[i];
		s += ", ";
		s += p->get();
	}

	s += " );";

	return s;
}

void CTable::genStruct( ofstream& fout )
{
	fout<<"//"<<_desc<<endl;
	fout<<"struct tb_"<<_name<<endl;
	fout<<"{"<<endl;
	for ( size_t i=0; i<_vFields.size(); ++i )
	{
		CField* field = _vFields[i];
		fout<<"	"<<field->getCType()<<" "<<field->getName()<<";	//"<<field->getDesc()<<endl;
	}
	fout<<"};"<<endl;
}

void CTable::genHeader( ofstream& fout )
{
	fout<<"	static bool create_"<<_name<<"( MYSQL* mysql );"<<endl;
	fout<<"	static bool insert_"<<_name<<"( MYSQL* mysql"<<endl;
	for ( size_t i=0; i<_vFields.size(); ++i )
	{
		CField* field = _vFields[i];
		if ( !field->isAuto() )
		{
			fout<<"		, "<<field->getConst()<<field->getCType()<<field->getRef()<<" "<<field->getName();
			if ( field->isNull() )
			{
				fout<<" = "<<field->getDefault();
			}
			fout<<"	//"<<field->getDesc()<<endl;
		}
	}
	fout<<"		);"<<endl;
	fout<<"	static bool get_"<<_name<<"( MYSQL* mysql, vector< tb_"<<_name<<"* >& vData );"<<endl;
	for ( size_t i=0; i<_vUpdates.size(); ++i )
	{
		_vUpdates[i]->genHeader( fout );
	}
	for ( size_t i=0; i<_vSelects.size(); ++i )
	{
		_vSelects[i]->genHeader( fout );
	}
	for ( size_t i=0; i<_vDeletes.size(); ++i )
	{
		_vDeletes[i]->genHeader( fout );
	}
	fout<<endl;
}

void CTable::genCpp( ofstream& fout )
{
	fout<<"bool DbCtrl::create_"<<_name<<"( MYSQL* mysql )"<<endl;
	fout<<"{"<<endl;
	fout<<"	assert( NULL != mysql );"<<endl;
	fout<<"	int ret = mysql_query( mysql, \""<<get()<<"\" );"<<endl;
	fout<<"	return ( 0 == ret );"<<endl;
	fout<<"}"<<endl;
	fout<<"bool DbCtrl::insert_"<<_name<<"( MYSQL* mysql"<<endl;
	
	for ( size_t i=0; i<_vFields.size(); ++i )
	{
		CField* field = _vFields[i];
		if ( !field->isAuto() )
		{
			fout<<"	, "<<field->getConst()<<field->getCType()<<field->getRef()<<" "<<field->getName()<<"	//"<<field->getDesc()<<endl;
		}
	}
	fout<<"	)"<<endl;
	fout<<"{"<<endl;
	fout<<"	string sql = \"insert into `"<<_name<<"` (";
	bool first = true;
	for ( size_t i=0; i<_vFields.size(); ++i )
	{
		CField* field = _vFields[i];
		if ( field->isAuto() )
		{
			continue;
		}
		if ( !first )
		{
			fout<<", ";
		}
		first = false;
		fout<<"`"<<field->getName()<<"`";
	}
	fout<<") values (\";"<<endl;

	first = true;
	for ( size_t i=0; i<_vFields.size(); ++i )
	{
		CField* field = _vFields[i];
		if ( field->isAuto() )
		{
			continue;
		}
		if ( !first )
		{
			fout<<"	sql += \", \";"<<endl;
		}
		first = false;
		if ( field->needBinTrans() )
		{
			fout<<"	if ( !"<<field->getName()<<".empty() )"<<endl;
			fout<<"	{"<<endl;
			fout<<"		sql += \"\\\'\";"<<endl;
			fout<<"		char* p = new char["<<field->getName()<<".size()*2];"<<endl;
			fout<<"		unsigned long l = mysql_real_escape_string( mysql, p, "<<field->getName()<<".data(), "<<field->getName()<<".size() );"<<endl;
			fout<<"		p[l] = 0;"<<endl;
			fout<<"		sql.append( (char*)p, l );"<<endl;
			fout<<"		delete [] p;"<<endl;
			fout<<"		sql += \"\\\'\";"<<endl;
			fout<<"	}"<<endl;
		}
		else
		{
			if ( "string" == field->getCType() )
			{
				fout<<"	sql += \"\\\'\";"<<endl;
				fout<<"	sql += "<<field->getName()<<";"<<endl;
				fout<<"	sql += \"\\\'\";"<<endl;
			}
			else
			{
				fout<<"	{"<<endl;
				fout<<"		char temp[100] = {0};"<<endl;
				if ( "int" == field->getCType() )
				{
					fout<<"		sprintf( temp, \"%d\", "<<field->getName()<<" );"<<endl;
				}
				else if ( "float" == field->getCType() )
				{
					fout<<"		sprintf( temp, \"%d\", "<<field->getName()<<" );"<<endl;
				}
				else if ( "double" == field->getCType() )
				{
					fout<<"		sprintf( temp, \"%db\", "<<field->getName()<<" );"<<endl;
				}
				fout<<"		sql += temp;"<<endl;
				fout<<"	}"<<endl;
			}
		}
	}

	fout<<"	sql += \")\";"<<endl;
	fout<<"	int ret = mysql_real_query( mysql, sql.data(), (unsigned int)sql.size() );"<<endl;
	fout<<"	return ( 0 == ret );"<<endl;
	fout<<"}"<<endl;
	fout<<"bool DbCtrl::get_"<<_name<<"( MYSQL* mysql, vector< tb_"<<_name<<"* >& vData )"<<endl;
	fout<<"{"<<endl;
	fout<<"	string sql = \"select * from `"<<_name<<"`\";"<<endl;
	fout<<"	int ret = mysql_query( mysql, sql.data() );"<<endl;
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
	fout<<"			tb_"<<_name<<"* data = new tb_"<<_name<<";"<<endl;
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

	for ( size_t i=0; i<_vUpdates.size(); ++i )
	{
		CUpdate* update = _vUpdates[i];
		update->genCpp( fout );
	}
	for ( size_t i=0; i<_vSelects.size(); ++i )
	{
		CSelect* select = _vSelects[i];
		select->genCpp( fout );
	}
	for ( size_t i=0; i<_vDeletes.size(); ++i )
	{
		_vDeletes[i]->genCpp( fout );
	}
}

CField* CTable::getFieldByName( const string& name )
{
	for ( size_t i=0; i<_vFields.size(); ++i )
	{
		CField* field = _vFields[i];
		if ( name == field->getName() )
		{
			return field;
		}
	}

	return NULL;
}

void CTable::genPhp( ofstream& fout )
{
	genCreatePhp( fout );
	genInsertPhp( fout );
	genSelectPhp( fout );
	for ( size_t i=0; i<_vUpdates.size(); ++i )
	{
		_vUpdates[i]->genPhp( fout );
	}
	for ( size_t i=0; i<_vSelects.size(); ++i )
	{
		_vSelects[i]->genPhp( fout );
	}
	for ( size_t i=0; i<_vDeletes.size(); ++i )
	{
		_vDeletes[i]->genPhp( fout );
	}
}
void CTable::genCreatePhp( ofstream& fout )
{
	fout<<"	//"<<_desc<<endl;
	fout<<"	public static function create_"<<_name<<"()"<<endl;
	fout<<"	{"<<endl;
	fout<<"		$d = M(\'\', \'\', \'DB_CONFIG\');"<<endl;
	fout<<"		$d->query( \'"<<get()<<"\' );"<<endl;
	fout<<"	}"<<endl;
}
void CTable::genInsertPhp( ofstream& fout )
{
	fout<<"	//"<<_desc<<endl;
	fout<<"	public static function insert_"<<_name<<"( "<<endl;
	bool first = true;
	for ( size_t i=0; i<_vFields.size(); ++i )
	{
		CField* field = _vFields[i];
		if ( !field->isAuto() )
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
			fout<<" $"<<field->getName();
			if ( field->isNull() )
			{
				fout<<" = "<<field->getDefaultPhp();
			}
			fout<<"	//"<<field->getDesc()<<endl;
		}
	}
	fout<<"		)"<<endl;
	fout<<"	{"<<endl;
	fout<<"		$data = array();"<<endl;
	for ( size_t i=0; i<_vFields.size(); ++i )
	{
		CField* field = _vFields[i];
		if ( field->isAuto() )
		{
			continue;
		}
		if ( field->needBinTrans() )
		{
			fout<<"		$data[\'"<<field->getName()<<"\'] = bin2hex($"<<field->getName()<<");"<<endl;
		}
		else
		{
			fout<<"		$data[\'"<<field->getName()<<"\'] = $"<<field->getName()<<";"<<endl;
		}
	}
	fout<<"		$d = M(\'"<<_name<<"\', \'\', \'DB_CONFIG\');"<<endl;
	fout<<"		$d->add($data);"<<endl;

	fout<<"	}"<<endl;
}
void CTable::genSelectPhp( ofstream& fout )
{
	fout<<"	//"<<_desc<<endl;
	fout<<"	public static function get_"<<_name<<"()"<<endl;
	fout<<"	{"<<endl;
	fout<<"		$d = M(\'"<<_name<<"\', \'\', \'DB_CONFIG\');"<<endl;
	fout<<"		return $d->query( \'select * from `"<<_name<<"`\' );"<<endl;	
	fout<<"	}"<<endl;
}
void CTable::genCreateSql( ofstream& fout )
{
	fout<<get()<<endl;
}

void CTable::genYaf()
{
	string className = _name + "Data";
	if ( className[0] >= 'a' && className[0] <= 'z' )
	{
		className[0] -= ( 'a'-'A');
	}
	string fileName = className + ".php";

	ofstream fout( fileName.data() );
	fout<<"<?php"<<endl;
	fout<<"//File Name:"<<fileName<<endl;
	fout<<"//This is is generated by dic2db. Don't modify it!"<<endl;
	fout<<endl;

	fout<<"namespace Prj\\Data;"<<endl;
	fout<<endl;

	CField* primary = getPrimary();
	assert( NULL != primary );

	fout<<"class "<<className<<" extends \\Sooh\\DB\\Base\\KVObj"<<endl;
	fout<<"{"<<endl;
	fout<<endl;

	fout<<"	public static function add("<<endl;
	int count = 0;
	for ( size_t i=0; i<_vFields.size(); ++i )
	{
		CField* field = _vFields[i];
		if ( field->isAuto() )
		{
			continue;
		}
		if ( field->hasValue() )
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
		fout<<"$"<<_vFields[i]->getName()<<endl;

		++count;
	}
	fout<<"		)"<<endl;
	fout<<"	{"<<endl;
	fout<<"		$datetime = new \\DateTime;"<<endl;
	fout<<"		$curDateTime = $datetime->format('Y-m-d H:i:s');"<<endl;
	fout<<"		$data = ["<<endl;
	count = 0;
	for ( size_t i=0; i<_vFields.size(); ++i )
	{
		CField* field = _vFields[i];
		if ( field->isAuto() )
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
		fout<<field->getPhpMap( true )<<endl;

		++count;
	}
	fout<<"			, 'iRecordVerID' => '1'"<<endl;
	fout<<"			, 'create_time' => $curDateTime"<<endl;
	fout<<"			, 'update_time' => $curDateTime"<<endl;
	fout<<"			, 'del' => 0"<<endl;
	fout<<"			];"<<endl;
	fout<<"		$model = self::getCopy('');"<<endl;
	fout<<"		$model->db()->addRecord( $model->tbname(), $data );"<<endl;
	fout<<"	}"<<endl;
	fout<<endl;

	fout<<"	public static function upd("<<endl;
	fout<<"			  $"<<primary->getName()<<endl;
	for ( size_t i=0; i<_vFields.size(); ++i )
	{
		CField* field = _vFields[i];
		if ( field->isAuto() || field->isPrimary() || field->hasValue() )
		{
			continue;
		}
		fout<<"			, $"<<field->getName()<<endl;
	}
	fout<<"		)"<<endl;
	fout<<"	{"<<endl;
	fout<<"		$data = ["<<endl;
	count = 0;
	for ( size_t i=0; i<_vFields.size(); ++i )
	{
		CField* field = _vFields[i];
		if ( field->isAuto() || field->isPrimary() || field->hasValue() )
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
		fout<<field->getPhpMap( false )<<endl;

		++count;
	}
	fout<<"			];"<<endl;
	fout<<"		$model = self::getCopy( ['"<<primary->getName()<<"' => $"<<primary->getName()<<"] );"<<endl;
	fout<<"		$model->load();"<<endl;
	fout<<"		if ( $model->exists() )"<<endl;
	fout<<"		{"<<endl;
	fout<<"			foreach( $data as $k => $v )"<<endl;
	fout<<"			{"<<endl;
	fout<<"				$model->setField( $k, $v );"<<endl;
	fout<<"			}"<<endl;
	fout<<"			$datetime = new \\DateTime;"<<endl;
	fout<<"			$curDateTime = $datetime->format('Y-m-d H:i:s');"<<endl;
	fout<<"			$model->setField('update_time', $curDateTime);"<<endl;
	fout<<"			$model->update();"<<endl;
	fout<<"		}"<<endl;
	fout<<"		else"<<endl;
	fout<<"		{"<<endl;
	fout<<"			return false;"<<endl;
	fout<<"		}"<<endl;
	fout<<"	}"<<endl;
	fout<<endl;

	fout<<"	public static function get( $"<<primary->getName()<<" )"<<endl;
	fout<<"	{"<<endl;
	fout<<"		$model = self::getCopy('');"<<endl;
	
	bool hasArray = false;
	for ( size_t i=0; i<_vFields.size(); ++i )
	{
		if ( _vFields[i]->isArray() )
		{
			hasArray = true;
			break;
		}
	}
	if ( hasArray )
	{
		fout<<"		$rs = $model->db()->getRecord( $model->tbname(), '*', ['del'=>0, '"<<primary->getName()<<"'=>$"<<primary->getName()<<"] );"<<endl;
		for ( size_t i=0; i<_vFields.size(); ++i )
		{
			CField* field = _vFields[i];
			if ( field->isArray() )
			{
				fout<<"		$rs['"<<field->getName()<<"'] = explode( ',', $rs['"<<field->getName()<<"'] );"<<endl;
				fout<<"		array_pop( $rs['"<<field->getName()<<"'] );"<<endl;
				fout<<"		array_shift( $rs['"<<field->getName()<<"'] );"<<endl;
			}
		}
		fout<<"		return $rs;"<<endl;
	}
	else
	{
		fout<<"		return $model->db()->getRecord( $model->tbname(), '*', ['del'=>0, '"<<primary->getName()<<"'=>$"<<primary->getName()<<"] );"<<endl;
	}
	fout<<"	}"<<endl;
	fout<<endl;

	fout<<"	public static function getAll()"<<endl;
	fout<<"	{"<<endl;
	fout<<"		$model = self::getCopy('');"<<endl;

	if ( hasArray )
	{
		fout<<"		$data = $model->db()->getRecords( $model->tbname(), '*', ['del'=>0] );"<<endl;
		fout<<"		$tmp = [];"<<endl;
		fout<<"		foreach( $data as $rs )"<<endl;
		fout<<"		{"<<endl;
		for ( size_t i=0; i<_vFields.size(); ++i )
		{
			CField* field = _vFields[i];
			if ( field->isArray() )
			{
				fout<<"			$rs['"<<field->getName()<<"'] = explode( ',', $rs['"<<field->getName()<<"'] );"<<endl;
				fout<<"			array_pop( $rs['"<<field->getName()<<"'] );"<<endl;
				fout<<"			array_shift( $rs['"<<field->getName()<<"'] );"<<endl;
			}
		}
		fout<<"			$tmp[] = $rs;"<<endl;
	}
	else
	{
		fout<<"		return $model->db()->getRecords( $model->tbname(), '*', ['del'=>0] );"<<endl;
	}
	fout<<"		}"<<endl;
	fout<<"		return $tmp;"<<endl;
	fout<<"	}"<<endl;
	fout<<endl;

	fout<<"	public static function del( $"<<primary->getName()<<" )"<<endl;
	fout<<"	{"<<endl;
	fout<<"		$model = self::getCopy(['"<<primary->getName()<<"' => $"<<primary->getName()<<"]);"<<endl;
	fout<<"		$model->load();"<<endl;
	fout<<"		if ( $model->exists() )"<<endl;
	fout<<"		{"<<endl;
	fout<<"			$model->setField( 'del', 1 );"<<endl;
	fout<<"			$datetime = new \\DateTime;"<<endl;
	fout<<"			$curDateTime = $datetime->format('Y-m-d H:i:s');"<<endl;
	fout<<"			$model->setField('update_time', $curDateTime);"<<endl;
	fout<<"			$model->update();"<<endl;
	fout<<"			return true;"<<endl;
	fout<<"		}"<<endl;
	fout<<"		else"<<endl;
	fout<<"		{"<<endl;
	fout<<"			return false;"<<endl;
	fout<<"		}"<<endl;
	fout<<"	}"<<endl;
	fout<<endl;

	fout<<"	public static function getCount()"<<endl;
	fout<<"	{"<<endl;
	fout<<"		$model = self::getCopy('');"<<endl;
	fout<<"		$sql = <<<sql"<<endl;
	fout<<"select count(*) as count from tb_"<<_name<<endl;
	fout<<"sql;"<<endl;
	fout<<"		var_log( $sql, 'execute sql' );"<<endl;
	fout<<"     $rs = $model->db()->fetchAssocThenFree($model->db()->execCustom(['sql'=>$sql]));"<<endl;
	fout<<"		return (int)$rs[0]['count'];"<<endl;
	fout<<"	}"<<endl;
	fout<<endl;

	for ( size_t i=0; i<_vUpdates.size(); ++i )
	{
		_vUpdates[i]->genYaf( fout );
	}

	for ( size_t i=0; i<_vSelects.size(); ++i )
	{
		_vSelects[i]->genYaf( fout );
	}

	fout<<"	protected static function splitedTbName($n,$isCache)"<<endl;
	fout<<"	{"<<endl;
	fout<<"		return 'tb_"<<_name<<"';"<<endl;
	fout<<"	}"<<endl;
	fout<<"}"<<endl;
}

CField* CTable::getPrimary()
{
	for ( size_t i=0; i<_vFields.size(); ++i )
	{
		if ( _vFields[i]->isPrimary() )
		{
			return _vFields[i];
		}
	}

	return NULL;
}
