#include "Enum.h"
#include "EnumData.h"


CEnum::CEnum()
: _enum(true)
{
}
CEnum::~CEnum()
{
}

void CEnum::parse( tinyxml2::XMLElement* node )
{
	_name = Utils::getXmlAttrStr( node, "name" );
	Utils::trim( _name );
	_desc = Utils::getXmlAttrStr( node, "desc" );

	for ( tinyxml2::XMLElement* child = node->FirstChildElement(); NULL != child; child = child->NextSiblingElement() )
	{
		CEnumData* p = new CEnumData;
		p->parse( child );
		if ( "string" == p->getType() )
		{
			_enum = false;
		}
		const string& type = p->getType();
		_swiftType = Utils::transSwiftType(type);
		_javaType = Utils::transJavaType(type);
		_vData.push_back(p);
	}
}

void CEnum::genForOc( ofstream& fout )
{
	fout<<"//"<<_desc<<endl;
	string prefix;
	if ( _enum )
	{
		prefix = "	";
		fout<<"enum "<<_name<<endl;
		fout<<"{"<<endl;
	}

	for ( size_t i = 0; i<_vData.size(); ++i )
	{
		_vData[i]->genForOc( fout, prefix );
	}

	if ( _enum )
	{
		fout<<"	__"<<_name<<"_END"<<endl;
		fout<<"};"<<endl;
	}
	fout<<endl;
}

void CEnum::genForSwift( ofstream& fout )
{
	fout<<"//"<<_desc<<endl;
	fout<<"enum "<<_name<<" : "<<_swiftType<<endl;
	fout<<"{"<<endl;
	for ( size_t i=0; i<_vData.size(); ++i )
	{
		_vData[i]->genForSwift( fout );
	}
	fout<<"}"<<endl;

	fout<<endl;
}

void CEnum::genForJava( ofstream& fout )
{
	fout<<"	//"<<_desc<<endl;
	fout<<"	public enum "<<_name<<" {"<<endl;
	for ( size_t i=0;i<_vData.size(); ++i )
	{
		_vData[i]->genForJava( fout );
		if ( (i+1) < _vData.size() )
		{
			fout<<",";
		}
		else
		{
			fout<<";";
		}
		fout<<"	//"<<_vData[i]->getDesc()<<endl;
	}
	fout<<endl;

	fout<<"		private "<<_javaType<<" __data;"<<endl;
	fout<<"		private "<<_name<<"( "<<_javaType<<" d ) {"<<endl;
	fout<<"			__data = d;"<<endl;
	fout<<"		}"<<endl;
	fout<<"		public String toString() {"<<endl;
	fout<<"			return \"\" + __data;"<<endl;
	fout<<"		}"<<endl;
	fout<<"	}"<<endl;
	fout<<endl;
}
