<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ isset($user) ? $user->name . ' - ' : '' }}{{ isset($tag) ? '#' . $tag . ' - ' : '' }}{{ config('app.name') }}</title>
        <link>{{ config('app.url') }}</link>
        <description>{{ isset($user) ? 'Posts by ' . $user->name : (isset($tag) ? 'Posts tagged with ' . $tag : 'Latest blog posts') }}</description>
        <language>en</language>
        <pubDate>{{ $posts->first()?->created_at?->toRssString() ?? now()->toRssString() }}</pubDate>
        <lastBuildDate>{{ now()->toRssString() }}</lastBuildDate>
        <atom:link href="{{ request()->url() }}" rel="self" type="application/rss+xml"/>

        @foreach($posts as $post)
        <item>
            <title><![CDATA[{{ $post->title }}]]></title>
            <link>{{ route('posts.show', $post) }}</link>
            <guid isPermaLink="true">{{ route('posts.show', $post) }}</guid>
            <description><![CDATA[{{ $post->excerpt ?? \Str::limit(strip_tags($post->content), 200) }}]]></description>
            <content:encoded><![CDATA[{{ $post->content }}]]></content:encoded>
            <author><![CDATA[{{ $post->user->email ?? 'noreply@' . parse_url(config('app.url'), PHP_URL_HOST) }} ({{ $post->user->name }})]]></author>
            <pubDate>{{ $post->created_at->toRssString() }}</pubDate>
            @foreach($post->tags as $tag)
            <category><![CDATA[{{ $tag->name }}]]></category>
            @endforeach
        </item>
        @endforeach
    </channel>
</rss>
