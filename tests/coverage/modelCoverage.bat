@echo off
.\..\..\vendor\bin\tester -j 8 -o console -c .\..\php.ini .\..\model\ --coverage .\modelCoverage.html --coverage-src .\..\..\app\model