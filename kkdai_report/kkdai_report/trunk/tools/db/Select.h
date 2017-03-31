#ifndef _SELECT_H_
#define _SELECT_H_
#include "sharelib.h"

class CField;
class CTable;
class CWhere;
class CSelect : public CObj
{
public:
	CSelect();
	~CSelect();

	void parse( tinyxml2::XMLElement* node, CTable* t );
	
	void genHeader( ofstream& fout );
	void genCpp( ofstream& fout );
	void genPhp( ofstream& fout );
	void genYaf( ofstream& fout );

private:
	CTable* _t;
	string _name;
	string _desc;
	vector< CField* > _vFields;
	vector< string > _vFieldsName;
	vector< CWhere* > _vWheres;

};


#endif //_SELECT_H_
