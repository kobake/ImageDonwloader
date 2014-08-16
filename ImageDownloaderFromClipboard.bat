%~d0:
cd %~d0%~p0

: クリップボードからURL取得
ClipboardText > url.tmp
set /p url= < url.tmp
del url.tmp
if "%url%" == "" goto error

: ImageDownloader起動
php ImageDownloader.php "%url%"
pause
exit

:error
@echo "URL取得失敗"
pause
exit
