#ifndef _ENUM_DATA_H_
#define _ENUM_DATA_H_
#include "Utils.h"

class CEnumData
{
public:
	CEnumData();
	~CEnumData();

	void parse( tinyxml2::XMLElement* node );

	void genForOc( ofstream& fout, const string& prefix );
	void genForSwift( ofstream& fout );
	void genForJava( ofstream& fout );

	const string& getName() const { return _name; }
	const string& getType() const { return _type; }
	const string& getValue() const { return _value; }
	const string& getDesc() const { return _desc; }

private:
	string _name;
	string _type;
	string _value;
	string _desc;
};


#endif //_ENUM_DATA_H_
