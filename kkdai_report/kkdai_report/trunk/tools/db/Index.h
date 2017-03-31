#ifndef _INDEX_H_
#define _INDEX_H_
#include "sharelib.h"

class CIndex : public CObj
{
public:
	CIndex();
	~CIndex();

	void parse( tinyxml2::XMLElement* node );

	string get();

	const string& getName() const { return _name; }
	const string& getValue() const { return _value; }

private:
	string _name;
	string _value;
	vector<string> _vValue;
};


#endif //_INDEX_H_
