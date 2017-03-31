dic2comm.exe ..\dic\send.xml .
move /Y enums.java E:\work\front_android\trunk\src\MiaoJi\src\com\xiaoxialicai\comm
move /Y CommSender.java E:\work\front_android\trunk\src\MiaoJi\src\com\xiaoxialicai\comm
del enums.h
del CommSender.mm
del CommSender.h
pause