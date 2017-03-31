#include "Utils.h"
#include <vector>
#include <algorithm>
#include <stdio.h>
using namespace std;

vector<unsigned char> _encodeTable;
vector<unsigned char> _decodeTable(128);

bool Utils::init()
{
	for ( unsigned char i='A'; i<='Z'; ++i )
	{
		_encodeTable.push_back(i);
	}
	for ( unsigned char i='a'; i<='z'; ++i )
	{
		_encodeTable.push_back(i);
	}
	for ( unsigned char i='0'; i<='9'; ++i )
	{
		_encodeTable.push_back(i);
	}
	_encodeTable.push_back('+');
	_encodeTable.push_back('/');
	_encodeTable.push_back('=');

	for ( size_t i=0; i<_encodeTable.size(); ++i )
	{
		_decodeTable[_encodeTable[i]] = i;
	}

	return true;
}

bool Utils::base64Encode( const string& in, string& out )
{
	if ( in.empty() )
	{
		out.clear();
		return true;
	}

	int inputLen = in.size()/3 * 3;
	int left = in.size()%3;
	int i = 0;
	for ( i = 0; i < inputLen; i += 3 )
	{
		out += _encodeTable[in[i]>>2];
		out += _encodeTable[((in[i] & 0x03) << 4 ) | (in[i+1] >> 4 )];
		out += _encodeTable[((in[i+1] & 0xf) << 2 ) | ((in[i+2] & 0xc0) >> 6)];
		out += _encodeTable[in[i+2] & 0x3f];
	}

	switch( left )
	{
		case 1:
			out += _encodeTable[in[i]>>2];
			out += _encodeTable[(in[i] & 0x3) << 4];
			out += _encodeTable[_encodeTable.size()-1];
			out += _encodeTable[_encodeTable.size()-1];
			break;

		case 2:
			out += _encodeTable[in[i]>>2];
			out += _encodeTable[((in[i] & 0x3)<<4) | (in[i+1]>>4)];
			out += _encodeTable[((in[i+1] & 0xf) << 2)];
			out += _encodeTable[_encodeTable.size()-1];
			break;

		default:
			break;
	}

	return true;
}
bool Utils::base64Decode( const string& in, string& out )
{
	int decodeLen = (in.size()>>2)*3;
	for ( size_t i=in.size(); i>0; --i )
	{
		if ( in[i-1] == _encodeTable[_encodeTable.size()-1] )
		{
			--decodeLen;
		}
		else
		{
			break;
		}
	}

	int outLen = decodeLen/3 * 3;
	int left = decodeLen%3;
	int i = 0;
	int j = 0;
	for ( i = 0,j = 0; i < outLen; i += 3, j += 4 )
	{
		out += (_decodeTable[in[j]] << 2) | (_decodeTable[in[j+1]] >> 4);
		out += (_decodeTable[in[j+1]] << 4) | (_decodeTable[in[j+2]] >> 2);
		out += (_decodeTable[in[j+2]] << 6) | _decodeTable[in[j+3]];
	}

	switch( left )
	{
		case 1:
			out += (_decodeTable[in[j]] <<2) | (_decodeTable[in[j+1]] >>4 );
			break;

		case 2:
			out += (_decodeTable[in[j]] << 2) | (_decodeTable[in[j+1]] >> 4);
			out += (_decodeTable[in[j+1]] << 4) | (_decodeTable[in[j+2]] >> 2);
			break;

		default:
			break;
	}

	return true;
}

void Utils::upperCase( string& s )
{
	transform( s.begin(), s.end(), s.begin(), (int (*)(int))toupper );
}
void Utils::lowerCase( string& s )
{
	transform( s.begin(), s.end(), s.begin(), (int (*)(int))tolower );
}

void Utils::getMailTime( string& s )
{
	time_t t = time(NULL);
	tm tm;
	localtime_r( &t, &tm );

	vector<string> week;
	week.push_back( "Sun" );
	week.push_back( "Mon" );
	week.push_back( "Tue" );
	week.push_back( "Wed" );
	week.push_back( "Thu" );
	week.push_back( "Fri" );
	week.push_back( "Sat" );

	vector<string> month;
	month.push_back( "Jan" );
	month.push_back( "Feb" );
	month.push_back( "Mar" );
	month.push_back( "Apr" );
	month.push_back( "May" );
	month.push_back( "Jun" );
	month.push_back( "Jul" );
	month.push_back( "Aug" );
	month.push_back( "Sep" );
	month.push_back( "Oct" );
	month.push_back( "Nov" );
	month.push_back( "Dec" );

	char szTemp[1024] = {0};
	sprintf( szTemp, "%s, %02d %s %04d %02d:%02d:%02d %s%02ld",
			week[tm.tm_wday].data(),
			tm.tm_mday,
			month[tm.tm_mon].data(),
			tm.tm_year+1900,
			tm.tm_hour,
			tm.tm_min,
			tm.tm_sec,
			tm.tm_gmtoff>=0?"+":"-",
			tm.tm_gmtoff/3600 );
	s = szTemp;
}

vector< string > Utils::split( const string& data, const string& s )
{
	vector< string > v;
	size_t n = 0;
	size_t m = 0;
	n = data.find( s, m );
	while( string::npos != n )
	{
		string tmp = data.substr( m, n-m );
		m = n + s.size();
		n = data.find( s, m );
		v.push_back( tmp );
	}

	if ( m < data.size() )
	{
		v.push_back( data.substr( m, data.size()-m ) );
	}

	return v;
}

string Utils::replace( const string& data, const string& Old, const string& New )
{
	string ret;
	size_t n = 0;
	size_t m = 0;
	n = data.find( Old, m );
	while( string::npos != n )
	{
		string tmp = data.substr( m, n-m );
		ret += tmp;
		ret += New;
		m = n + Old.size();
		n = data.find( Old, m );
	}

	if ( m < data.size() )
	{
		ret += data.substr( m, data.size()-m );
	}

	return ret;
}
void Utils::trimAll( string& tmp )
{
	tmp = replace( tmp, "-", "_" );
	tmp = replace( tmp, ":", "_" );
	tmp = replace( tmp, " ", "_" );
	tmp = replace( tmp, "	", "_" );
	tmp = replace( tmp, "\\", "_" );
	tmp = replace( tmp, "*", "_" );
	tmp = replace( tmp, "?", "_" );
	tmp = replace( tmp, "\"", "_" );
	tmp = replace( tmp, "<", "_" );
	tmp = replace( tmp, ">", "_" );
	tmp = replace( tmp, "|", "_" );
	tmp = replace( tmp, "/", "_" );
	tmp = replace( tmp, ";", "_" );
	tmp = replace( tmp, "&", "_" );
	tmp = replace( tmp, "%", "_" );
	tmp = replace( tmp, "\r", "_" );
	tmp = replace( tmp, "\n", "_" );
}

string Utils::getDateTimeString()
{
	char szTime[100] = { 0 };
#ifdef WIN32
	SYSTEMTIME st;
	memset(&st, 0, sizeof(st));
	::GetLocalTime(&st);
	sprintf(szTime, "%04d_%02d_%02d_%02d_%02d_%02d",
		st.wYear,
		st.wMonth,
		st.wDay,
		st.wHour,
		st.wMinute,
		st.wSecond );
#else
	time_t ts = time(NULL);
	tm t;
	localtime_r( &ts, &t );
	sprintf( szTime, "%04d_%02d_%02d_%02d_%02d_%02d",
			t.tm_year+1900,
			t.tm_mon+1,
			t.tm_mday,
			t.tm_hour,
			t.tm_min,
			t.tm_sec );
#endif

	return string( szTime );
}

void Utils::mkDir(const string& dir)
{
#ifdef WIN32
	_mkdir(dir.data());
#else
	mkdir(dir.data(), 0777);
#endif
}
