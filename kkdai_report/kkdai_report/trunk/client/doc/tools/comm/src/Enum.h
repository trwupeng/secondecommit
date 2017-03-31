#ifndef _ENUM_H_
#define _ENUM_H_
#include "Utils.h"

class CEnumData;

class CEnum
{
public:
	CEnum();
	~CEnum();

	void parse( tinyxml2::XMLElement* node );
	
	void genForOc( ofstream& fout );
	void genForSwift( ofstream& fout );
	void genForJava( ofstream& fout );
		
	const string& getName() const { return _name; }
	const string& getDesc() const { return _desc; }

private:
	string _name;
	string _desc;
	string _swiftType;
	string _javaType;
	bool _enum;
	vector< CEnumData* > _vData;
};


#endif //_ENUM_H_
