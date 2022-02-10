string(1) "1"
<pre>An Error occurred while handling another error:
yii\web\HeadersAlreadySentException: Headers already sent in C:\OpenServer\domains\localhost\docs\controllers\ReportController.php on line 133. in C:\OpenServer\domains\localhost\docs\vendor\yiisoft\yii2\web\Response.php:366
Stack trace:
#0 C:\OpenServer\domains\localhost\docs\vendor\yiisoft\yii2\web\Response.php(339): yii\web\Response-&gt;sendHeaders()
#1 C:\OpenServer\domains\localhost\docs\vendor\yiisoft\yii2\web\ErrorHandler.php(136): yii\web\Response-&gt;send()
#2 C:\OpenServer\domains\localhost\docs\vendor\yiisoft\yii2\base\ErrorHandler.php(135): yii\web\ErrorHandler-&gt;renderException(Object(yii\base\ErrorException))
#3 [internal function]: yii\base\ErrorHandler-&gt;handleException(Object(yii\base\ErrorException))
#4 {main}
Previous exception:
yii\base\ErrorException: Cannot modify header information - headers already sent by (output started at C:\OpenServer\domains\localhost\docs\controllers\ReportController.php:133) in C:\OpenServer\domains\localhost\docs\controllers\ReportController.php:134
Stack trace:
#0 [internal function]: yii\base\ErrorHandler-&gt;handleError(2, &#039;Cannot modify h...&#039;, &#039;C:\\OpenServer\\d...&#039;, 134, Array)
#1 C:\OpenServer\domains\localhost\docs\controllers\ReportController.php(134): header(&#039;Content-Disposi...&#039;)
#2 [internal function]: app\controllers\ReportController-&gt;actionGetFullReport()
#3 C:\OpenServer\domains\localhost\docs\vendor\yiisoft\yii2\base\InlineAction.php(57): call_user_func_array(Array, Array)
#4 C:\OpenServer\domains\localhost\docs\vendor\yiisoft\yii2\base\Controller.php(180): yii\base\InlineAction-&gt;runWithParams(Array)
#5 C:\OpenServer\domains\localhost\docs\vendor\yiisoft\yii2\base\Module.php(528): yii\base\Controller-&gt;runAction(&#039;get-full-report&#039;, Array)
#6 C:\OpenServer\domains\localhost\docs\vendor\yiisoft\yii2\web\Application.php(103): yii\base\Module-&gt;runAction(&#039;report/get-full...&#039;, Array)
#7 C:\OpenServer\domains\localhost\docs\vendor\yiisoft\yii2\base\Application.php(386): yii\web\Application-&gt;handleRequest(Object(yii\web\Request))
#8 C:\OpenServer\domains\localhost\docs\web\index.php(13): yii\base\Application-&gt;run()
#9 {main}</pre>