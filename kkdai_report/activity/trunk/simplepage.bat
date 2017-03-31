@echo off
echo 请输入id（样子是1024/2016010230）
set /p ID=
zip.exe -qr spread.zip spread/%ID%