#ifndef _SEND_ARG_H_
#define _SEND_ARG_H_
#include "Utils.h"

class CSendArg
{
public:
	CSendArg();
	~CSendArg();

	static CSendArg* create( const string& name, const string& type, const string& desc = "", const string& value = "", bool null = true );

	void parse( tinyxml2::XMLElement* node );

	const string& getName() const { return _name; }
	const string& getType() const { return _type; }
	const string& getValue() const { return _value; }
	const string& getDesc() const { return _desc; }
	bool canNull() const { return _null; }

	bool isShowArg() const { return _value.empty(); }

	bool isReal() const { return _real; }
private:
	string _name;
	string _type;
	string _value;
	string _desc;
	bool _null;
	bool _real;
};

#endif //_SEND_ARG_H_
