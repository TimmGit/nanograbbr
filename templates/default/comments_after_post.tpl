<!-- Этот блок выводится в ленте постов после поста, в котором разрешено комментировать -->
<!-- вставляется в index.tpl вместо $posts.comments_link -->
<a href="{{ $site.dir }}{{ $post_type }}s/{{ $post_id }}">{{ BEGIN no_comments }}{{ $lang.site.no_comments }}{{ END no_comments }}{{ BEGIN have_comments }}{{ $comments_count }} {{ BEGIN 1comment }}{{ $lang.site.1comment }}{{ END 1comment }}{{ BEGIN 2comment }}{{ $lang.site.2comment }}{{ END 2comment }}{{ BEGIN 5comment }}{{ $lang.site.5comment }}{{ END 5comment }}{{ END have_comments }}</a>