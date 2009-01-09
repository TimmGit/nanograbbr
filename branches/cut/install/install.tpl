<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>{{ $lang.install.title }}</title>
		<link href="css/install.css" rel="stylesheet" type="text/css" />	
	</head>
	<body>
		<div id="main">			
		{{ BEGIN first_page }}
			{{ $lang.install.first_page_text }}<br \>
			<form action="index.php" method="POST">
				<input type="hidden" name="page" value="second">
				<input type="submit" value="ru" name="lang">
				<input type="submit" value="en" name="lang">
			</form>
		{{ END first_page }}		
		{{ BEGIN second_page }}
			{{ $lang.install.second_page_text }}<br \>
			{{ $lang.install.check_config_writable }} {{ BEGIN config_writable }}<font color="Green">YES</font>{{ END config_writable }}{{ BEGIN config_unwritable }}<font color="Red">NO</font> ({{ $lang.install.config_unwritable }} chmod 777 {{ $config_file_path }}){{ END config_unwritable }}<br>
			{{ $lang.install.check_img_writable }} {{ BEGIN img_writable }}<font color="Green">YES</font>{{ END img_writable }}{{ BEGIN img_unwritable }}<font color="Red">NO</font> ({{ $lang.install.config_unwritable }} chmod 777 {{ $img_file_path }}){{ END img_unwritable }}<br>
			{{ $lang.install.check_gdlib }} {{ BEGIN have_gd }}<font color="Green">YES</font>{{ END have_gd }}{{ BEGIN havenot_gd }}<font color="Red">NO (<a href="http://php.net/gd" target="_blank">link</a>)</font>{{ END havenot_gd }}<br>
			{{ BEGIN db_settings_form }}
			<p><b style="color: red">{{ $lang.install.all_fields }}</b></p>
			<p>				
				<form action="index.php" method="POST">
				{{ $lang.install.db_host }} <input type="text" name="host" value="{{ $post.host }}"><br>
				{{ $lang.install.db_user }} <input type="text" name="user" value="{{ $post.user }}"><br>
				{{ $lang.install.db_passwd }} <input type="text" name="passwd" value="{{ $post.passwd }}"><br>
				{{ $lang.install.db_name }} <input type="text" name="name" value="{{ $post.name }}"><br>
				{{ $lang.install.db_prefix }} <input type="text" name="prefix" value="{{ $post.prefix }}"><br>
				<br>
				{{ $lang.install.site_title }} <input type="text" name="site_title" value="{{ $post.site_title }}"><br>				
				<br>
				{{ $lang.install.password }} <input type="pasword" name="password" value=""><br>
				{{ $lang.install.password2 }} <input type="pasword" name="password2" value=""><br>
				<br>
				{{ $lang.install.notification }} <input type="checkbox" name="notification" checked><br>
				{{ $lang.install.email4notification }} <input type="text" name="email" value="{{ $post.email }}"><br>
				<br>
				{{ $lang.install.check_updates }} <input type="checkbox" name="check_update" checked><br>
				<input type="hidden" name="page" value="third">
				<input type="submit" name="save" value="{{ $lang.install.next }}">
				</form>
			</p>
			{{ END db_settings_form }}
			{{ BEGIN wrong_settings }}
			<p>
				{{ $lang.install.wrong_filesystem_settings }}<br>
				<form action="index.php" method="POST">
					<input type="hidden" name="page" value="second">
					<input type="submit" value="{{ $lang.install.check }}">
				</form>				
			</p>
			{{ END wrong_settings }}
		{{ END second_page }}		
		{{ BEGIN third_page }}
			{{ BEGIN error }}
				{{ BEGIN sql_error }}
				<font color="red">{{ $lang.install.sql_connect_error }}{{ BEGIN sql_error_msg }} [{{ $sql_error }}]{{ END sql_error_msg }}</font><br>
				{{ END sql_error }}				
				{{ BEGIN password_error }}
				<font color="red">{{ $lang.install.password_error }}</font><br>
				{{ END password_error }}				
				<form action="index.php" method="POST">
					<input type="hidden" name="page" value="second">
					<input type="hidden" name="host" value="{{ $post.host }}">
					<input type="hidden" name="user" value="{{ $post.user }}">
					<input type="hidden" name="name" value="{{ $post.name }}">
					<input type="hidden" name="passwd" value="{{ $post.passwd }}">
					<input type="hidden" name="prefix" value="{{ $post.prefix }}">
					<input type="submit" value="{{ $lang.install.back }}" name="go">
				</form>				
			{{ END error }}
			{{ BEGIN install_completed }}{{ $lang.install.install_completed }}<br>{{ $lang.install.where_is_config }}<br><a href='{{ $dir }}'>{{ $lang.install.go2firstpage }}</a>{{ END install_completed }}
		{{ END third_page }}		
		</div>
	</body>
</html>