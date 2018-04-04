<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no" />
<title>提示</title>
<style type="text/css">
html, body {
	margin: 0px;
	padding: 0px;
}

body {
	background-color: #efeff4;
}

.wrap {
	position: relative;
	text-align: center;
	padding-top: 60px;
	text-align: center;
}

.wrap img {
	margin: 0px auto;
}

.msg {
	text-align: center;
	padding-top: 34px;
	color: #989898;
	font-size: 20px;
}
</style>
</head>
<body>
	<div class="wrap">
		<img alt="" src="/ug/img/error_m.png">
		<div class="msg"><?php echo isset($message) ? $message : '';?></div>
	</div>
</body>
</html>