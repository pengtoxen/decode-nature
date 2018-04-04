<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no" />
<title>错误</title>
<style type="text/css">
html, body {
	margin: 0px;
	padding: 0px;
}

body {
	background-color: #eae7e7;
}

.wrap {
	position: relative;
	text-align: center;
	padding-top: 60px;
	text-align: center;
}

.msg {
	position: relative;
	left: -25px;
	top: -62px;
	font-size: 20px;
	color: #989898;
}
</style>
</head>
<body>
	<div class="wrap">
		<span><img alt="" src="/ug/img/error_pc.png"><span class="msg"><?php echo isset($message) ? $message : '';?></span></span>

	</div>
</body>
</html>