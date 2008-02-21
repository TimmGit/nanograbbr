{{ BEGIN new_comment }}<form action="{{ $site_dir }}{{ $post_type }}/{{ $post_id }}/comment/" id="new_comment" method="POST">{{ END new_comment }}
{{ BEGIN edit_comment }}<form id="form_comment">{{ END edit_comment }}
<ul class="form">

<li>{{ $lang.site.your_name }} <br />
<input type="text" name="name" value="{{ $user_name }}" id="comment_name" class="fld"></li>
{{ BEGIN new_comment }}<li>{{ $lang.site.your_email }} <br />
<input type="text" name="email" value="{{ $user_email }}" id="comment_email" class="fld"></li>
<li><img src="{{ $site_dir }}captcha/"></li>
<li>{{ $lang.site.captcha }} <br />
<input type="text" name="captcha" id="captcha_id" value="" class="fld">
<span id="captcha_error" style="display: none; color:red">{{ $lang.site.captcha_error }}</span></li>{{ END new_comment }}
<li>{{ $lang.site.your_comment }} <br />
{{ BEGIN new_comment }}<script type="text/javascript">edToolbar();</script>{{ END new_comment }}
<textarea name="comment" id="comment_id" class="fld">{{ $comment }}</textarea>
{{ BEGIN new_comment }}<script type="text/javascript">var edCanvas = document.getElementById('comment');</script>{{ END new_comment }}</li>
{{ BEGIN new_comment }}<input type="hidden" name="post_id" value="{{ $post_id }}">{{ END new_comment }}
{{ BEGIN edit_comment }}<input type="hidden" name="comment_id" value="{{ $comment_id }}">{{ END edit_comment }}
<li><input type="submit" value="{{ $lang.site.send }}" onclick="return sendComment();" class="btn"></li>
{{ BEGIN edit_comment }}<li><input type="submit" value="{{ $lang.site.delete }}" onclick="return deleteComment();" class="del"></li>{{ END edit_comment }}
</ul>
</form>