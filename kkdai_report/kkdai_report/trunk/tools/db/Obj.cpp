#include "Obj.h"
#include <assert.h>

CObj::CObj()
: _ref(1)
{
}
CObj::~CObj()
{
}

void CObj::retain()
{
	assert( _ref > 0 );
	++_ref;
}
void CObj::release()
{
	assert( _ref > 0 );
	--_ref;
	if ( 0 == _ref )
	{
		delete this;
	}
}
