@echo off
"C:\wamp64\bin\php\php7.4.33\php.exe" "C:\wamp64\www\diagoma\index.php" cron 79f7629a88d7bcba31d8a98b4c9cd034a9f7a42f >> "C:\wamp64\www\diagoma\backup\cron_log.txt" 2>&1
exit /b %ERRORLEVEL%
