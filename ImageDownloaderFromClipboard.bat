%~d0:
cd %~d0%~p0

: �N���b�v�{�[�h����URL�擾
ClipboardText > url.tmp
set /p url= < url.tmp
del url.tmp
if "%url%" == "" goto error

: ImageDownloader�N��
php ImageDownloader.php "%url%"
pause
exit

:error
@echo "URL�擾���s"
pause
exit
