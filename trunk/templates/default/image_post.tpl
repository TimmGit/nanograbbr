<div style="text-align:center">
{{ BEGIN original }}
<a href='/{{ $big_url }}' target='_blank'><img src='{{ $url }}' alt='{{ $alt }}' {{ $size }} title="{{ $lang.site.view_full_size }}"></a>
{{ END original }}
{{ BEGIN small }}
<img src='{{ $url }}' alt='{{ $alt }}' {{ $size }} title="{{ $alt }}">
{{ END small }} </div>
{{ $text }}