#ifndef _UTILS_H_
#define _UTILS_H_
#include <string>
#include <map>
#include <vector>
#include <time.h>
#include <iostream>
#include <fstream>
#ifndef WIN32
#include <unistd.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <netdb.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <sys/socket.h>
typedef char CHAR;
typedef unsigned char BYTE;
typedef short SHORT;
typedef unsigned short WORD;
typedef int INT;
typedef int INT32;
typedef unsigned int DWORD;
typedef unsigned int UINT;
typedef unsigned int UINT32;
typedef long LONG;
typedef long INT64;
typedef unsigned long ULONG;
typedef unsigned long UINT64;
typedef float FLOAT;
typedef double DOUBLE;
typedef void* POINTER;
typedef void VOID;
#else
#include <WinSock2.h>
#include <direct.h>
#endif
using namespace std;

const int LEN_CHAR = sizeof(CHAR);
const int LEN_BYTE = sizeof(BYTE);
const int LEN_SHORT = sizeof(SHORT);
const int LEN_WORD = sizeof(WORD);
const int LEN_INT = sizeof(INT);
const int LEN_INT32 = sizeof(INT32);
const int LEN_UINT = sizeof(UINT);
const int LEN_UINT32 = sizeof(UINT32);
const int LEN_DWORD = sizeof(DWORD);
const int LEN_LONG = sizeof(LONG);
const int LEN_INT64 = sizeof(INT64);
const int LEN_ULONG = sizeof(ULONG);
const int LEN_UINT64 = sizeof(UINT64);
const int LEN_FLOAT = sizeof(FLOAT);
const int LEN_DOUBLE = sizeof(DOUBLE);
const int LEN_POINTER = sizeof(POINTER);


class Utils
{
public:
	static bool init();
	static bool base64Encode( const string& in, string& out );
	static bool base64Decode( const string& in, string& out );
	static void upperCase( string& s );
	static void lowerCase( string& s );
	static void getMailTime( string& t );
	static vector< string > split( const string& data, const string& s );
	static string replace( const string& data, const string& Old, const string& New );
	static void trimAll( string& tmp );
	static string getDateTimeString();
	static void mkDir(const string& dir);

	template< typename T >
	void swap( T& a, T& b )
	{
		T t = a;
		a = b;
		b = t;
	}

};


#endif //_UTILS_H_
