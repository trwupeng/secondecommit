#include "Delete.h"
#include "Table.h"
#include "Where.h"
#include "Field.h"


CDelete::CDelete()
: _t(NULL)
{
}
CDelete::~CDelete()
{
}

void CDelete::parse( tinyxml2::XMLElement* node, CTable* t )
{
	_t = t;
	_name = ShareLib::getString( node, "name" );
	_desc = ShareLib::getString( node, "desc" );
	for ( tinyxml2::XMLElement* child = node->FirstChildElement(); NULL != child; child = child->NextSiblingElement() )
	{
		string name = child->Name();
		if ( "where" == name )
		{
			CWhere* where = new CWhere;
			where->parse( child, t );
			_vWheres.push_back(where);
		}
	}
}
void CDelete::genHeader( ofstream& fout )
{
	fout<<"	//"<<_desc<<endl;
	fout<<"	static bool "<<_name<<"( MYSQL* mysql"<<endl;
	for ( size_t i=0; i<_vWheres.size(); ++i )
	{
		CWhere* where = _vWheres[i];
		const CField* field = where->getField();
		fout<<"		, "<<field->getConst()<<field->getCType()<<field->getRef()<<" "<<field->getName()<<"_cond	//"<<field->getDesc()<<endl;
		fout<<"		);"<<endl;
	}
}
void CDelete::genCpp( ofstream& fout )
{
	fout<<"//"<<_desc<<endl;
	fout<<"bool DbCtrl::"<<_name<<"( MYSQL* mysql"<<endl;
	for ( size_t i=0; i<_vWheres.size(); ++i )
	{
		CWhere* where = _vWheres[i];
		const CField* field = where->getField();
		fout<<"	, "<<field->getConst()<<field->getCType()<<field->getRef()<<" "<<field->getName()<<"_cond	//"<<field->getDesc()<<endl;
		fout<<"	)"<<endl;
	}
	fout<<"{"<<endl;
	fout<<"	string sql = \"delete from `"<<_t->getName()<<"`\";"<<endl;
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
	fout<<"	int ret = mysql_query( mysql, sql.data() );"<<endl;
	fout<<"	return ( 0 == ret );"<<endl;
	fout<<"}"<<endl;
}

void CDelete::genPhp( ofstream& fout )
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
	first = true;
	if ( !_vWheres.empty() )
	{
		fout<<"		$sql = \'\';"<<endl;
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

	fout<<"		$d = M(\'"<<_t->getName()<<"\', \'\', \'DB_CONFIG\');"<<endl;
	if ( !_vWheres.empty() )
	{
		fout<<"		$d->where( $sql )->delete();"<<endl;
	}
	fout<<"	}"<<endl;
}
