#ifndef _FIELD_H_
#define _FIELD_H_
#include "sharelib.h"

class CField : public CObj
{	
public:
	CField();
	~CField();

	void parse( tinyxml2::XMLElement* node );
	string get();

	bool isString();
	const string& getName() const { return _name; }
	const string& getType() const { return _type; }
	int getSize() const { return _size; }
	bool isAuto() const { return _auto; }
	bool isPrimary() const { return _primary; }
	bool isNull() const { return _null; }
	bool isBin() const { return _bin; }
	const string& getValue() const { return _value; }
	bool hasValue() const { return !_value.empty(); }
	bool isUnique() const { return _unique; }
	bool isZeroFill() const { return _zeroFill; }
	bool isArray() const { return _array; }
	const string& getDesc() const { return _desc; }
	const string& getRealDbType() const { return _realDbType; }
	const string& getCType() const { return _cType; }
	const string& getConst() const { return _const; }
	const string& getRef() const { return _ref; }
	const string& getDefault() const { return _default; }
	const string& getDefaultPhp() const { return _defaultPhp; }
	string getPhpMap( bool needDefault = false );
	bool needBinTrans() const { return _needBinTrans; }
		
private:
	void switchType();

private:
	string _name;
	string _type;
	int _size;
	bool _auto;
	bool _primary;
	bool _null;
	bool _bin;
	string _value;
	bool _unique;
	bool _zeroFill;
	bool _array;
	string _desc;
	string _realDbType;
	string _cType;
	string _const;
	string _ref;
	string _default;
	string _defaultPhp;
	bool _needBinTrans;
};

#endif //_FIELD_H_
