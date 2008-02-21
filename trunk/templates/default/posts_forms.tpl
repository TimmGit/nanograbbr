<div id="post_form"> <a name="form_top" id="form_top_id" /></a>
  <!-- POST TEXT FORM -->
  <form id="form_post_text" style="display:none">
    <ul class="form">
      <li class="formtitle"><strong>{{ $lang.site.text }}</strong>. <span id="text_create">{{ $lang.site.create_post_form_title }}</span>  <span id="text_edit" style="display:none">{{ $lang.site.edit_post_form_title }}</span> (<a href='#' onclick="$('form_post_text').hide();">{{ $lang.site.cancel }}</a>)</li>
      <li>{{ $lang.site.title }}<br />
        <input type="text" id="text_title_id" name="title" value="{{ $post.title }}" class="fld">
      </li>
      <li>{{ $lang.site.text }}<br />
        <script type="text/javascript">edToolbar('text_text_id');</script>
		<textarea id="text_text_id" name="text" class="fld">{{ $post.text }}</textarea>
      </li>
      <li>{{ $lang.site.allow_comment }}
        <input type="checkbox" id="text_allow_comment_id" name="allow_comment">
      </li>
      <li class="delbtn">
        <input type="button" id="text_delete_btn" value="{{ $lang.site.delete }}" onclick="if (confirm('{{ $lang.site.confirm_delete }}')) deletePost('text');" style="display:none;" class="del">
      </li>
      <li>
        <input type="button" value="{{ $lang.site.save }}" onclick="savePost('text');" class="btn">
      </li>
    </ul>
    <input type="hidden" name="post_type" id="text_post_type_id" value="text">
    <input type="hidden" name="post_id" id="text_post_id_id" value="">
  </form>
  <!-- /POST TEXT FORM -->
  <!-- POST IMAGE FORM -->
  <form id="form_post_image" style="display:none" method="POST" enctype="multipart/form-data" action="{{ $site.dir }}ajax/post/save">
    <ul class="form">
      <li class="formtitle"><strong>{{ $lang.site.image }}</strong>. <span id="image_create">{{ $lang.site.create_post_form_title }}</span> <span id="image_edit" style="display:none">{{ $lang.site.edit_post_form_title }}</span> (<a href='#' onclick="$('form_post_image').hide();">{{ $lang.site.cancel }}</a>)</li>
      <li>{{ $lang.site.title }}<br />
        <input type="text" id="image_title_id" name="title" value="{{ $post.title }}" class="fld">
      </li>
      <li>{{ $lang.site.image_url }}<br />
        <input type="text" id="image_url_id" name="url" value="{{ $post.url }}" class="fld">
      </li>
      <li>{{ $lang.site.save_image_url }}
        <input type="checkbox" id="image_save_url_id" name="save_url">
      </li>
      <li>{{ $lang.site.image_file }}<br />
        <input type="file" id="image_img_file_id" name="img_file" value="" size="43">
      </li>
      <li>{{ $lang.site.description }}<br />
        <textarea id="image_text_id" name="text" class="fld">{{ $post.text }}</textarea>
      </li>
      <li>{{ $lang.site.allow_comment }}
        <input type="checkbox" id="image_allow_comment_id" name="allow_comment">
      </li>
      <li class="delbtn">
        <input type="button" id="image_delete_btn" value="{{ $lang.site.delete }}" onclick="if (confirm('{{ $lang.site.confirm_delete }}')) deletePost('image');" style="display:none;" class="del">
      </li>
      <li>
        <input type="submit" value="{{ $lang.site.save }}" class="btn">
      </li>
    </ul>
    <input type="hidden" name="post_type" id="image_post_type_id" value="image">
    <input type="hidden" name="post_id" id="image_post_id_id" value="">
  </form>
  <!-- /POST IMAGE FORM -->
  <a name="form_top" id="form_top_id" /></a>
  <!-- POST QUOTE FORM -->
  <form id="form_post_quote" style="display: none">
    <ul class="form">
      <li class="formtitle"><strong>{{ $lang.site.quote }}</strong>. <span id="quote_create">{{ $lang.site.create_post_form_title }}</span> <span id="quote_edit" style="display:none">{{ $lang.site.edit_post_form_title }}</span> (<a href='#' onclick="$('form_post_quote').hide();">{{ $lang.site.cancel }}</a>)</li>
      <li>{{ $lang.site.author }}<br />
        <input type="text" id="quote_title_id" name="title" value="{{ $post.title }}" class="fld">
      </li>
      <li>{{ $lang.site.quote }}<br />
        <script type="text/javascript">edToolbar('quote_text_id');</script>
		<textarea id="quote_text_id" name="text" class="fld">{{ $post.text }}</textarea>
      </li>
      <li>{{ $lang.site.url }}<br />
        <input type="text" id="quote_url_id" name="url" value="{{ $post.url }}" class="fld">
      </li>
      <li>{{ $lang.site.allow_comment }}
        <input type="checkbox" id="quote_allow_comment_id" name="allow_comment">
      </li>
      <li class="delbtn">
        <input type="button" id="quote_delete_btn" value="{{ $lang.site.delete }}" onclick="if (confirm('{{ $lang.site.confirm_delete }}')) deletePost('quote');" style="display:none;" class="del">
      </li>
      <li>
        <input type="button" value="{{ $lang.site.save }}" onclick="savePost('quote');" class="btn">
      </li>
    </ul>
    <input type="hidden" name="post_type" id="quote_post_type_id" value="quote">
    <input type="hidden" name="post_id" id="quote_post_id_id" value="">
  </form>
  <!-- /POST QUOTE FORM -->
  <a name="form_top" id="form_top_id" /></a>
  <!-- POST LINK FORM -->
  <form id="form_post_link" style="display:none">
    <ul class="form">
      <li class="formtitle"><strong>{{ $lang.site.link }}</strong>. <span id="link_create">{{ $lang.site.create_post_form_title }}</span> <span id="link_edit" style="display:none">{{ $lang.site.edit_post_form_title }}</span> (<a href='#' onclick="$('form_post_link').hide();">{{ $lang.site.cancel }}</a>)</li>
      <li>{{ $lang.site.title }}<br />
        <input type="text" id="link_title_id" name="title" value="{{ $post.title }}" class="fld">
      </li>
      <li>{{ $lang.site.url }}<br />
        <input type="text" id="link_url_id" name="url" value="{{ $post.url }}" class="fld">
      </li>
      <li>{{ $lang.site.text }}<br />
        <script type="text/javascript">edToolbar('link_text_id');</script>
		<textarea id="link_text_id" name="text" class="fld">{{ $post.text }}</textarea>		
      </li>
      <li>{{ $lang.site.allow_comment }}
        <input type="checkbox" id="link_allow_comment_id" name="allow_comment">
      </li>
      <li class="delbtn">
        <input type="button" id="link_delete_btn" value="{{ $lang.site.delete }}" onclick="if (confirm('{{ $lang.site.confirm_delete }}')) deletePost('link');" style="display:none;" class="del">
      </li>
      <li>
        <input type="button" value="{{ $lang.site.save }}" onclick="savePost('link');" class="btn">
      </li>
    </ul>
    <input type="hidden" name="post_type" id="link_post_type_id" value="link">
    <input type="hidden" name="post_id" id="link_post_id_id" value="">
  </form>
  <!-- /POST LINK FORM -->
  <a name="form_top" id="form_top_id" /></a>
  <!-- POST VIDEO FORM -->
  <form id="form_post_video" style="display: none">
    <ul class="form">
      <li class="formtitle"><strong>{{ $lang.site.video }}</strong>. <span id="video_create">{{ $lang.site.create_post_form_title }}</span> <span id="video_edit" style="display:none">{{ $lang.site.edit_post_form_title }}</span> (<a href='#' onclick="$('form_post_video').hide();">{{ $lang.site.cancel }}</a>)</li>
      <li>{{ $lang.site.title }}<br />
        <input type="text" id="video_title_id" name="title" value="{{ $post.title }}" class="fld">
      </li>
      <li>{{ $lang.site.embed }}<br />
		<textarea id="video_text_id" name="text" class="fld">{{ $post.text }}</textarea>
      </li>     
      <li>{{ $lang.site.allow_comment }}
        <input type="checkbox" id="video_allow_comment_id" name="allow_comment">
      </li>
      <li class="delbtn">
        <input type="button" id="video_delete_btn" value="{{ $lang.site.delete }}" onclick="if (confirm('{{ $lang.site.confirm_delete }}')) deletePost('video');" style="display:none;" class="del">
      </li>
      <li>
        <input type="button" value="{{ $lang.site.save }}" onclick="savePost('video');" class="btn">
      </li>
    </ul>
    <input type="hidden" name="post_type" id="video_post_type_id" value="video">
    <input type="hidden" name="post_id" id="video_post_id_id" value="">
  </form>
  <!-- /POST VIDEO FORM -->
  <a name="form_top" id="form_top_id" /></a>
  <!-- POST RSS FORM -->
  <form id="form_post_rss" style="display: none">
    <ul class="form">
      <li class="formtitle"><strong>{{ $lang.site.rss }}</strong>. (<a href='#' onclick="$('form_post_rss').hide();">{{ $lang.site.cancel }}</a>)</li>
      <b>{{ $lang.site.rss_feed_edit }}</b>
      <li>{{ $lang.site.rss }}<br />
      	<select id="my_rss" class="fld">      		
      		{{ START $my_rss }}
      		<option value="{{ $my_rss.id }}">{{ $my_rss.rss_url }}
      		{{ FINISH $my_rss }}      		
      	</select>
      	<input type="button" value="{{ $lang.site.select }}" onclick="getRss();" class="btn">
      </li>      
      <li>{{ $lang.site.rss_url }}<br />
        <input type="text" id="rss_url_id" name="url" value="{{ $post.url }}" class="fld">
      </li>
      <li>{{ $lang.site.rss_update_period_min }}<br />
        <input type="text" id="rss_period_id" name="period" value="{{ $post.period }}" class="fld">
      </li>
      <li class="delbtn">
        <input type="button" id="rss_delete_btn" value="{{ $lang.site.delete }}" onclick="if (confirm('{{ $lang.site.confirm_delete }}')) deletePost('rss');" style="display:none;" class="del">
      </li>
      <li>
        <input type="button" value="{{ $lang.site.save }}" onclick="savePost('rss');" class="btn">
      </li>
    </ul>
    <input type="hidden" name="post_type" id="rss_post_type_id" value="rss">
    <input type="hidden" name="post_id" id="rss_post_id_id" value="">
  </form>
  <!-- /POST RSS FORM -->
  <a name="form_top" id="form_top_id" /></a>
  <!-- POST FEED FORM -->
  <form id="form_post_feed" style="display: none">
    <ul class="form">
      <li class="formtitle"><strong>{{ $lang.site.rss_post }}</strong>. <span id="feed_create">{{ $lang.site.create_post_form_title }}</span> <span id="feed_edit" style="display:none">{{ $lang.site.edit_post_form_title }}</span> (<a href='#' onclick="$('form_post_feed').hide();">{{ $lang.site.cancel }}</a>)</li>
      <li>{{ $lang.site.title }}<br />
        <input type="text" id="feed_title_id" name="title" value="{{ $post.title }}" class="fld">
      </li>
      <li>{{ $lang.site.text }}<br />
		<textarea id="feed_text_id" name="text" class="fld">{{ $post.text }}</textarea>
      </li>     
      <li>{{ $lang.site.url }}<br />
        <input type="text" id="feed_url_id" name="url" value="{{ $post.url }}" class="fld">
      </li>      
      <li>{{ $lang.site.allow_comment }}
        <input type="checkbox" id="feed_allow_comment_id" name="allow_comment">
      </li>
      <li class="delbtn">
        <input type="button" id="feed_delete_btn" value="{{ $lang.site.delete }}" onclick="if (confirm('{{ $lang.site.confirm_delete }}')) deletePost('feed');" style="display:none;" class="del">
      </li>
      <li>
        <input type="button" value="{{ $lang.site.save }}" onclick="savePost('feed');" class="btn">
      </li>
    </ul>
    <input type="hidden" name="post_type" id="feed_post_type_id" value="feed">
    <input type="hidden" name="post_id" id="feed_post_id_id" value="">
  </form>
  <!-- /POST RSS FORM -->
</div>
