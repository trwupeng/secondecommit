#ifndef _DELETE_H_
#define _DELETE_H_
#include "sharelib.h"
class CTable;
class CWhere;

class CDelete : public CObj
{
public:
	CDelete();
	~CDelete();

	void parse( tinyxml2::XMLElement* node, CTable* t );
	void genHeader( ofstream& fout );
	void genCpp( ofstream& fout );
	void genPhp( ofstream& fout );

private:
	CTable* _t;
	string _name;
	string _desc;
	vector< CWhere* > _vWheres;
};


#endif //_DELETE_H_
