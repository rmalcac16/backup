<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
	<url>
		<loc>https://www.animeon.moe/</loc>
		<priority>1.0</priority>
		<changefreq>hourly</changefreq>
	</url>
@foreach($episodios as $episodio)
	<url>
		<loc>{{'https://www.animeon.moe/'.$episodio->anime->slug.'/capitulo-'.$episodio->number }}</loc>
		@if($episodio->anime->status === 1)
			<priority>0.8</priority>
			<changefreq>weekly</changefreq>
		@else	
			<priority>0.6</priority>
			<changefreq>yearly</changefreq>
		@endif	
	</url>
@endforeach	
@foreach($animes as $anime)
	<url>
		<loc>{{'https://www.animeon.moe/anime/'.$anime->slug }}</loc>
		@if($anime->status === 1)
			<priority>0.8</priority>
			<changefreq>weekly</changefreq>
		@else	
			<priority>0.6</priority>
			<changefreq>yearly</changefreq>
		@endif	
	</url>
@endforeach
</urlset>