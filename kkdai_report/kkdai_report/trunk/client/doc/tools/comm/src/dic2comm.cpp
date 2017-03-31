#include <iostream>
#include <vector>
#include <string>
#include <map>
#include <fstream>
#include "tinyxml2.h"
using namespace std;
using namespace tinyxml2;

struct ENUM_DATA
{
	string name;
	string type;
	string value;
	string desc;
};
struct ENUM_INFO
{
	string name;
	string desc;
	bool isEnum;
	vector<ENUM_DATA*> data;
};

struct ARG_DATA
{
	string name;
	string type;
	string desc;
	bool null;
	string Default;
};
struct SEND_INFO
{
	string func;
	string api;
	string urlType;
	string desc;
	bool ui;
	bool callback;
	bool error;
	bool view;
	bool loading;
	int timeout;
	int retry;
	bool userData;
	int timer;
	vector< ARG_DATA* > arg;
};

vector< ENUM_INFO* > g_vEnums;
vector< SEND_INFO* > g_vSend;

void parseEnums( XMLElement* node );
void parseSend( XMLElement* noce );

void genEnums( const string& dir );
void genSend( const string& dir );

int main( int argc, char** argv )
{
	if ( argc < 3 )
	{
		cout<<"usage:dic2comm input_file output_dir"<<endl;
		return -1;
	}

	XMLDocument doc;
	int err = doc.LoadFile( argv[1] );
	if ( XML_NO_ERROR != err )
	{
		cout<<"load file:"<<argv[1]<<" failed, error code:"<<err<<endl;
		return -2;
	}

	for ( XMLElement* root = doc.RootElement(); NULL != root; root = root->NextSiblingElement() )
	{
		string name = root->Name();
		if ( "enums" == name )
		{
			parseEnums( root );
		}
		else if ( "sends" == name )
		{
			parseSend( root );
		}
	}

	genEnums( argv[2] );
	genSend( argv[2] );
	return 0;
}

void parseOneEnum( XMLElement* node )
{
	ENUM_INFO* info = new ENUM_INFO;
	info->name = node->Attribute( "name" );
	
	{
		const char* tmp = node->Attribute( "desc" );
		if ( NULL != tmp )
		{
			info->desc = tmp;
		}
	}
	info->isEnum = true;

	for ( XMLElement* child = node->FirstChildElement(); NULL != child; child = child->NextSiblingElement() )
	{
		ENUM_DATA* data = new ENUM_DATA;
		data->name = child->Attribute( "name" );
		data->type = child->Attribute( "type" );
		data->value = child->Attribute( "value" );

		{
			const char* tmp = child->Attribute( "desc" );
			if ( NULL != tmp )
			{
				data->desc = tmp;
			}
		}
		info->data.push_back(data);
		if ( "int" != data->type )
		{
			info->isEnum = false;
		}
	}
	g_vEnums.push_back(info);
}
void parseEnums( XMLElement* node )
{
	for ( XMLElement* child = node->FirstChildElement(); NULL != child; child = child->NextSiblingElement() )
	{
		string name = child->Name();
		if ( "enum" == name )
		{
			parseOneEnum( child );
		}
	}
}
void parseOneSend( XMLElement* node )
{
	SEND_INFO* info = new SEND_INFO;
	info->func = node->Attribute( "func" );
	info->api = node->Attribute( "api" );

	{
		const char* tmp = node->Attribute( "url_type" );
		if ( NULL != tmp )
		{
			info->urlType = tmp;
		}
		else
		{
			info->urlType = "api";
		}
	}

	{
		const char* tmp = node->Attribute( "desc" );
		if ( NULL != tmp )
		{
			info->desc = tmp;
		}
	}

	info->ui = false;
	{
		const char* tmp = node->Attribute( "ui" );
		if ( NULL != tmp && 0 == strcmp( "true", tmp ) )
		{
			info->ui = true;
		}
	}

	if ( info->ui )
	{
		info->callback = true;
	}
	else
	{
		info->callback = false;
		{
			const char* tmp = node->Attribute( "callback" );
			if ( NULL != tmp && 0 == strcmp( "true", tmp ) )
			{
				info->callback = true;
			}
		}
	}

	info->error = false;
	{
		{
			const char* tmp = node->Attribute( "error" );
			if ( NULL != tmp && 0 == strcmp( "true", tmp ) )
			{
				info->error = true;
			}
		}
	}

	if ( info->ui )
	{
		info->view = true;
	}
	else
	{
		info->view = false;
		{
			const char* tmp = node->Attribute( "view" );
			if ( NULL != tmp && 0 == strcmp( "true", tmp ) )
			{
				info->view = true;
			}
		}
	}

	info->loading = false;
	if ( info->view )
	{
		const char* tmp = node->Attribute( "loading" );
		if ( NULL != tmp && 0 == strcmp( "true", tmp ) )
		{
			info->loading = true;
		}
	}

	info->timeout = 0;
	{
		const char* tmp = node->Attribute( "timeout" );
		if ( NULL != tmp && strlen(tmp) > 0 )
		{
			info->timeout = atoi(tmp);
		}
	}

	info->retry = 0;
	{
		const char* tmp = node->Attribute( "retry" );
		if ( NULL != tmp && strlen(tmp) > 0 )
		{
			info->retry = atoi(tmp);
		}
	}

	info->userData = false;
	{
		const char* tmp = node->Attribute( "user_data" );
		if ( NULL != tmp && 0 == strcmp( "true", tmp ) )
		{
			info->userData = true;
		}
	}

	info->timer = 0;
	{
		const char* tmp = node->Attribute( "timer" );
		if ( NULL != tmp && strlen(tmp) > 0 )
		{
			info->timer = atoi(tmp);
		}
	}

	for ( XMLElement* child = node->FirstChildElement(); NULL != child; child = child->NextSiblingElement() )
	{
		ARG_DATA* arg = new ARG_DATA;
		arg->name = child->Attribute( "name" );
		arg->type = child->Attribute( "type" );

		{
			const char* tmp = child->Attribute( "desc" );
			if ( NULL != tmp )
			{
				arg->desc = tmp;
			}
		}

		arg->null = false;
		{
			const char* tmp = child->Attribute( "null" );
			if ( NULL != tmp && 0 == strcmp( "true", tmp ) )
			{
				arg->null = true;
			}
		}

		{
			const char* tmp = child->Attribute( "value" );
			if ( NULL != tmp )
			{
				arg->Default = tmp;
			}
		}
		info->arg.push_back(arg);
	}
	g_vSend.push_back(info);
}
void parseSend( XMLElement* node )
{
	for ( XMLElement* child = node->FirstChildElement(); NULL != child; child = child->NextSiblingElement() )
	{
		string name = child->Name();
		if ( "send" == name )
		{
			parseOneSend( child );
		}
	}
}

void genIosEnums( const string& dir )
{
	string file = dir + "/enums.h";
	ofstream fout( file.data() );
	if ( !fout.is_open() )
	{
		cout<<"open file:"<<file<<" failed"<<endl;
		return ;
	}

	fout<<"//This file is generated by dic2comm. Don't modify it."<<endl;
	fout<<"#ifndef _ENUMS_H_"<<endl;
	fout<<"#define _ENUMS_H_"<<endl;
	fout<<endl;
	
	for ( size_t i=0; i<g_vEnums.size(); ++i )
	{
		ENUM_INFO* info = g_vEnums[i];
		string prefix;
		fout<<"//"<<info->desc<<endl;
		if ( info->isEnum )
		{
			prefix = "	";
			fout<<"enum "<<info->name<<endl;
			fout<<"{"<<endl;
		}

		for ( size_t j=0; j<info->data.size(); ++j )
		{
			ENUM_DATA* data = info->data[j];
			if ( "string" == data->type )
			{
				fout<<prefix<<"static NSString* "<<data->name<<" = "<<"@\""<<data->value<<"\"";
			}
			else
			{
				fout<<prefix<<data->name<<" = "<<data->value;
			}
			if ( info->isEnum )
			{
				if ( (j+1) != info->data.size() )
				{
					fout<<",";
				}
			}
			else
			{
				fout<<";";
			}
			fout<<" //"<<data->desc<<endl;
		}

		if ( info->isEnum )
		{
			fout<<"};"<<endl;
		}
		fout<<endl;
	}

	fout<<"#endif //_ENUMS_H_"<<endl;

	fout.close();
}
string getAndroidType( const string& type )
{
	if ( "string" == type )
	{
		return "String";
	}
	else if ( "bool" == type )
	{
		return "boolean";
	}

	return type;
}
void genAndroidEnums( const string& dir )
{
	string file = dir + "/enums.java";
	ofstream fout( file.data() );
	if ( !fout.is_open() )
	{
		cout<<"open file:"<<file<<" failed"<<endl;
		return ;
	}

	fout<<"//This file is generated by dic2comm. Don't modify it."<<endl;
	fout<<"package com.xlicai.comm;"<<endl;
	fout<<endl;

	fout<<"public class enums {"<<endl;
	
	for ( size_t i=0; i<g_vEnums.size(); ++i )
	{
		ENUM_INFO* info = g_vEnums[i];
		fout<<"	//"<<info->desc<<endl;
		for ( size_t j=0; j<info->data.size(); ++j )
		{
			ENUM_DATA* data = info->data[j];
			if ( "string" == data->type )
			{
				fout<<"	static final public String "<<data->name<<" = "<<"\""<<data->value<<"\";";
			}
			else
			{
				fout<<"	static final public "<<getAndroidType(data->type)<<" "<<data->name<<" = "<<data->value<<";";
			}
			fout<<" //"<<data->desc<<endl;
		}

		fout<<endl;
	}

	fout<<"}"<<endl;
	fout.close();
}
void genEnums( const string& dir )
{
	genIosEnums( dir );
	genAndroidEnums( dir );
}
string getIosType( const string& type )
{
	if ( "string" == type )
	{
		return "NSString*";
	}
	else if ( "float" == type )
	{
		return "CGFloat";
	}
	else if ( "bool" == type )
	{
		return "BOOL";
	}

	return type;
}
void genIosFuncDeclare( SEND_INFO* info, ofstream& fout, bool header )
{
	fout<<"//"<<info->desc<<endl;
	fout<<"+ (void)"<<info->func;
	vector< ARG_DATA* > v;
	for ( size_t j=0; j<info->arg.size(); ++j )
	{
		ARG_DATA* arg = info->arg[j];
		if ( arg->Default.empty() )
		{
			v.push_back(arg);
		}
	}

	if ( info->callback )
	{
		ARG_DATA* arg = new ARG_DATA;
		arg->name = "callback";
		arg->type = "void(^)(long code, NSString* msg, NSError* error)";
		arg->desc = "callback function";
		arg->null = false;
		v.push_back(arg);
	}

	if ( info->view )
	{
		ARG_DATA* arg = new ARG_DATA;
		arg->name = "view";
		arg->type = "UIView*";
		arg->desc = "waiting view's parent";
		arg->null = false;
		v.push_back(arg);
	}

	if ( info->loading )
	{
		ARG_DATA* arg = new ARG_DATA;
		arg->name = "loading";
		arg->type = "string";
		arg->desc = "waiting view's text";
		arg->null = false;
		v.push_back(arg);
	}

	if ( info->userData )
	{
		ARG_DATA* arg = new ARG_DATA;
		arg->name = "userData";
		arg->type = "string";
		arg->desc = "user custom data";
		arg->null = false;
		v.push_back(arg);
	}

	for ( size_t j=0; j<v.size(); ++j )
	{
		ARG_DATA* arg = v[j];
		if ( 0 == j )
		{
			fout<<":("<<getIosType(arg->type)<<")"<<arg->name;
		}
		else
		{
			fout<<"	"<<arg->name<<":("<<getIosType(arg->type)<<")"<<arg->name;
		}
		if ( (j+1) != v.size() || !header )
		{
			fout<<" //"<<arg->desc<<endl;
		}
		else
		{
			fout<<"; //"<<arg->desc<<endl;
		}
	}
	if ( v.empty() )
	{
		fout<<";"<<endl;
	}
}
void genIosSendHeader( const string& dir )
{
	string file = dir + "/CommSender.h";
	ofstream fout( file.data() );
	if ( !fout.is_open() )
	{
		cout<<"create file:"<<file<<" failed"<<endl;
		return ;
	}
	fout<<"//This file is generated by dic2comm. Don't mofity it."<<endl;
	fout<<"#ifndef _COMM_SENDER_H_"<<endl;
	fout<<"#define _COMM_SENDER_H_"<<endl;
	fout<<endl;

	fout<<"@interface CommSender : NSObject"<<endl;
	fout<<endl;

	for ( size_t i=0; i<g_vSend.size(); ++i )
	{
		genIosFuncDeclare( g_vSend[i], fout, true );
		fout<<endl;
	}

	fout<<"@end"<<endl;
	
	fout<<"#endif //_COMM_SENDER_H_"<<endl;
	fout.close();
}
void genIosSendCpp( const string& dir )
{
	string file = dir + "/CommSender.mm";
	ofstream fout(file.data());
	if ( !fout.is_open() )
	{
		cout<<"create file:"<<file<<" failed"<<endl;
		return ;
	}

	fout<<"//This file is generated by dic2comm. Don't mofity it."<<endl;
	fout<<"#import \"CommSender.h\""<<endl;
	fout<<"#import \"CommCtrl.h\""<<endl;
	fout<<endl;

	fout<<"@implementation CommSender"<<endl;
	fout<<endl;

	for ( size_t i=0; i<g_vSend.size(); ++i )
	{
		genIosFuncDeclare( g_vSend[i], fout, false );
		fout<<"{"<<endl;
		SEND_INFO* info = g_vSend[i];
		bool hasAssert = false;
		for ( size_t j=0; j<info->arg.size(); ++j )
		{
			ARG_DATA* arg = info->arg[j];
			if ( !arg->Default.empty() )
			{
				continue;
			}
			if ( arg->null )
			{
				continue;
			}
			if ( "string" == arg->type )
			{
				hasAssert = true;
				fout<<"	ASSERT0( nil != "<<arg->name<<" );"<<endl;
			}
			else
			{
				size_t n = arg->type.find( "*" );
				if ( string::npos != n )
				{
					hasAssert = true;
					fout<<"	ASSERT0( nil != "<<arg->name<<" );"<<endl;
				}
			}
		}
		if ( hasAssert )
		{
			fout<<endl;
		}
		fout<<"	[CommCtrl start];"<<endl;
		if ( "auth" == info->urlType )
		{
			fout<<"	NSString* url = [CommCtrl makeAuth:@\""<<info->api<<"\"];"<<endl;
		}
		else if ( "cms" == info->urlType )
		{
			fout<<"	NSString* url = [CommCtrl makeCms:@\""<<info->api<<"\"];"<<endl;
		}
		else
		{
			fout<<"	NSString* url = [CommCtrl makeApi:@\""<<info->api<<"\"];"<<endl;
		}
		string param = "@{";
		if ( !info->arg.empty() )
		{
			for ( size_t j=0; j<info->arg.size(); ++j )
			{
				if ( param.size() > 2 && param[param.size()-1] != ',' )
				{
					param += ",";
				}
				ARG_DATA* arg = info->arg[j];
				param += "@\"" + arg->name + "\":";
				if ( !arg->Default.empty() )
				{
					param += "@\"" + arg->Default + "\"";
				}
				else
				{
					if ( "string" == arg->type )
					{
						if ( arg->null )
						{
							param += "nil==" + arg->name + "?@\"\":" + arg->name;
						}
						else
						{
							param += arg->name;
						}
					}
					else if ( "int" == arg->type )
					{
						param += "[NSString stringWithFormat:@\"%d\", " + arg->name + "]";
					}
					else if ( "float" == arg->type )
					{
						param += "[NSString stringWithFormat:@\"%f\", " + arg->name + "]";
					}
					else if ( "bool" == arg->type )
					{
						param += arg->name + "?@\"1\":@\"0\"";
					}
				}
			}
		}
		param += "}";

		string callback = "nil";
		if ( info->callback )
		{
			callback = "callback";
		}

		string type;
		if ( info->ui )
		{
			type = "CT_UI";
		}
		else if ( info->callback )
		{
			type = "CT_WORK";
		}
		else 
		{
			type = "CT_BG";
		}

		string view = "nil";
		if ( info->view )
		{
			view = "view";
		}

		string loading = "nil";
		if ( info->loading )
		{
			loading = "loading";
		}

		string error = "NO";
		if ( info->error )
		{
			error = "YES";
		}

		string userData = "nil";
		if ( info->userData )
		{
			userData = "userData";
		}

		vector< string > vExParam;
		if ( info->timeout > 0 )
		{
			char szTemp[100] = {0};
			sprintf( szTemp, "%d", info->timeout );
			string s = "@\"timeout\":@\"";
			s += szTemp;
			s += "\"";
			vExParam.push_back(s);
		}
		if ( info->retry > 0 )
		{
			char szTemp[100] = {0};
			sprintf( szTemp, "%d", info->retry );
			string s = "@\"retry\":@\"";
			s += szTemp;
			s += "\"";
			vExParam.push_back(s);
		}
		if ( info->timer > 0 )
		{
			char szTemp[100] = {0};
			sprintf( szTemp, "%d", info->timer );
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

		if ( 0 == info->timer )
		{
			fout<<"	[CommCtrl send:url param:"<<param<<" callback:"<<callback<<" type:"<<type<<" view:"<<view<<" loading:"<<loading<<" needErr:"<<error<<" exParam:"<<exparam<<" userData:"<<userData<<"];"<<endl;
		}
		else
		{
			fout<<"	CommInfo* info = [CommCtrl send:url param:"<<param<<" callback:"<<callback<<" type:"<<type<<" view:"<<view<<" loading:"<<loading<<" needErr:"<<error<<" exParam:"<<exparam<<" userData:"<<userData<<"];"<<endl;
			fout<<"	[TimerCtrl addTimer:@\"CommSender_"<<info->func<<"\" interval:"<<info->timer<<" callback:^( NSString* name, id userData ) {"<<endl;
			fout<<"		[CommCtrl addComm:info];"<<endl;
			fout<<"	} userData:info];"<<endl;
		}

		fout<<"}"<<endl;
		fout<<endl;
	}

	fout<<"@end"<<endl;
	
	fout.close();
}
void genIosSend( const string& dir )
{
	genIosSendHeader( dir );
	genIosSendCpp( dir );
}
void genAndroidFuncDeclare( SEND_INFO* info, ofstream& fout )
{
	fout<<"	//"<<info->desc<<endl;
	fout<<"	public static void "<<info->func<<"(";
	vector< ARG_DATA* > v;
	for ( size_t j=0; j<info->arg.size(); ++j )
	{
		ARG_DATA* arg = info->arg[j];
		if ( arg->Default.empty() )
		{
			v.push_back(arg);
		}
	}

	if ( info->callback )
	{
		ARG_DATA* arg = new ARG_DATA;
		arg->name = "callback";
		arg->type = "CallbackInterface";
		arg->desc = "callback function";
		arg->null = false;
		v.push_back(arg);
	}

	if ( info->view )
	{
		ARG_DATA* arg = new ARG_DATA;
		arg->name = "ctx";
		arg->type = "Context";
		arg->desc = "waiting view's parent";
		arg->null = false;
		v.push_back(arg);
	}

	if ( info->loading )
	{
		ARG_DATA* arg = new ARG_DATA;
		arg->name = "loading";
		arg->type = "string";
		arg->desc = "waiting view's text";
		arg->null = false;
		v.push_back(arg);
	}

	if ( info->userData )
	{
		ARG_DATA* arg = new ARG_DATA;
		arg->name = "userData";
		arg->type = "string";
		arg->desc = "user custom data";
		arg->null = false;
		v.push_back(arg);
	}

	for ( size_t j=0; j<v.size(); ++j )
	{
		ARG_DATA* arg = v[j];
		fout<<endl<<"		"<<getAndroidType(arg->type)<<" "<<arg->name;
		if ( (j+1) != v.size() )
		{
			fout<<",";
		}
		fout<<" //"<<arg->desc;
	}

	if ( !v.empty() )
	{
		fout<<endl<<"		";
	}
	fout<<")";
}
void genAndroidSend( const string& dir )
{
	string file = dir + "/CommSender.java";
	ofstream fout(file.data());
	if ( !fout.is_open() )
	{
		cout<<"create file:"<<file<<" failed"<<endl;
		return ;
	}

	fout<<"//This file is generated by dic2comm. Don't mofity it."<<endl;
	fout<<"package com.xlicai.comm;"<<endl;
	fout<<endl;
	fout<<"import java.util.HashMap;"<<endl;
	fout<<"import android.content.Context;"<<endl;
	fout<<endl;

	fout<<"public class CommSender {"<<endl;
	for ( size_t i=0; i<g_vSend.size(); ++i )
	{
		genAndroidFuncDeclare( g_vSend[i], fout );
		fout<<endl;
		fout<<"	{"<<endl;
	
		SEND_INFO* info = g_vSend[i];
		bool hasAssert = false;
		for ( size_t j=0; j<info->arg.size(); ++j )
		{
			ARG_DATA* arg = info->arg[j];
			if ( !arg->Default.empty() )
			{
				continue;
			}
			if ( arg->null )
			{
				continue;
			}
			if ( "string" == arg->type )
			{
				hasAssert = true;
				fout<<"		if ( null == "<<arg->name<<" )"<<endl;
				fout<<"		{"<<endl;
				fout<<"			return ;"<<endl;
				fout<<"		}"<<endl;
			}
		}
		if ( hasAssert )
		{
			fout<<endl;
		}
		fout<<"		try{"<<endl;
		fout<<"			CommCtrl.start();"<<endl;
		if ( "auth" == info->urlType )
		{
			fout<<"			String url = CommCtrl.makeAuth(\""<<info->api<<"\");"<<endl;
		}
		else if ( "cms" == info->urlType )
		{
			fout<<"			String url = CommCtrl.makeCms(\""<<info->api<<"\");"<<endl;
		}
		else
		{
			fout<<"			String url = CommCtrl.makeApi(\""<<info->api<<"\");"<<endl;
		}
		string param = "null";
		if ( info->arg.size() > 0 )
		{
			param = "param";
			fout<<"			HashMap< String, String > param = new HashMap< String, String >();"<<endl;
		}
		if ( !info->arg.empty() )
		{
			for ( size_t j=0; j<info->arg.size(); ++j )
			{
				ARG_DATA* arg = info->arg[j];

				if ( "string" == arg->type && arg->null )
				{
					fout<<"			if ( null != "<<arg->name<<" )"<<endl;
					fout<<"				param.put( \""<<arg->name<<"\", ";
				}
				else
				{
					fout<<"			param.put( \""<<arg->name<<"\", ";
				}
				if ( !arg->Default.empty() )
				{
					fout<<"\""<<arg->Default<<"\" );"<<endl;
				}
				else
				{
					if ( "string" == arg->type )
					{
						fout<<arg->name<<" );"<<endl;
					}
					else if ( "int" == arg->type )
					{
						fout<<arg->name<<"+\"\" );"<<endl;
					}
					else if ( "float" == arg->type )
					{
						fout<<arg->name<<"+\"\" );"<<endl;
					}
					else if ( "bool" == arg->type )
					{
						fout<<arg->name<<"?\"1\":\"0\" );"<<endl;
					}
				}
			}
		}

		string callback = "null";
		if ( info->callback )
		{
			callback = "callback";
		}

		string type;
		if ( info->ui )
		{
			type = "CommConstant.CT_UI";
		}
		else if ( info->callback )
		{
			type = "CommConstant.CT_WORK";
		}
		else 
		{
			type = "CommConstant.CT_BG";
		}

		string view = "null";
		if ( info->view )
		{
			view = "ctx";
		}

		string loading = "null";
		if ( info->loading )
		{
			loading = "loading";
		}

		string error = "false";
		if ( info->error )
		{
			error = "true";
		}

		string exParam = "null";
		if ( info->timeout > 0 || info->retry > 0 || info->timer > 0 )
		{
			exParam = "exParam";
			fout<<"			HashMap< String, String > exParam = new HashMap< String, String >();"<<endl;
		}
		if ( info->timeout > 0 )
		{
			char szTemp[100] = {0};
			sprintf( szTemp, "%d", info->timeout );
			fout<<"			exParam.put( \"timeout\", \""<<szTemp<<"\" );"<<endl;
		}

		if ( info->retry > 0 )
		{
			char szTemp[100] = {0};
			sprintf( szTemp, "%d", info->retry );
			fout<<"			exParam.put( \"retry\", \""<<szTemp<<"\" );"<<endl;
		}

		if ( info->timer > 0 )
		{
			char szTemp[100] = {0};
			sprintf( szTemp, "%d", info->timer );
			fout<<"			exParam.put( \"timer\", \""<<szTemp<<"\" );"<<endl;
		}

		string userData = "null";
		if ( info->userData )
		{
			userData = "userData";
		}

		if ( 0 == info->timer )
		{
			fout<<"			CommCtrl.send(url, "<<param<<", "<<callback<<", "<<type<<", "<<view<<", "<<loading<<", "<<error<<", "<<exParam<<", \""<<info->api<<"\", "<<userData<<" );"<<endl;
		}
		else
		{
			fout<<"			CommInfo info = CommCtrl.send(url, "<<param<<", "<<callback<<", "<<type<<", "<<view<<", "<<loading<<", "<<error<<", "<<exParam<<", \""<<info->api<<"\", "<<userData<<" );"<<endl;
			fout<<"			TimerCtrl.addTimer( \"CommSender_"<<info->func<<"\", "<<info->timer<<", new TimerInterface() {"<<endl;
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
		fout<<endl;
	}

	fout<<"}"<<endl;
	
	fout.close();
}
void genSend( const string& dir )
{
	genIosSend( dir );
	genAndroidSend( dir );
}
