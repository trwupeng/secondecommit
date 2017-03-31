#ifndef _OBJ_H_
#define _OBJ_H_

class CObj
{
public:
	CObj();

protected:
	virtual ~CObj();

public:
	void retain();
	void release();

private:
	int _ref;
};


#endif //_OBJ_H_
