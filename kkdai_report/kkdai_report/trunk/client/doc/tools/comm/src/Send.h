#ifndef _SEND_H_
#define _SEND_H_
#include "Utils.h"

class CSendArg;
class CSend
{
public:
	CSend();
	~CSend();

	void parse( tinyxml2::XMLElement* node );
	void genForOcHeader( ofstream& fout );
	void genForOcMM( ofstream& fout );
	void genForSwift( ofstream& fout );
	void genForJava( ofstream& fout );

	const string& getName() const { return _name; }
	const string& getApi() const { return _api; }
	int getRetry() const { return _retry; }
	bool isUi() const { return _ui; }
	bool hasCallback() const { return _callback || _ui; }
	bool hasError() const { return _error; }
	bool hasView() const { return _view; }
	bool hasLoading() const { return _loading; }
	int getTimeout() const { return _timeout; }
	int getTimer() const { return _timer; }
	const string& getDesc() const { return _desc; }
	bool hasUserData() const { return _userData; }

private:
	void genForOcDeclare( const string& prefix, ofstream& fout, bool header );

private:
	string _name;
	string _api;
	int _retry;
	bool _ui;
	bool _callback;
	bool _error;
	bool _view;
	bool _loading;
	int _timeout;
	int _timer;
	string _desc;
	bool _userData;

	vector< CSendArg* > _vArg;
};


#endif //_SEND_H_
