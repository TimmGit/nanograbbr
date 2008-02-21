<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>{{ $site.top_title }}</title>
<link rel="STYLESHEET" href="{{ $site.dir }}templates/default/css/style.css" type="text/css">
<script language="JavaScript"> var site_path = '{{ $site.dir }}'; </script>
<script src="{{ $site.dir }}js/prototype.js" type="text/javascript"></script>
<script src="{{ $site.dir }}js/nanograbbr.js" type="text/javascript"></script>
<script src="{{ $site.dir }}js/showhide.js" type="text/javascript"></script>
<link rel="shortcut icon" href="{{ $site.dir }}templates/default/favicon.ico" />
<link href="{{ $site.dir }}rss/{{ $section }}" rel="alternate" type="application/rss+xml" title="{{ $site.title }}" />
<script src="{{ $site.dir }}js/js_quicktags.js" type="text/javascript"></script>
</head>
<body{{ BEGIN cron_off }} onload="rssUpdater();"{{ END cron_off }}>
<div id="wrap">
<div id="menu"><a href="{{ $site.dir }}texts/">{{ $lang.site.menutext }}</a> | <a href="{{ $site.dir }}images/">{{ $lang.site.menuimages }}</a> | <a href="{{ $site.dir }}videos/">{{ $lang.site.menuvideo }}</a> | <a href="{{ $site.dir }}quotes/">{{ $lang.site.menuquotes }}</a> | <a href="{{ $site.dir }}links/">{{ $lang.site.menulinks }}</a> | <a href="{{ $site.dir }}feeds/">{{ $lang.site.menurss }}</a></div>
  <div id="header">
    <h1><a href="{{ $site.dir }}">{{ $site.title }}</a></h1>
  </div>
  {{ BEGIN author_form }}{{ $author_form }}{{ END author_form }}
  {{ BEGIN post_form }}{{ $post_form }}{{ END post_form }}
  <div id="content"> {{ START $posts }}
    <div class="post">
      <h2><a href="{{ $site.dir }}{{ $posts.post_type_name }}s/{{ $posts.id }}">{{ $posts.title }}</a></h2>
      <div class="entry">{{ $posts.body }}</div>
      <div class="meta">{{ $posts.comments_link }} &nbsp;&nbsp; {{ BEGIN can_edit }}<a href="#form_top_id" id="post_edit_{{ $posts.id }}" onclick='editPost({{ $posts.id }}); return false;'>{{ $lang.site.edit }}</a>{{ END can_edit }} &nbsp;&nbsp; {{ $lang.site.date }}
        <script type="text/javascript">document.write(TimestampToHuman({{ $posts.posted_date }}));</script>
      </div>
    </div>
    {{ FINISH $posts }} 
    {{ BEGIN paginator }}   
     <div id="paginator">
		{{ BEGIN prev }}
		<a href="{{ $url }}?page={{ $prev_page }}">..{{ $prev_page }}</a>
		{{ END prev }}     
    	<!-- сюда вставляется шаблон paginator.tpl -->    	
    	{{ $paginator }} 
		{{ BEGIN next }}
		<a href="{{ $url }}?page={{ $next_page }}">{{ $next_page }}..</a>
		{{ END next }}    	
     </div>  
    {{ END paginator }}    
    {{ BEGIN one_post }}
    <div class="post">
      <h2>{{ $one_post.title }}</h2>
      <div class="entry">{{ $one_post.body }}</div>
      <div class="meta">{{ BEGIN can_edit }}<a href="#" id="post_edit_{{ $one_post.id }}" onclick='editPost({{ $one_post.id }}); return false;'>{{ $lang.site.edit }}</a>{{ END can_edit }} &nbsp;&nbsp; {{ $lang.site.date }}
        <script type="text/javascript">document.write(TimestampToHuman({{ $one_post.posted_date }}));</script>
      </div>      
    </div>
    {{ BEGIN can_have_comments }}
    	<div class="comments">
    	<div id="comment_form" style="display:{{ $show_comments_form }}">{{ $comment_form }}</div>
    	<div id="leaveacomment"><a href="#comment_form" name="comment_form" onclick="$('comment_form').show(); $('leaveacomment').hide(); Field.activate($('comment_name'));">{{ $lang.site.write_comment }}</a></div>    	
    	{{ BEGIN no_comments }}<h3>{{ $lang.site.no_coments_yet }}</h3>{{ END no_comments }}
    	<ol class="commentslist">{{ START $comments }}
		<li><div class="one_comment" id="comment_{{ $comments.id }}"><a name="comment{{ $comments.id }}"></a>
    	<strong class="author">{{ $comments.author }}</strong>{{ BEGIN can_edit }} <!-- email {{ $comments.email }}-->{{ END can_edit }} <span class="date">@ <script type="text/javascript">document.write(TimestampToHuman({{ $comments.posted_date }}));</script>{{ BEGIN can_edit }} <a href="#" onclick="getHTML('comment_form', 'comment_{{ $comments.id }}', {{ $comments.id }}); return false;">{{ $lang.site.edit }}</a>{{ END can_edit }}</span><br>
    	{{ $comments.comment }}    	
    	</div></li>
    	{{ FINISH $comments }}</ol>    	
    	</div>    
    {{ END can_have_comments }}
    {{ END one_post }}
    </div>
  <div id="footer"> <img src="{{ $site.dir }}templates/default/i/feed.png" align="absmiddle" alt="" /> RSS: <a href="{{ $site.dir }}rss/texts">{{ $lang.site.menutext }}</a> | <a href="{{ $site.dir }}rss/images">{{ $lang.site.menuimages }}</a> | <a href="{{ $site.dir }}rss/videos">{{ $lang.site.menuvideo }}</a> | <a href="{{ $site.dir }}rss/quotes">{{ $lang.site.menuquotes }}</a> | <a href="{{ $site.dir }}rss/links">{{ $lang.site.menulinks }}</a> | <a href="{{ $site.dir }}rss/feeds">{{ $lang.site.menurss }}</a> | <strong><a href="{{ $site.dir }}rss">{{ $lang.site.globalrss }}</a></strong><br /><br />

&copy; 2008, {{ $site.title }}. Powered by <a href="http://nanograbbr.com">NanoGrabbr</a>. {{ BEGIN login }}<a href="#" onClick="showHideObj('login_form'); $('password').focus(); return false;">{{ $lang.site.log_in }}</a>.{{ END login }}{{ BEGIN logout }}<a href="#" onClick="logout(); return false;">{{ $lang.site.log_out }}</a>.{{ END logout }}
    <div id="login_form" style="display:none"> {{ BEGIN login_form }}
      <input type="password" id="password" class="fld" onFocus="this.style.background='FFFFFF';" onBlur="this.style.background='F9F9F9';">
      <input type="submit" value="{{ $lang.site.log_in }}" class="btn" onClick="login('{{ $needCheck }}'); return false;">
      {{ END login_form }} </div>
  </div>
</div>
</body>
</html>
