#ifndef _TABLE_H_
#define _TABLE_H_
#include "sharelib.h"

class CField;
class CIndex;
class CUpdate;
class CSelect;
class CDelete;

class CTable : public CObj
{
public:
	CTable();
	~CTable();

	void parse( tinyxml2::XMLElement* node );
	string get();
	void genStruct( ofstream& fout );
	void genHeader( ofstream& fout );
	void genCpp( ofstream& fout );
	CField* getFieldByName( const string& name );
	const string& getName() const { return _name; }
	const string& getDesc() const { return _desc; }

	void genPhp( ofstream& fout );

	void genCreateSql( ofstream& fout );
	void genYaf();
private:
	void genCreatePhp( ofstream& fout );
	void genInsertPhp( ofstream& fout );
	void genSelectPhp( ofstream& fout );
	CField* getPrimary();

private:
	string _name;
	string _desc;
	vector< CField* > _vFields;
	vector< CIndex* > _vIndexes;
	vector< CUpdate* > _vUpdates;
	vector< CSelect* > _vSelects;
	vector< CDelete* > _vDeletes;
};

#endif //_TABLE_H_
