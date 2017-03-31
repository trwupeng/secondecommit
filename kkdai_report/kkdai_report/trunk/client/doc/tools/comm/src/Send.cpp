#include "Send.h"
#include "SendArg.h"

CSend::CSend()
{
}
CSend::~CSend()
{
}

void CSend::parse( tinyxml2::XMLElement* node )
{
	_name = Utils::getXmlAttrStr( node, "func" );
	Utils::trim( _name );
	_api = Utils::getXmlAttrStr( node, "api" );
	_retry = Utils::getXmlAttrInt( node, "retry" );
	_ui = Utils::getXmlAttrBool( node, "ui" );
	_callback = Utils::getXmlAttrBool( node, "callback" );
	_error = Utils::getXmlAttrBool( node, "error" );
	_view = Utils::getXmlAttrBool( node, "view" );
	_loading = Utils::getXmlAttrBool( node, "loading" );
	_timeout = Utils::getXmlAttrInt( node, "timeout" );
	_timer = Utils::getXmlAttrInt( node, "timer" );
	_desc = Utils::getXmlAttrStr( node, "desc" );
	_userData = Utils::getXmlAttrBool( node, "user_data" );

	for ( tinyxml2::XMLElement* child = node->FirstChildElement(); NULL != child; child = child->NextSiblingElement() )
	{
		CSendArg* p = new CSendArg;
		p->parse( child );
		_vArg.push_back(p);
	}

	if ( _callback || _ui )
	{
		CSendArg* p = CSendArg::create( "callback", "callback", "callback function", "", false );
		_vArg.push_back(p);
	}

	if ( _view || _ui )
	{
		CSendArg* p = CSendArg::create( "view", "view", "waiting view's parent" );
		_vArg.push_back(p);
	}

	if ( _loading )
	{
		CSendArg* p = CSendArg::create( "loading", "string", "waiting view's text" );
		_vArg.push_back(p);
	}

	if ( _userData )
	{
		CSendArg* p = CSendArg::create( "userData", "string", "user's custom data" );
		_vArg.push_back(p);
	}
}
void CSend::genForOcHeader( ofstream& fout )
{
	genForOcDeclare( "	", fout, true );
}
void CSend::genForOcMM( ofstream& fout )
{
	genForOcDeclare( "", fout, false );
	fout<<"{"<<endl;
	bool hasAssert = false;
	//先是断言
	for ( size_t i=0; i<_vArg.size(); ++i )
	{
		CSendArg* arg = _vArg[i];
		if ( !arg->getValue().empty() )
		{
			continue;
		}
		if ( arg->canNull() )
		{
			continue;
		}

		if ( "string" == arg->getType() )
		{
			hasAssert = true;
			fout<<"	ASSERT0( nil != "<<arg->getName()<<" );"<<endl;
		}
		else
		{
			size_t n = arg->getType().find( "*" );
			if ( string::npos != n )
			{
				hasAssert = true;
				fout<<"	ASSERT0( nil != "<<arg->getName()<<" );"<<endl;
			}
		}
	}
	if ( hasAssert )
	{
		fout<<endl;
	}
	fout<<"	[CommCtrl start];"<<endl;
	fout<<"	NSString* url = [CommCtrl makeUrl:@\""<<getApi()<<"\"];"<<endl;
	string param = "@{";
	if ( !_vArg.empty() )
	{
		for ( size_t i=0; i<_vArg.size(); ++i )
		{
			CSendArg* arg = _vArg[i];
			if ( !arg->isReal() )
			{
				continue;
			}
			if ( param.size() > 2 && param[param.size()-1] != ',' )
			{
				param += ",";
			}
			param += "@\"" + arg->getName() + "\":";
			if ( !arg->getValue().empty() )
			{
				param += "@\"" + arg->getValue() + "\"";
			}
			else
			{
				const string& type = arg->getType();
				if ( "string" == type )
				{
					if ( arg->canNull() )
					{
						param += "nil==" + arg->getName() + "?@\"\":" + arg->getName();
					}
					else
					{
						param += arg->getName();
					}
				}
				else if ( "int" == type )
				{
					param += "[NSString stringWithFormat:@\"%d\", " + arg->getName() + "]";
				}
				else if ( "float" == type )
				{
					param += "[NSString stringWithFormat:@\"%f\", " + arg->getName() + "]";
				}
				else if ( "bool" == type )
				{
					param += arg->getName() + "?@\"1\":@\"0\"";
				}
			}
		}
	}
	param += "}";

	string callback = "nil";
	if ( hasCallback() )
	{
		callback = "callback";
	}

	string type;
	if ( isUi() )
	{
		type = "CT_UI";
	}
	else if ( hasCallback() )
	{
		type = "CT_WORK";
	}
	else 
	{
		type = "CT_BG";
	}

	string view = "nil";
	if ( hasView() )
	{
		view = "view";
	}

	string loading = "nil";
	if ( hasLoading() )
	{
		loading = "loading";
	}

	string error = "NO";
	if ( hasError() )
	{
		error = "YES";
	}

	string userData = "nil";
	if ( hasUserData() )
	{
		userData = "userData";
	}

	vector< string > vExParam;
	if ( getTimeout() > 0 )
	{
		char szTemp[100] = {0};
		sprintf( szTemp, "%d", getTimeout() );
		string s = "@\"timeout\":@\"";
		s += szTemp;
		s += "\"";
		vExParam.push_back(s);
	}
	if ( getRetry() > 0 )
	{
		char szTemp[100] = {0};
		sprintf( szTemp, "%d", getRetry() );
		string s = "@\"retry\":@\"";
		s += szTemp;
		s += "\"";
		vExParam.push_back(s);
	}
	if ( getTimer() > 0 )
	{
		char szTemp[100] = {0};
		sprintf( szTemp, "%d", getTimer() );
		string s = "@\"timer\":@\"";
		s += szTemp;
		s += "\"";
		vExParam.push_back(s);
	}
	string exparam;
	if ( vExParam.empty() )
	{
		exparam = "nil";
	}
	else
	{
		exparam = "@{";
		for ( size_t i=0; i<vExParam.size(); ++i )
		{
			if ( 0 != i )
			{
				exparam += ",";
			}
			exparam += vExParam[i];
		}
		exparam += "}";
	}

	if ( 0 == getTimer() )
	{
		fout<<"	[CommCtrl send:url param:"<<param<<" callback:"<<callback<<" type:"<<type<<" view:"<<view<<" loading:"<<loading<<" needErr:"<<error<<" exParam:"<<exparam<<" userData:"<<userData<<"];"<<endl;
	}
	else
	{
		fout<<"	CommInfo* info = [CommCtrl send:url param:"<<param<<" callback:"<<callback<<" type:"<<type<<" view:"<<view<<" loading:"<<loading<<" needErr:"<<error<<" exParam:"<<exparam<<" userData:"<<userData<<"];"<<endl;
			fout<<"	[TimerCtrl addTimer:@\"CommSender_"<<getName()<<"\" interval:"<<getTimer()<<" callback:^( NSString* name, id userData ) {"<<endl;
		fout<<"		[CommCtrl addComm:info];"<<endl;
		fout<<"	} userData:info];"<<endl;
	}

	fout<<"}"<<endl;
	fout<<endl;
}
void CSend::genForSwift( ofstream& fout )
{
	vector< CSendArg* > vArg;
	for ( size_t i=0; i<_vArg.size(); ++i )
	{
		if ( _vArg[i]->isShowArg() )
		{
			vArg.push_back( _vArg[i] );
		}
	}
	fout<<"	//"<<_desc<<endl;
	fout<<"	static func "<<_name<<"("<<endl;
	for ( size_t i=0; i<vArg.size(); ++i )
	{
		CSendArg* arg = vArg[i];
		fout<<"		";
		if ( i > 0 )
		{
			fout<<", ";
		}
		else
		{
			fout<<"_ ";
		}
		fout<<arg->getName()<<" : "<<Utils::transSwiftType(arg->getType());
		if ( arg->canNull() )
		{
			fout<<"?";
		}
		fout<<"	//"<<arg->getDesc()<<endl;
	}
	fout<<"		)"<<endl;
	fout<<"	{"<<endl;
	fout<<"		CommCtrl.start();"<<endl;
	fout<<"		let url : String = CommCtrl.makeUrl( \""<<getApi()<<"\" );"<<endl;
	string param = "[";
	if ( !_vArg.empty() )
	{
		for ( size_t i=0; i<_vArg.size(); ++i )
		{
			CSendArg* arg = _vArg[i];
			if ( !arg->isReal() )
			{
				continue;
			}
			if ( param.size() > 2 && param[param.size()-1] != ',' )
			{
				param += ",";
			}
			param += "\"" + arg->getName() + "\":";
			if ( !arg->getValue().empty() )
			{
				param += "\"" + arg->getValue() + "\"";
			}
			else
			{
				const string& type = arg->getType();
				if ( "string" == type )
				{
					if ( arg->canNull() )
					{
						param += "nil==" + arg->getName() + " ? \"\":" + arg->getName() + "!";
					}
					else
					{
						param += arg->getName();
					}
				}
				else
				{
					param += "String(" + arg->getName();
					if ( arg->canNull() )
					{
						param += "?";
					}
					param += ")";
				}
			}
		}
	}
	if ( 1 == param.size() )
	{
		param += ":";
	}
	param += "]";

	string callback = "nil";
	if ( hasCallback() )
	{
		callback = "callback";
	}

	string type;
	if ( isUi() )
	{
		type = "COMM_TYPE.ui";
	}
	else if ( hasCallback() )
	{
		type = "COMM_TYPE.work";
	}
	else 
	{
		type = "COMM_TYPE.bg";
	}

	string view = "nil";
	if ( hasView() )
	{
		view = "view";
	}

	string loading = "nil";
	if ( hasLoading() )
	{
		loading = "loading";
	}

	string error = "false";
	if ( hasError() )
	{
		error = "true";
	}

	string userData = "nil";
	if ( hasUserData() )
	{
		userData = "userData";
	}

	vector< string > vExParam;
	if ( getTimeout() > 0 )
	{
		char szTemp[100] = {0};
		sprintf( szTemp, "%d", getTimeout() );
		string s = "\"timeout\":\"";
		s += szTemp;
		s += "\"";
		vExParam.push_back(s);
	}
	if ( getRetry() > 0 )
	{
		char szTemp[100] = {0};
		sprintf( szTemp, "%d", getRetry() );
		string s = "\"retry\":\"";
		s += szTemp;
		s += "\"";
		vExParam.push_back(s);
	}
	if ( getTimer() > 0 )
	{
		char szTemp[100] = {0};
		sprintf( szTemp, "%d", getTimer() );
		string s = "\"timer\":\"";
		s += szTemp;
		s += "\"";
		vExParam.push_back(s);
	}
	string exparam;
	if ( vExParam.empty() )
	{
		exparam = "nil";
	}
	else
	{
		exparam = "[";
		for ( size_t i=0; i<vExParam.size(); ++i )
		{
			if ( 0 != i )
			{
				exparam += ",";
			}
			exparam += vExParam[i];
		}
		exparam += "]";
	}

	if ( 0 == getTimer() )
	{
		fout<<"		CommCtrl.send( url"<<endl;
		fout<<"			, param : "<<param<<endl;
		fout<<"			, callback : "<<callback<<endl;
		fout<<"			, type : "<<type<<endl;
		fout<<"			, view : "<<view<<endl;
		fout<<"			, loading : "<<loading<<endl;
		fout<<"			, needErr : "<<error<<endl;
		fout<<"			, exParam : "<<exparam<<endl;
		fout<<"			, userData : "<<userData<<endl;
		fout<<"			);"<<endl;
	}
	else
	{
	/*
		fout<<"	CommInfo* info = [CommCtrl send:url param:"<<param<<" callback:"<<callback<<" type:"<<type<<" view:"<<view<<" loading:"<<loading<<" needErr:"<<error<<" exParam:"<<exparam<<" userData:"<<userData<<"];"<<endl;
			fout<<"	[TimerCtrl addTimer:@\"CommSender_"<<getName()<<"\" interval:"<<getTimer()<<" callback:^( NSString* name, id userData ) {"<<endl;
		fout<<"		[CommCtrl addComm:info];"<<endl;
		fout<<"	} userData:info];"<<endl;
	*/
	}
	fout<<"	}"<<endl;
}
void CSend::genForJava( ofstream& fout )
{
	vector< CSendArg* > vArg;
	for ( size_t i=0; i<_vArg.size(); ++i )
	{
		if ( _vArg[i]->isShowArg() )
		{
			vArg.push_back( _vArg[i] );
		}
	}
	fout<<"	//"<<getDesc()<<endl;
	fout<<"	public static void "<<getName()<<"(";
	for ( size_t i=0; i<vArg.size(); ++i )
	{
		CSendArg* arg = vArg[i];
		fout<<endl;
		fout<<"		"<<Utils::transJavaType(arg->getType())<<" "<<arg->getName();
		if ( (i+1) != vArg.size() )
		{
			fout<<",";
		}
		fout<<"	//"<<arg->getDesc();
	}

	if ( !vArg.empty() )
	{
		fout<<endl;
		fout<<"		";
	}
	fout<<")"<<endl;
	fout<<"	{"<<endl;
	bool hasAssert = false;
	for ( size_t i=0; i<_vArg.size(); ++i )
	{
		CSendArg* arg = _vArg[i];
		if ( !arg->getValue().empty() )
		{
			continue;
		}
		if ( arg->canNull() )
		{
			continue;
		}
		if ( "string" == arg->getType() )
		{
			hasAssert = true;
			fout<<"		if ( null == "<<arg->getName()<<" )"<<endl;
			fout<<"		{"<<endl;
			fout<<"			return ;"<<endl;
			fout<<"		}"<<endl;
		}
		if ( hasAssert )
		{
			fout<<endl;
		}
	}

	fout<<"		try"<<endl;
	fout<<"		{"<<endl;
	fout<<"			CommCtrl.start();"<<endl;
	fout<<"			String url = CommCtrl.makeApi( \""<<getApi()<<"\" );"<<endl;
	string param = "null";
	if ( !_vArg.empty() )
	{
		param = "param";
		fout<<"			HasMap< String, String > param = new HashMap< String, String >();"<<endl;
		for ( size_t i=0; i<_vArg.size(); ++i )
		{
			CSendArg* arg = _vArg[i];
			if ( !arg->isReal() )
			{
				continue;
			}
			const string& type = arg->getType();
			if ( "string" == arg->getType() && arg->canNull() )
			{
				fout<<"			if ( null != "<<arg->getName()<<" )"<<endl;
				fout<<"				param.put( \""<<arg->getName()<<"\", ";
			}
			else
			{
				fout<<"			param.put( \""<<arg->getName()<<"\", ";
			}
			if ( !arg->getValue().empty() )
			{
				fout<<"\""<<arg->getValue()<<"\" );"<<endl;
			}
			else
			{
				if ( "string" == type )
				{
					fout<<arg->getName()<<" );"<<endl;
				}
				else if ( "int" == type || "float" == type || "double" == type )
				{
					fout<<arg->getName()<<"+\"\" );"<<endl;
				}
				else if ( "bool" == type )
				{
					fout<<arg->getName()<<"?\"1\":\"0\" );"<<endl;
				}
			}
		}
	}
	string callback = "null";
	if ( hasCallback() )
	{
		callback = "callback";
	}

	string type;
	if ( isUi() )
	{
		type = "CommConstant.CT_UI";
	}
	else if ( hasCallback() )
	{
		type = "CommConstant.CT_WORK";
	}
	else 
	{
		type = "CommConstant.CT_BG";
	}

	string view = "null";
	if ( hasView() )
	{
		view = "ctx";
	}

	string loading = "null";
	if ( hasLoading() )
	{
		loading = "loading";
	}

	string error = "false";
	if ( hasError() )
	{
		error = "true";
	}

	string exParam = "null";
	if ( getTimeout() > 0 || getRetry() > 0 || getTimer() > 0 )
	{
		exParam = "exParam";
		fout<<"			HashMap< String, String > exParam = new HashMap< String, String >();"<<endl;
	}
	if ( getTimeout() > 0 )
	{
		char szTemp[100] = {0};
		sprintf( szTemp, "%d", getTimeout() );
		fout<<"			exParam.put( \"timeout\", \""<<szTemp<<"\" );"<<endl;
	}

	if ( getRetry() > 0 )
	{
		char szTemp[100] = {0};
		sprintf( szTemp, "%d", getRetry() );
		fout<<"			exParam.put( \"retry\", \""<<szTemp<<"\" );"<<endl;
	}

	if ( getTimer() > 0 )
	{
		char szTemp[100] = {0};
		sprintf( szTemp, "%d", getTimer() );	
		fout<<"			exParam.put( \"timer\", \""<<szTemp<<"\" );"<<endl;
	}

	string userData = "null";
	if ( hasUserData() )
	{
		userData = "userData";
	}

	if ( 0 == getTimer() )
	{
		fout<<"			CommCtrl.send(url, "<<param<<", "<<callback<<", "<<type<<", "<<view<<", "<<loading<<", "<<error<<", "<<exParam<<", \""<<getApi()<<"\", "<<userData<<" );"<<endl;
	}
	else
	{
		fout<<"			CommInfo info = CommCtrl.send(url, "<<param<<", "<<callback<<", "<<type<<", "<<view<<", "<<loading<<", "<<error<<", "<<exParam<<", \""<<getApi()<<"\", "<<userData<<" );"<<endl;
		fout<<"			TimerCtrl.addTimer( \"CommSender_"<<getName()<<"\", "<<getTimer()<<", new TimerInterface() {"<<endl;
		fout<<"				@Override"<<endl;
		fout<<"				public void onCallback(String name, Object userData) {"<<endl;
		fout<<"					CommCtrl.addComm( (CommInfo)userData );"<<endl;
		fout<<"				}"<<endl;
		fout<<"			}, info );"<<endl;
	}
	if ( "null" != param )
	{
		fout<<"			param = null;"<<endl;
	}

	if ( "null" != exParam )
	{
		fout<<"			exParam = null;"<<endl;
	}
	fout<<"		} catch( Exception e ){"<<endl;
	fout<<"			e.printStackTrace();"<<endl;
	fout<<"		}"<<endl;
	fout<<"	}"<<endl;
}

void CSend::genForOcDeclare( const string& prefix, ofstream& fout, bool header )
{
	vector< CSendArg* > vArg;
	for ( size_t i=0; i<_vArg.size(); ++i )
	{
		if ( _vArg[i]->isShowArg() )
		{
			vArg.push_back( _vArg[i] );
		}
	}
	fout<<prefix<<"+ (void)"<<_name;
	for ( size_t i=0; i<vArg.size(); ++i )
	{
		CSendArg* arg = vArg[i];
		if ( 0 == i )
		{
			fout<<":("<<Utils::transOcType(arg->getType())<<")"<<arg->getName();
		}
		else
		{
			fout<<prefix;
			fout<<" "<<arg->getName()<<":("<<Utils::transOcType(arg->getType())<<")"<<arg->getName();
		}
		if ( (i+1) != vArg.size() || !header )
		{
			fout<<"	//"<<arg->getDesc()<<endl;
		}
		else
		{
			fout<<";	//"<<arg->getDesc()<<endl;
		}
	}

	if ( vArg.empty() && header )
	{
		fout<<";"<<endl;
	}
}
