#ifndef _UPDATE_H_
#define _UPDATE_H_
#include "sharelib.h"

class CTable;
class CField;
class CWhere;
class CUpdate : public CObj
{
public:
	CUpdate();
	~CUpdate();

	void parse( tinyxml2::XMLElement* node, CTable* t );

	void genHeader( ofstream& fout );
	void genCpp( ofstream& fout );
	void genPhp( ofstream& fout );
	void genYaf( ofstream& fout );

private:
	CTable* _t;
	string _name;
	string _desc;
	vector<CField*> _vFields;
	vector<CWhere*> _vWheres;
};

#endif //_UPDATE_H_
