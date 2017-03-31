update db_kkrpt.tb_menu set statusCode=1 where id=1 
or id=2
or ( id >=8 and id <= 21 )
	or ( id >= 22 and id <= 38 )
	or ( id >= 39 and id <= 59 )
	or id = 61 or id = 65
	or id = 1011
	or ( id >= 1091 and id <= 1166 );
