#ifndef _WHERE_H_
#define _WHERE_H_
#include "sharelib.h"

class CTable;
class CField;
class CWhere : public CObj
{
public:
	CWhere();
	~CWhere();

	void parse( tinyxml2::XMLElement* node, CTable* t, CWhere* parent = NULL );
	const CField* getField() const { return _field; }
	const string& getName() const { return _name; }
	const string& getComp() const { return _comp; }
	const string& getSuffix() const { return _suffix; }
	string getFullName() const { return _name + "_" + _suffix; }
	bool isLike() const { return false; }
	string getYaf( int tabNum = 0 );
	CWhere* getParent() const { return _parent; }
	void getLeaves( vector<CWhere*>& v );

private:
	string genSuffix();

private:
	string _name;
	string _comp;
	string _suffix;
	CField* _field;
	string _type;
	CWhere* _parent;
	vector<CWhere*> _vChildren;
};


#endif //_WHERE_H_
